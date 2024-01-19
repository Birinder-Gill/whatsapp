<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PhpParser\Node\Expr\Cast\Object_;
use Psr\Http\Message\ResponseInterface;

class WhatsAppMessageController extends Controller
{
    function sendMessage(Request $request)
    {
        // dd($request);
        $to = '917009154010@c.us';
        $body = json_encode(request()->json()->all());
        $response = $this->whatsAppMessage($to, $body);
        return $response->getBody();
    }
    function messageReceived(Request $request)
    {
        $to = '917009154010@c.us';
        try {

            $data = request()->json()->all()['data']['message']['_data'];
            $personName = $data['notifyName'];
            $message = $data['body'];
            if (str_contains($message, 'info')) {
                $toSend = "Hi, ".$personName.", \nThank you for your interest in our Eye Loupe Magnifier lens for jewelers! Our LED magnifying glass is great for looking closely at jewelry details, helping jewelers see small things better, like gemstones and delicate pieces.

If you want to buy one, just say \"Interested,\" and we'll guide you through the process. This fantastic tool is currently available at a discounted price of 990 rupees for a limited time. If you have any questions, feel free to ask here on WhatsApp.

हमारे Eye Loupe Magnifier लेंस के लिए आपकी रुचि के लिए धन्यवाद! हमारा LED magnifying glass ज्वेलरी के छोटे विवरणों को ध्यान से देखने के लिए बहुत उपयुक्त है, जो ज्वेलर्स को जेमस्टोन्स और नाजुक टुकड़ों को बेहतर तरीके से देखने में मदद करता है।

यदि आप एक खरीददारी करना चाहते हैं, तो बस \"Interested\" कहें, और हम आपको प्रक्रिया के माध्यम से मार्गदर्शन करेंगे। यह शानदार टूल अब एक सीमित समय के लिए 990 रुपये में उपलब्ध है। अगर आपके पास कोई सवाल है, तो WhatsApp पर यहां पूछें।";
                $this->whatsAppMessage($to, $toSend);
            }
        } catch (\Throwable $e) {
            $this->whatsAppMessage($to, $e->getMessage());
        }
    }













    function whatsAppMessage($to, $body): ResponseInterface
    {
        $apiToken = 'PudFRi3j0sxlsy1qCwL6vSCyjG17fjLFs9fbZp0O336e5cf8';

        $client = new \GuzzleHttp\Client([
            'verify' => false, // Disable SSL verification - only use this for local development
        ]);
        $body = [
            "chatId" => $to,
            "message" => $body
        ];
        $response = $client->request('POST', 'https://waapi.app/api/v1/instances/4734/client/action/send-message', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ]);
        return $response;
    }

    function sendMediaApi(Request $request)
    {
        $mediaUrl = 'https://drive.google.com/file/d/1sP3zfH4nIznkGX65Jtbh6WhQd1X0WWPS/view?usp=sharing';
        // $mediaUrl = 'https://images.unsplash.com/photo-1682686578707-140b042e8f19?q=80&w=1375&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D';
        $to =  '917009154010@c.us';
        $response = $this->sendMedia($to, $mediaUrl);
        return $response->getBody();
    }

    function sendMedia($to, $mediaUrl, $caption = ''): ResponseInterface
    {
        $apiToken = 'PudFRi3j0sxlsy1qCwL6vSCyjG17fjLFs9fbZp0O336e5cf8';

        $client = new \GuzzleHttp\Client([
            'verify' => false, // Disable SSL verification - only use this for local development
        ]);
        $body = [
            "chatId" => $to,
            "mediaUrl" => $mediaUrl,
            "mediaCaption" => $caption
        ];
        $response = $client->request('POST', 'https://waapi.app/api/v1/instances/4734/client/action/send-media', [
            'body' => json_encode($body),
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
        return $response;
    }
}
