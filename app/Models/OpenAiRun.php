<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenAiRun extends Model
{
    use HasFactory;
    protected $fillable = [
        'threadId',
        'runId'
    ];
}
