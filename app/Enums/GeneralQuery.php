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
    case UNKNOWN = "UNKNOWN";
    case OK = 'OK';
}


