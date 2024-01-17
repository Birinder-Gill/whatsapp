<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WhatsAppMessageController extends Controller
{
    function sendMessage(Request $request)
    {
        $apiToken = 'PudFRi3j0sxlsy1qCwL6vSCyjG17fjLFs9fbZp0O336e5cf8';

        $client = new \GuzzleHttp\Client([
            'verify' => false, // Disable SSL verification - only use this for local development
        ]);
        $response = $client->request('POST', 'https://waapi.app/api/v1/instances/4596/client/action/send-message', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'body' => '{"chatId":"917009154010@c.us","message":"Yo bro"}',
        ]);
       return $response->getBody();
    }
    function messageReceived(Request $request) {
            dd($request);

    }
}
