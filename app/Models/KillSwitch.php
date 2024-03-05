<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KillSwitch extends Model
{
    use HasFactory;
    protected $fillable = [
        'from',
        'kill',
        'kill_message'
    ];
}
