<?php

namespace App\Services;

use App\Models\OpenAiLock;
use App\Models\OpenAiMessageTrack;
use App\Models\OpenAiThread;
use Illuminate\Support\Facades\Http;

class V2Service
{
    protected $threadId;
    protected $apiKey;
    protected $assId;

    public function __construct()
    {
        $this->apiKey = config('app.openAiKey');
    }

    function initialise(string $from): bool
    {
        try {
            $query = OpenAiThread::where('from', $from);
            if ($query->exists()) {
                $this->threadId = $query->first()->threadId;
            } else {
                $response = $this->createThread();
                $this->threadId = $response['id'];
                OpenAiThread::create([
                    'from' => $from,
                    'threadId' => $this->threadId
                ]);
            }
            return true;
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    function queryDetection($message, $asstID = null): string
    {
        $toSend = $this->createAndRun($message, $asstID);
        return explode("-", $toSend)[0];
    }

    function getThreadId(): string
    {
        return $this->threadId;
    }

    function createAndRun($message, $assId = null)
    {
        if ($assId) {
            $this->assId = $assId;
        }
        $lock = OpenAiLock::where('threadId', $this->threadId);
        if ($lock->exists()) {
            OpenAiMessageTrack::create([
                'threadId' => $this->threadId,
                'message' => $message
            ]);
        } else {
            return $this->createMessages([
                ['role' => 'user', 'content' => $message],
            ]);
        }
    }

    function getAssistantResponse()
    {
        $response = $this->listMessages($this->threadId, 1);
        return $response['data'][0]['content'][0]['text']['value'];
    }

    function createRun()
    {
        $run = $this->createRunRequest($this->threadId, [
            'assistant_id' => $this->assId ?? config('app.assistantId'),
        ]);
        logMe("RUN",$run);
        $this->runRetrievePolling($run['id']);
        return $this->getAssistantResponse();
    }

    function runRetrievePolling($runId)
    {
        OpenAiLock::updateOrCreate(['threadId' => $this->threadId]);
        while (true) {
            sleep(.5);
            $response = $this->retrieveRun($this->threadId, $runId);
            if ($response['status'] == 'completed') break;
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
            return $this->createMessages($mappedMessages->toArray());
        }
    }

    function createMessages(array $messages)
    {
        $this->sendMessageRequest($this->threadId, $messages);
        return $this->createRun();
    }

    private function createThread()
    {
        $response = Http::withToken($this->apiKey)->post('https://api.openai.com/v1/threads');
        return $response->json();
    }

    private function listMessages($threadId, $limit)
    {
        $response = Http::withToken($this->apiKey)->get("https://api.openai.com/v1/threads/{$threadId}/messages", [
            'limit' => $limit,
        ]);
        return $response->json();
    }

    private function createRunRequest($threadId, $parameters)
    {
        $response = Http::withToken($this->apiKey)->withHeaders(["OpenAI-Beta"=> "assistants=v2"])->post("https://api.openai.com/v1/threads/{$threadId}/runs", $parameters);
        return $response->json();
    }

    private function retrieveRun($threadId, $runId)
    {
        $response = Http::withToken($this->apiKey)->get("https://api.openai.com/v1/threads/{$threadId}/runs/{$runId}");
        return $response->json();
    }

    private function sendMessageRequest($threadId, $messages)
    {
        Http::withToken($this->apiKey)->post("https://api.openai.com/v1/threads/{$threadId}/messages", [
            'messages' => $messages
        ]);
    }

    function tryToRetrieve($runId)
    {
        try {
            $response = $this->retrieveRun($this->threadId, $runId);
            return $response;
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }
}
