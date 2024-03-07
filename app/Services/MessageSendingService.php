<?php

namespace App\Services;

use App\Models\WhatsAppLead;
use Illuminate\Support\Facades\Log;

class MessageSendingService
{

    protected ReplyCreationService $rcService;
    protected WhatsAppApiService $waService;

    public function __construct(ReplyCreationService $rcService, WhatsAppApiService $waService)
    {
        $this->rcService = $rcService;
        $this->waService = $waService;
    }

    function sendOpenAiResponse(array $toSend)
    {
        $to = $this->rcService->getFrom();
        $this->waService->sendWhatsAppMessage($to, $toSend);
    }

    function sendFirstMessage($personName)
    {
        Log::info("sendFirstMessage",$personName);
        $to = $this->rcService->getFrom();
        $toSend = $this->rcService->getFirstMessage($personName);

        $response = $this->waService->sendWhatsAppMedia($to, config('app.url') . $toSend['media'], $toSend['message']);
        if (json_decode($response->getBody())->data->status === 'success') {
            WhatsAppLead::where('from', $to)->update(['infoSent' => 1]);
        }
        $medias = $this->rcService->getFirstMedias();
        foreach ($medias as $media) {
            $this->waService->sendWhatsAppMedia($to, config('app.url') . $media);
        }
    }

    function giveQueryResponse(string $query, $appendLink = false)
    {
        $response = $this->rcService->getQueryResponse($query);
        if ($appendLink && $query !== 'OK') {
            $response = $response . $this->rcService->getLinkMessage();
        }
        return $this->waService->sendWhatsAppMessage($this->rcService->getFrom(), $response);
    }

    function deleteMessage($hash)
    {
        $this->waService->deleteWhatsAppMessage($hash);
    }

    function sendTestMessage($message)
    {
        return $this->waService->sendWhatsAppMessage('917009154010@c.us', $message);
    }

    function sendTestMedia($mediaUrl, $caption = '')
    {
        return $this->waService->sendWhatsappMedia('917009154010@c.us', $mediaUrl, $caption);
    }
}
