<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenAiThread extends Model
{
    use HasFactory;
    protected $fillable = [
        "from",
        "threadId"
    ];
}

