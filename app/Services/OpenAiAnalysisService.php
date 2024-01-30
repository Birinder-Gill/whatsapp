<?php


namespace App\Services;

use App\Models\OpenAiLock;
use App\Models\OpenAiMessageTrack;
use App\Models\OpenAiRun;
use App\Models\OpenAiThread;
use OpenAI;

class OpenAiAnalysisService
{
    protected $openAiKey = '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';
    protected $threadId;
    protected  $client;
    public function __construct()
    {
        $this->client = OpenAI::client($this->openAiKey);

        try {
            $from = request()->json()->all()['data']['message']['_data']['from'];
            $query = OpenAiThread::where('from', $from);
            if ($query->exists()) {
                $this->threadId = $query->first()->threadId;
            } else {
                $response = $this->client->threads()->create([]);
                $this->threadId = $response->id;
                OpenAiThread::create([
                    'from' => $from,
                    'threadId' => $this->threadId
                ]);
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    function getThreadId(): string
    {
        return $this->threadId;
    }

    function createAndRun($message)
    {
        $lock = OpenAiLock::where('threadId', $this->threadId);
        if ($lock->exists()) {
            OpenAiMessageTrack::create(
                [
                    'threadId' => $this->threadId,
                    'message' => $message
                ]
            );
        } else {
            $this->createMessages([
                'role' => 'user',
                'content' => $message,
            ]);
        }

        //TODO: WHILE THE THREAD POLLING IS GOING ON,
        //       WE SAVE THE NEW MESSAGES IN DB AND CREATE THREADS FOR THOSE MESSAGES ALONG THE WAY
        //      WE DO THIS USING A FLAG IN DB. AND IF WE START THE CHAT i.e WE SEND A MESSAGE MANUALLY,
        //      OPENAI SHOULD STOP MAKING RUNS AND FLAG THE CONVERSATION COMPLETE.
        //      TODO: if threadId in lock exists add message to tracker.



        // OpenAiRun::create(
        //     [
        //         'threadId' => $this->threadId,
        //         'runId'=>  $run->id
        //     ]
        // );
        // sleep(2);





        // return $response->toArray();
        // dd($run->toArray());
    }
    function getAssistantResponse()
    {
        $response = $this->client->threads()->messages()->list($this->threadId, [
            'limit' => 1,
        ]);
        return $response;
    }
    function createRun()
    {
        $run =  $this->client->threads()->runs()->create(
            threadId: $this->threadId,
            parameters: [
                'assistant_id' => 'asst_ljqNMDA6uAGjRMatHUyBnR07',
            ],
        );
        $this->runRetrievePolling($run->id);
        $this->getAssistantResponse();
    }
    function runRetrievePolling($runId)
    {
        OpenAiLock::updateOrCreate(['threadId' => $this->threadId]);
        $response = $this->client->threads()->runs()->retrieve(
            threadId: $this->threadId,
            runId: $runId,
        );
        OpenAiLock::where('threadId', $this->threadId)->delete();
        $this->checkMessageTrack();
    }
    function checkMessageTrack()
    {
        $messages = OpenAiMessageTrack::where('threadId', $this->threadId)->get();
        if($messages->count()){
            $mappedMessages = $messages;
            $this->createMessages($mappedMessages);
        }
    }
    function createMessages(array $messages)
    {
        $response = $this->client->threads()->messages()->create($this->threadId, $messages);
        $run = $this->createRun();
    }
    function tryToRetrieve($runId)
    {
    }
}
