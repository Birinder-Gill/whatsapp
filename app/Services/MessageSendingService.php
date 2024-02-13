<?php

namespace App\Services;

use App\Enums\GeneralQuery;
use App\Models\LogKeeper;
use App\Models\WhatsAppLead;
use Psr\Http\Message\ResponseInterface;

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
        $to = $this->rcService->getFrom();
        $toSend = $this->rcService->getFirstMessage($personName);

        $response = $this->waService->sendWhatsAppMedia($to, config('app.url') . config('app.video'), $toSend);
        if (json_decode($response->getBody())->data->status === 'success') {
            WhatsAppLead::where('from' , $to)->update(['infoSent' => 1]);
        }
        $this->waService->sendWhatsAppMedia($to, config('app.url') . config('app.picOne'));
        $this->waService->sendWhatsAppMedia($to, config('app.url') . config('app.picTwo'));
        $this->waService->sendWhatsAppMedia($to, config('app.url') . config('app.picThree'));
    }

    function giveQueryResponse(GeneralQuery $query)
    {
        $response = $this->rcService->getQueryResponse($query);
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
