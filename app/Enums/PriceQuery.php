<?php

namespace App\Enums;

enum PriceQuery: string
{
    case HIGH_AS_COMPARED = 'HIGH_AS_COMPARED';
    case HIGH_IN_GENERAL = 'HIGH_IN_GENERAL';
    case WHOLESALE = 'WHOLESALE';
    case UNKNOWN = "UNKNOWN";
}
