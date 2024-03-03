<?php

namespace App\Services;

use Illuminate\Http\Request;

abstract class ReplyCreationService
{
    protected $from;

    public function __construct(Request $request)
    {
        try {
            $this->from =  request()->json()->all()['data']['message']['_data']['from'];
        } catch (\Throwable $th) {
            $this->from = '917009154010@c.us';
        }
    }

    function getFrom()
    {
        return $this->from;
    }

    abstract function getQueryResponse(string $query): string;

    abstract  function getLinkMessage(): string;

    abstract function getFirstMessage($personName): array;

    abstract function getFirstMedias(): array;
}
