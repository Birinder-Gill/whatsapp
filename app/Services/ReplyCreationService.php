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

    function getContactSaveFollowUp(): string
    {
        return 'Namaste, kripya hamara contact save kar lein. Aisa karne se aap jewellery se related products aur offers, jo apke business me apki help kar sakte hain, seedhe WhatsApp stories me dekh sakte hain aur WhatsApp se hi order karke apne address par product pa sakte hain.';
    }

    abstract function getQueryResponse(string $query): string;

    abstract  function getLinkMessage(): string;

    abstract  function getFirstFollowUp(): string;

    abstract function getFirstMessage($personName): array;

    abstract function getFirstMedias(): array;
}
