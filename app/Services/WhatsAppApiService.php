<?php

namespace App\Services;
use Psr\Http\Message\ResponseInterface;

class WhatsAppApiService
{
    function sendWhatsAppMessage($to,$body)
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
        $response = $client->request('POST', config('app.waapiBaseUrl').'send-message', [
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
            "messageId" => $hash        ];
        $response = $client->request('POST',  config('app.waapiBaseUrl').'delete-message-by-id', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ]);
        return $response;
    }



    function sendWhatsappMedia($to,$mediaUrl, $caption = '')
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
        $response = $client->request('POST',  config('app.waapiBaseUrl').'send-media', [
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
