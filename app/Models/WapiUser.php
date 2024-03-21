<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WapiUser extends Model
{
    use HasFactory;
    protected $fillable = [
        "chatId",
        "isGroup",
        "name",
        "number",
        "lastMessage",
        "messagesFetched"
    ];
}
