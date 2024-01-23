<?php

namespace App\Services;

use App\Enums\GeneralQuery;
use App\Enums\PriceQuery;
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

    function sendFirstMessage($personName): ResponseInterface
    {
        $to = $this->rcService->getFrom();
        $toSend = $this->rcService->getFirstMessage($personName);
        return $this->waService->sendWhatsAppMessage($to, $toSend);
    }

    function giveQueryResponse(GeneralQuery $query): ResponseInterface
    {
        $response = $this->rcService->getQueryResponse($query);
        return $this->waService->sendWhatsappMedia($this->rcService->getFrom(),$response);
    }


    function answerPriceDiscussion(PriceQuery $priceQuery): ResponseInterface
    {
        $response = $this->rcService->getPriceDiscussion($priceQuery);
        return $this->waService->sendWhatsAppMessage($this->rcService->getFrom(),$response);
    }

    function sendOrderConfirmation(): ResponseInterface
    {
        $message = $this->rcService->createOrderConfirmation();
        return $this->waService->sendWhatsAppMessage($this->rcService->getFrom(),$message);
    }

    function sendDiscountedPriceMessage(): ResponseInterface
    {
        $message = $this->rcService->getDiscountedPriceMessage();
        return $this->waService->sendWhatsAppMessage($this->rcService->getFrom(),$message);
    }

    function sendTestMessage($message): ResponseInterface
    {
        return $this->waService->sendWhatsappMedia('917009154010@c.us', $message);
    }

    function sendTestMedia($mediaUrl, $caption = ''): ResponseInterface
    {
        return $this->waService->sendWhatsappMedia('917009154010@c.us', $mediaUrl, $caption);
    }
}
