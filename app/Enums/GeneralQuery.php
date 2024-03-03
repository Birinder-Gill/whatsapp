<?php

namespace App\Enums;


enum GeneralQuery: string
{
    case PRICE = "PRICE";
    case ADDRESS = 'ADDRESS';
    case MORE_DETAILS = 'MORE_DETAILS';
    case USE_CASE = 'USE_CASE';
    case DELIVERY_WAY = 'DELIVERY_WAY';
    case DELIVERY_TIME = 'DELIVERY_TIME';
    case PINCODE_AVAILABILITY = 'PINCODE_AVAILABILITY';
    case FOLLOW_UP_GIVEN_BY_USER = 'FOLLOW_UP_GIVEN_BY_USER';
    case HIGH_AS_COMPARED = 'HIGH_AS_COMPARED';
    case HIGH_IN_GENERAL = 'HIGH_IN_GENERAL';
    case WHOLESALE = 'WHOLESALE';
    case OK = 'OK';
    case UNKNOWN = "UNKNOWN";
}


