<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllWapiChats extends Model
{
    use HasFactory;
    protected $fillable = [
        'from',
        "messageId",
        'message',
        "type", // text, document image video bla bla
        "to",
        "fromMe",
        "messageTime"
    ];
}
