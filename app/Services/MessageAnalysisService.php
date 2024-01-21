<?php

namespace App\Services;

class MessageAnalysisService
{
    function askingForPrice($message): bool
    {
        return isAskingForPrice($message);
    }

    function discussingPrice($message): int
    {
        if (priceIsHighInComparison($message)) return 1;
        if (priceIsHighGenerally($message)) return 2;
        if (askingBulkOrderPrice($message)) return 3;
        return 0;
    }

    function userReadyToOrder($message): bool
    {
        return isReadyToOrder($message);
    }
    function queryDetection($message):string
    {


        //  TODO: THIS METHOD SHOULD RETURN THE REPLY STRINGS AS PER CONDITION
        if (userAsksForDemo($message)) {  //  Address
        }
        if (userAsksForAddress($message)) {  //  More details.
        }
        if (userAsksForMoreDetails($message)) { //  Use case.
        }
        if (userAsksForUseCase($message)) { //  Delivery Way.
        }
        if (userAsksForDeliveryWay($message)) { //  Delivery time
        }
        if (userAsksForDeliveryTime($message)) {  //  Pincode availability
        }
        if (userAsksForPincodeAvailability($message)) {  //  Follow Up given by user.
        }
        if (userSaysOk($message)) {  //  Follow Up given by user.
        }
        return '';
    }
}
