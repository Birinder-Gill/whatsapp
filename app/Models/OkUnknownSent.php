<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OkUnknownSent extends Model
{
    use HasFactory;
    protected $fillable = [
        'from'
    ];
}
