<?php

namespace App\Http\Controllers;

use App\Enums\GeneralQuery;
use App\Jobs\SendFollowUpsJob;
use App\Models\LogKeeper;
use App\Models\WhatsAppLead;
use App\Services\MessageAnalysisService;
use App\Services\MessageSendingService;
use App\Services\OpenAiAnalysisService;
use Illuminate\Http\Request;

class WhatsAppMessageController extends Controller
{
    protected $to = '917009154010@c.us';
    protected MessageAnalysisService $maService;
    protected MessageSendingService $msService;
    protected OpenAiAnalysisService $aiService;

    public function __construct(MessageAnalysisService $maService, MessageSendingService $msService, OpenAiAnalysisService $aiService)
    {
        $this->maService = $maService;
        $this->msService = $msService;
        $this->aiService = $aiService;
    }

    function sendMessage(Request $request)
    {

        SendFollowUpsJob::dispatch($this->msService);
        // dd($this->msService->getReq()->all());
        $body = "prod sirra bc ";
        $response = $this->msService->sendTestMessage($body);
        return json_decode($response->getBody())->data->status;
    }

    function messageReceived(Request $request)
    {
        try {
            $useOpenAi = config('app.useOpenAi');
            $data = request()->json()->all()['data']['message']['_data'];
            $message = $data['body'];
            $personName = $data['notifyName'];
            $from = $data['from'];
            $messageNumber = detectManualMessage($from, $message);
            $hash = $data['id']['_serialized'];
            $fromMe = $data['id']['fromMe'];

            $logArray = [
                'from' => $from,
                'displayName' => $personName,
                'to' => $data['to'],
                'counter' => $messageNumber + 1,
                'messageText' => $message,
                'messageId' => $data['id']['id'],
                'messageHash' => $hash,
                'threadId' => $this->aiService->getThreadId()
            ];


            if ($messageNumber > -1) {
                incrementCounter($logArray);
                if ($messageNumber === 0) {
                    createNewLead($from);
                    $this->msService->deleteMessage($hash);
                    $this->msService->sendFirstMessage($personName);
                } else {
                    if ($messageNumber === 1) {
                        createHotLead($from);
                    }
                    if ($useOpenAi) {
                        $assistant = $this->aiService->createAndRun($message);
                        $this->msService->sendOpenAiResponse($assistant);
                    } else {
                        $query = $this->aiService->queryDetection($message);
                        $this->msService->giveQueryResponse($query);
                    }
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    function runATest($from)
    {
        $message = "https://api.whatsapp.com/send?phone=" . substr($from, 2, 10) . "&text=Hello, How may I help you";
        $this->msService->sendTestMessage($message);
    }



















    function sendMediaApi(Request $request)
    {
        $mediaUrl = 'https://drive.google.com/file/d/1sP3zfH4nIznkGX65Jtbh6WhQd1X0WWPS/view?usp=sharing';
        // $mediaUrl = 'https://images.unsplash.com/photo-1682686578707-140b042e8f19?q=80&w=1375&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D';
        $to =  '917009154010@c.us';
        $response = $this->msService->sendTestMedia($mediaUrl);
        return $response->getBody();
    }
}
