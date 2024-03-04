<?php

namespace App\Services;

use Psr\Http\Message\ResponseInterface;

class WhatsAppApiService
{
    function sendWhatsAppMessage($to, $body)
    {
        if ($body == "") {
            return;
        }
        $apiToken = config('app.waapiKey');

        $client = new \GuzzleHttp\Client([
            'verify' => false, // Disable SSL verification - only use this for local development
        ]);
        $body = [
            "chatId" => $to,
            "message" => $body
        ];
        $response = $client->request('POST', config('app.waapiBaseUrl') . 'send-message', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ]);
        return $response;
    }

    function deleteWhatsAppMessage($hash)
    {
        if ($hash == "") {
            return;
        }
        $apiToken = config('app.waapiKey');

        $client = new \GuzzleHttp\Client([
            'verify' => false, // Disable SSL verification - only use this for local development
        ]);
        $body = [
            "messageId" => $hash
        ];
        $response = $client->request('POST',  config('app.waapiBaseUrl') . 'delete-message-by-id', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ]);
        return $response;
    }

    function sendGemCraftVCard($to, $firstName, $lastName)
    {
        $apiToken = config('app.waapiKey');
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        $number = explode("@",config('app.myNumber'))[0];
        $body = [
            "vCard"=>[
                "waid"=>$number,
                "iternationalnumber"=>"+$number",
                "lastname"=>$lastName,
                "firstname"=>$firstName,
                "organization"=>"$firstName $lastName"
            ],
            "chatId"=>$to
        ];

        $response = $client->request('POST', 'https://waapi.app/api/v1/instances/5391/client/action/send-vcard', [
            'body' => json_encode($body),
            'headers' => [
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $apiToken,
                'content-type' => 'application/json',
            ],
        ]);

        echo $response->getBody();
    }

    function sendWhatsappMedia($to, $mediaUrl, $caption = '')
    {
        $apiToken = config('app.waapiKey');
        $client = new \GuzzleHttp\Client([
            // 'verify' => false, // Disable SSL verification - only use this for local development
        ]);
        $body = [
            "chatId" => $to,
            "mediaUrl" => $mediaUrl,
            "mediaCaption" => $caption
        ];
        $response = $client->request('POST',  config('app.waapiBaseUrl') . 'send-media', [
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
