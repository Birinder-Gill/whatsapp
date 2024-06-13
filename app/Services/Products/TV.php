<?php

namespace App\Services\Products;

use App\Enums\UserLanguage;
use App\Services\ReplyCreationService;
use Nette\NotImplementedException;

class TV extends ReplyCreationService
{
    function getQueryResponse(string $query): string
    {
        return $query;
        $language = UserLanguage::HINGLISH;
        switch ($language) {
            case UserLanguage::HINGLISH:
                return match ($query) {
                    "UNKNOWN" => '',
                };
            #region Other languages
            case UserLanguage::HINDI:
                return match ($query) {
                };
            case UserLanguage::ENGLISH:
                return match ($query) {
                };
            #endregion
        }

        return '';
    }

    function getLinkMessage(): string
    {
       return "";
    }

    function getFirstMessage($personName): array
    {
        $firstMessage = "";

        return [
            'message' => $firstMessage,
            'media' =>  config('app.video')
        ];
    }

    function getFirstMedias(): array
    {
        return [
            config('app.picOne'),
            config('app.picTwo'),
            config('app.picThree')
        ];
    }


    function getFirstFollowUp(): string
    {
        return "";
    }

    function getContactSaveFollowUp(): string
    {
        return "";
    }
}
