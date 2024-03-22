<?php

namespace App\Services;

use Illuminate\Http\Request;

abstract class ReplyCreationService
{
    abstract function getContactSaveFollowUp(): string;

    abstract function getQueryResponse(string $query): string;

    abstract  function getLinkMessage(): string;

    abstract  function getFirstFollowUp(): string;

    abstract function getFirstMessage($personName): array;

    abstract function getFirstMedias(): array;
}
