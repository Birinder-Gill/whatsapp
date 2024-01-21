<?php

namespace App\Services;

use Illuminate\Http\Request;

class ReplyCreationService
{
    protected $from;

    public function __construct(Request $request)
    {
        // $this->from =  request()->json()->all()['data']['message']['_data'];
    }
    function getFrom()
    {
        return "917009154010@c.us";
        // return $this->from;
    }
}
