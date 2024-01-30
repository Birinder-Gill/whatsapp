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
        return;
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
            return $this->createMessages([
                'role' => 'user',
                'content' => $message,
            ]);
        }
    }
    function getAssistantResponse()
    {
        $response = $this->client->threads()->messages()->list($this->threadId, [
            'limit' => 1,
        ]);
        return $response->toArray();
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
        return $this->getAssistantResponse();
    }
    function runRetrievePolling($runId)
    {
        OpenAiLock::updateOrCreate(['threadId' => $this->threadId]);
        while (true) {
            sleep(.5);
            $response = $this->client->threads()->runs()->retrieve(
                threadId: $this->threadId,
                runId: $runId,
            );
            if ($response->status == 'completed') break;
        }

        OpenAiLock::where('threadId', $this->threadId)->delete();
        $this->checkMessageTrack();
    }
    function checkMessageTrack()
    {
        $messages = OpenAiMessageTrack::where('threadId', $this->threadId)->get();
        if ($messages->count()) {
            $mappedMessages = $messages->map(function ($mapMessage) {
                return [
                    'role' => 'user',
                    'content' => $mapMessage->message
                ];
            });
            OpenAiMessageTrack::destroy($messages->pluck('id'));
            return $this->createMessages($mappedMessages);
        }
    }
    function createMessages(array $messages)
    {
        $response = $this->client->threads()->messages()->create($this->threadId, $messages);
        return $this->createRun();
    }
    function tryToRetrieve($runId)
    {
    }
}
