<?php

namespace App\Services;

use Psr\Http\Message\ResponseInterface;

class MessageSendingService
{

    protected ReplyCreationService $rcService;

    public function __construct(ReplyCreationService $rcService)
    {
        $this->rcService = $rcService;
    }

    function sendWhatsAppMessage($body): ResponseInterface
    {
        $apiToken = 'PudFRi3j0sxlsy1qCwL6vSCyjG17fjLFs9fbZp0O336e5cf8';
        $to = $this->rcService->getFrom();

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



    function sendWhatsappMedia($mediaUrl, $caption = ''): ResponseInterface
    {
        $apiToken = 'PudFRi3j0sxlsy1qCwL6vSCyjG17fjLFs9fbZp0O336e5cf8';
        $to = $this->rcService->getFrom();
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
