<?php

namespace App\Services;

use App\Enums\GeneralQuery;
use App\Enums\PriceQuery;
use App\Models\PriceLog;
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

    function sendFirstMessage($personName)
    {
        $to = $this->rcService->getFrom();
        $toSend = $this->rcService->getFirstMessage($personName);
        $this->waService->sendWhatsAppMedia($to, 'https://electricsuitcase.tech/storage/bVideo.mp4', $toSend);
        $this->waService->sendWhatsAppMedia($to, 'https://electricsuitcase.tech/storage/b1.jpeg');
        $this->waService->sendWhatsAppMedia($to, 'https://electricsuitcase.tech/storage/b2.jpeg');
        return $this->waService->sendWhatsAppMedia($to, 'https://electricsuitcase.tech/storage/b3.jpeg');
    }

    function giveQueryResponse(GeneralQuery $query)
    {
        if ($query == GeneralQuery::PRICE) return $this->sendDiscountedPriceMessage();
        $response = $this->rcService->getQueryResponse($query);
        if ($query == GeneralQuery::ADDRESS) {
            if (PriceLog::where(['to' => $this->rcService->getFrom(), 'address' => 1])->exists()) return;
            PriceLog::updateOrCreate(
                ['to' => $this->rcService->getFrom()],
                [
                    'address' => 1
                ]
            );
        }
        $this->waService->sendWhatsAppMessage($this->rcService->getFrom(), $response);
    }


    function answerPriceDiscussion(PriceQuery $priceQuery)
    {
        $response = $this->rcService->getPriceDiscussion($priceQuery);
        return $this->waService->sendWhatsAppMessage($this->rcService->getFrom(), $response);
    }

    function sendOrderConfirmation()
    {
        $message = $this->rcService->createOrderConfirmation();
        return $this->waService->sendWhatsAppMessage($this->rcService->getFrom(), $message);
    }

    function sendDiscountedPriceMessage()
    {
        if (PriceLog::where(['to' => $this->rcService->getFrom(), 'price' => 1])->exists()) return;
        PriceLog::updateOrCreate(
            ['to' => $this->rcService->getFrom()],
            [
                'price' => 1
            ]
        );
        $message = $this->rcService->getDiscountedPriceMessage();
        return $this->waService->sendWhatsAppMessage($this->rcService->getFrom(), $message);
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
