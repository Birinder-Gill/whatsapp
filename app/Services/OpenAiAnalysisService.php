<?php


namespace App\Services;

use App\Models\OpenAiThread;
use OpenAI;
use OpenAI\Client;

class OpenAiAnalysisService
{
    protected $openAiKey = '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';
    protected $threadId;
    protected Client $client;
    public function __construct()
    {
        $this->client = OpenAI::client($this->openAiKey);

        // try {
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
        // } catch (\Throwable $th) {
        // }
    }

    function createARun($message)
    {
        $response = $this->client->threads()->messages()->create($this->threadId, [
            'role' => 'user',
            'content' => $message,
        ]);

        $run = $this->client->threads()->runs()->create(
            threadId: $this->threadId,
            parameters: [
                'assistant_id' => 'asst_ljqNMDA6uAGjRMatHUyBnR07',
            ],
        );

        $response = $this->client->threads()->runs()->retrieve(
            threadId: $this->threadId,
            runId: $run->id,
        );

        // dd($run->toArray());
    }

    function tryToRetrieve($runId)
    {
    }
}
