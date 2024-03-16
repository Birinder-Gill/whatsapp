<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappUser extends Model
{
    use HasFactory;
    protected $fillable = [
        "chatId",
        "name",

    ];
}
