<?php

namespace App\Services;

use Psr\Http\Message\ResponseInterface;

class WhatsAppApiService
{

    function callEndpoint(string $endpoint, array $body = [])
    {
        $apiToken = config('app.waapiKey');


        $client = new \GuzzleHttp\Client([
            'verify' => config('app.env') !== 'local', // Disable SSL verification - only use this for local development
        ]);
        $response = $client->request('POST', config('app.waapiBaseUrl') . $endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
           'body' => json_encode($body),
        ]);
        return $response->getBody();
    }
    function sendWhatsAppMessage($to, $body)
    {
        if ($body == "") {
            return;
        }
        $apiToken = config('app.waapiKey');

        $client = new \GuzzleHttp\Client([
            'verify' => config('app.env') !== 'local', // Disable SSL verification - only use this for local development
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
            'verify' => config('app.env') !== 'local', // Disable SSL verification - only use this for local development
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

    function sendGemCraftVCard($to)
    {
        $apiToken = config('app.waapiKey');
        $client = new \GuzzleHttp\Client([
            'verify' => config('app.env') !== 'local',
        ]);
        $name = explode(" ", config('app.myName'));
        $number = explode("@", config('app.myNumber'))[0];
        $body = [
            "vCard" => [
                "waid" => $number,
                "iternationalnumber" => "+$number",
                "lastname" => $name[1],
                "firstname" => $name[0],
                "organization" => config('app.myName')
            ],
            "chatId" => $to
        ];

        $response = $client->request('POST',  config('app.waapiBaseUrl') . 'send-vcard', [
            'body' => json_encode($body),
            'headers' => [
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $apiToken,
                'content-type' => 'application/json',
            ],
        ]);
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
