<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    use HasFactory;
    protected $fillable = [
        "from",
        "displayName",
        "to",
        "counter",
        "messageText",
        "fromMe",
        "leadSent"
    ];
}
