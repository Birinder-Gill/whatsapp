<?php

namespace App\Http\Controllers;

use App\Services\MessageAnalysisService;
use App\Services\MessageSendingService;
use Illuminate\Http\Request;


class WhatsAppMessageController extends Controller
{
    protected $to = '917009154010@c.us';
    protected MessageAnalysisService $maService;
    protected MessageSendingService $msService;

    public function __construct(MessageAnalysisService $maService, MessageSendingService $msService)
    {
        $this->maService = $maService;
        $this->msService = $msService;
    }

    function isAskingForPrice(Request $request)
    {
        // Normalize the input
        // Normalize text for case-insensitivity and basic cleaning:
        $normalized = strtolower(trim(preg_replace('/[^a-z0-9\s]/i', '', $request->query('line'))));

        // Keywords related to price, including common misspellings and Hinglish variations:
        $priceKeywords = [
            'price', 'prac', 'praice', 'prise', 'prc',
            'rate', 'ret', 'raet', 'reet',
            'kitna', 'kitne', 'keemat', 'dam', 'muly', 'mahnga', 'sasta'
        ];

        // Check for direct matches or partial matches with wildcards:
        dd(preg_match('/\b(' . implode('|', $priceKeywords) . ')\b/i', $normalized) ||
            preg_match('/\b(' . implode('|', $priceKeywords) . ')\w*\b/i', $normalized));
    }
    function sendMessage(Request $request)
    {
        // dd($this->msService->getReq()->all());

        $body = "fudu bc";
        $response = $this->msService->sendTestMessage($body);
        return $response->getBody();
    }


    function messageReceived(Request $request)
    {
        try {
            $data = request()->json()->all()['data']['message']['_data'];
            $message = $data['body'];
            $personName = $data['notifyName'];
            $from = $data['from'];
            $messageNumber = detectManualMessage($from, $message);
            $logArray = [
                'from' => $from,
                'displayName' => $personName,
                'to' => $data['to'],
                'counter' => $messageNumber + 1,
                'messageText' => $message,
                'messageId' => $data['id']['id'],
            ];

            if ($messageNumber > -1) {
                incrementCounter($logArray);
                if ($messageNumber === 0) {
                    $this->msService->sendFirstMessage($personName); //TODO:: CHANGE IT TO MEDIA WITH CAPTION
                }
                if ($messageNumber === 1) {
                    if ($this->maService->askingForPrice($message)) {
                        $this->sendDiscountedPriceMessage();
                    } else {
                        $discussingPrice = $this->maService->discussingPrice($message);
                        if ($discussingPrice) {
                            $this->msService->answerPriceDiscussion($discussingPrice);
                        } else {
                            if ($this->maService->userReadyToOrder($message)) {
                                //Order confirmation
                                $this->sendOrderConfirmation($from, $personName, $messageNumber);
                            } else {
                                $query = $this->maService->queryDetection($message);
                                $this->msService->giveQueryResponse($query);
                            }
                        }
                    }
                }
                if ($messageNumber >= 2) {
                    if ($this->maService->userReadyToOrder($message)) {
                        //Order confirmation
                        $this->sendOrderConfirmation($from, $personName, $messageNumber);
                    } else {
                        $query = $this->maService->queryDetection($message);
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->msService->sendTestMessage($e->getMessage());
        }
    }

    function sendOrderConfirmation($from, $personName, $messageNumber)
    {
        //TODO: STORE THE PERSON'S ID AND ORDER PLACED

        $this->msService->sendOrderConfirmation();
    }
    function sendDiscountedPriceMessage()
    {
        $this->msService->sendDiscountedPriceMessage();
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
