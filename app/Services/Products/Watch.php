<?php

namespace App\Services\Products;

use App\Enums\UserLanguage;
use App\Services\ReplyCreationService;
use Nette\NotImplementedException;

class Watch extends ReplyCreationService
{
    function getQueryResponse(string $query): string
    {
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
        throw new NotImplementedException();
    }

    function getFirstMessage($personName): array
    {
        $firstMessage = "";

        return [
            'message' => $firstMessage,
            'media' =>  ''
        ];
    }

    function getFirstMedias(): array
    {
        return [];
    }

    function getFirstFollowUp(): string
    {
        throw new NotImplementedException();
    }

    function getContactSaveFollowUp(): string
    {
        throw new NotImplementedException();
    }
}
