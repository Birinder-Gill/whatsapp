<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogKeeper extends Model
{
    use HasFactory;
    protected $fillable = [
        "to",
        "price",
        "address",
        "moreDetails",
        "useCase",
        "deliveryWay",
        "deliveryWime",
        "pincodeAvailability",
        "followUpGivenByUser",
        "ok",
    ];
}
