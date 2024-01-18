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
            $jsonBody = json_encode(request()->json()->all());
            // $jsonBody = request()->json()->all()['data']['message']['_data']['notifyName'];
            $this->whatsAppMessage($to, $jsonBody);
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
        $mediaUrl = 'https://images.unsplash.com/photo-1682686578707-140b042e8f19?q=80&w=1375&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D';
        $to =  '917009154010@c.us';
        $response = $this->sendMedia($to,$mediaUrl);
        return $response->getBody();
    }

    function sendMedia($to,$mediaUrl,$caption = '') : ResponseInterface {
        $apiToken = 'PudFRi3j0sxlsy1qCwL6vSCyjG17fjLFs9fbZp0O336e5cf8';

        $client = new \GuzzleHttp\Client([
            'verify' => false, // Disable SSL verification - only use this for local development
        ]);
        $body = [
            "chatId" =>$to,
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
