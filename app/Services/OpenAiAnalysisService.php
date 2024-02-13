<?php


namespace App\Services;

use App\Enums\GeneralQuery;
use App\Models\OpenAiLock;
use App\Models\OpenAiMessageTrack;
use App\Models\OpenAiRun;
use App\Models\OpenAiThread;
use OpenAI;

class OpenAiAnalysisService
{
    protected $threadId;
    protected  $client;
    public function __construct()
    {
        $openAiKey = config('app.openAiKey');
        $this->client = OpenAI::client($openAiKey);

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
        }
    }

    function queryDetection($message) : GeneralQuery {
        $toSend= $this->createAndRun($message);
        $toSend = explode("-",$toSend)[0];
        return match ($toSend) {
            'PRICE' => GeneralQuery::PRICE,
            'ADDRESS' => GeneralQuery::ADDRESS,
            'MORE_DETAILS' => GeneralQuery::MORE_DETAILS,
            'USE_CASE' => GeneralQuery::USE_CASE,
            'DELIVERY_WAY' => GeneralQuery::DELIVERY_WAY,
            'DELIVERY_TIME' => GeneralQuery::DELIVERY_TIME,
            'PINCODE_AVAILABILITY' => GeneralQuery::PINCODE_AVAILABILITY,
            'FOLLOW_UP_GIVEN_BY_USER' => GeneralQuery::FOLLOW_UP_GIVEN_BY_USER,
            'HIGH_AS_COMPARED' => GeneralQuery::HIGH_AS_COMPARED,
            'HIGH_IN_GENERAL' => GeneralQuery::HIGH_IN_GENERAL,
            'WHOLESALE' => GeneralQuery::WHOLESALE,
            'OK' => GeneralQuery::OK,
            default => GeneralQuery::UNKNOWN,
        };
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
        $result = $response->toArray();
        return $result['data'][0]['content'][0]['text']['value'];
    }
    function createRun()
    {
        $run =  $this->client->threads()->runs()->create(
            threadId: $this->threadId,
            parameters: [
                'assistant_id' => config('app.assistantId'),
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
