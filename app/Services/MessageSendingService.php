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

    function sendOpenAiResponse(array $toSend, string $to)
    {
        $this->waService->sendWhatsAppMessage($to, $toSend);
    }

    function sendFirstMessage($personName, string $to)
    {
        $toSend = $this->rcService->getFirstMessage($personName);
        logMe("sendFirstMessage", [
            "person name" => $personName,
            "To" => $to,
            "To send" => $toSend,
        ]);
        $response = $this->waService->sendWhatsAppMedia($to, config('app.url') . $toSend['media'], $toSend['message']);
        if (json_decode($response->getBody())->data->status === 'success') {
            WhatsAppLead::where('from', $to)->update(['infoSent' => 1]);
        }
        $medias = $this->rcService->getFirstMedias();

        foreach ($medias as $media) {
            $this->waService->sendWhatsAppMedia($to, config('app.url') . $media);
        }
    }

    function giveQueryResponse(string $query, string $to, $appendLink)
    {
        $response = $this->rcService->getQueryResponse($query);
        if (is_array($response)) {
            if (isset($response['type'])) {
                switch ($response['type']) {
                    case 'TV':
                        if (isset($response['address'])) {
                            $content =  "*CONFIRM ORDER*\n------------------------";
                            $content = '*Number:* ' . substr(explode("@", $to)[0], -10) . "\n";
                            $content = $content . '*Address: ' . ($response['address']) . "*\n" . "------------------------";
                             $this->waService->sendWhatsAppMessage('917009154010@c.us', $content);
                        }
                        if (isset($response['media'])) {
                            return $this->waService->sendWhatsAppMedia($to, $response['media'], isset($response['remainingMessage']) ? $response['remainingMessage'] : '');
                        }
                        if (isset($response['remainingMessage'])) {
                            return $this->waService->sendWhatsAppMessage($to, $response);
                        }
                        break;

                    default:
                        # code...
                        break;
                }
            }
        }

        if (is_string($response)) {
            if ($appendLink && $query !== 'OK') {
                $response = $response . $this->rcService->getLinkMessage();
            }
            return $this->waService->sendWhatsAppMessage($to, $response);
        }
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
    function callEndpoint($endpoint, $body = [])
    {
        return json_decode("" . $this->waService->callEndpoint($endpoint, $body));
    }
}
