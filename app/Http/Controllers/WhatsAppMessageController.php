<?php

namespace App\Http\Controllers;

use App\Jobs\SendFollowUpsJob;
use App\Models\KillSwitch;
use App\Services\MessageSendingService;
use App\Services\OpenAiAnalysisService;
use Illuminate\Http\Request;

class WhatsAppMessageController extends Controller
{
    protected $to = '917009154010@c.us';
    protected MessageSendingService $msService;
    protected OpenAiAnalysisService $aiService;

    public function __construct(MessageSendingService $msService, OpenAiAnalysisService $aiService)
    {
        $this->msService = $msService;
        $this->aiService = $aiService;
    }
    function orderReceived(Request $request)
    {
        $phone =  $order['phone'] ?? $order['customer']['phone'] ?? $order['billing_address']['phone'] ?? $order['shipping_address']['phone'] ?? null;
        if ($phone) {
            updateStatus(formatPhoneNumber($phone), 'yes');
        }
    }

    function sendMessage(Request $request)
    {
        // SendFollowUpsJob::dispatch($this->msService);
        // // dd($this->msService->getReq()->all());
        $body = "prod sirra \n\n\nbc ";
        $response = $this->msService->sendTestMessage($body);
        return json_decode($response->getBody());
    }

    function mickeyCalling(Request $request)
    {
        $data = request()->json()->all()['data']['message']['_data'];
        $message = $data['body'];
        $assistant = $this->aiService->createAndRun($message, "asst_sgHG5GtlW0UWg4z2zZqzvC1W");
        $this->msService->sendOpenAiResponse($assistant);
    }

    function messageReceived(Request $request)
    {
        $this->middleware('killSwitch');

        try {

            $useOpenAi = false;
            $data = request()->json()->all()['data']['message']['_data'];
            $message = $data['body'];
            $personName = $data['notifyName'];
            $from = $data['from'];
            $to = $data['to'];
            $hash = $data['id']['_serialized'];
            $fromMe = $data['id']['fromMe'];
            $messageNumber = detectManualMessage($from, $message);
            if ($fromMe) {
                KillSwitch::create([
                    "from" => $to,
                    "kill" => true,
                    "kill_message" =>"Controller ".$message.$data['type'],
                ]);
            }
                 $this->msService->sendTestMessage("Controller ".$message.$data['type']);
                            return;
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
                createConvo($from);
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
                        // if ($from == '917009154010@c.us') {
                        //     $this->msService->sendTestMessage($query);
                        //     return;
                        // }
                        $this->msService->giveQueryResponse($query, $messageNumber == 1);
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
        $to =  '917009154010@c.us';
        $response = $this->msService->sendTestMedia($mediaUrl);
        return $response->getBody();
    }
}
