<?php

use App\Models\Conversation;
use App\Models\WhatsAppLead;
use App\Models\WhatsAppMessage;
use Carbon\Carbon;

if (!function_exists('detectManualMessage')) {
    function detectManualMessage($senderId, $message): int
    {
        $row = WhatsAppMessage::where('from', $senderId)->orderBy('id', 'desc')->first();
        if ($row) {
            return $row->counter;
        } else if (str_contains($message, "info") || str_contains($message, "Facebook")) {
            return 0;
        }
        return -1;
    }
}

if (!function_exists('updateStatus')) {
    function updateStatus($from,$fromMe, $status = 'active')
    {
        Conversation::updateOrCreate(['from'=>$from],[
            'from'=>$from,
            'status'=>$status,
            'last_message_at' => Carbon::now(),
            'fromMe' => $fromMe
        ]);
    }
}


if (!function_exists('incrementCounter')) {
    function incrementCounter($logArray)
    {
       WhatsAppMessage::updateOrCreate([
        'messageId'=>$logArray['messageId']
       ],$logArray);

    }
}

if (!function_exists('createNewLead')) {
    function createNewLead($from)
    {
       if(WhatsAppLead::where('from',$from)->exists()) return;
       WhatsAppLead::create(['from'=>$from]);

    }
}

if (!function_exists('createHotLead')) {
    function createHotLead($from)
    {
        WhatsAppLead::where('from', $from)->update(['hotLead' => 1]);

    }
}




if (!function_exists('orderConfirmation')) {
    function orderConfirmation(): string
    {
        return "To place an order, fill out your address in the following google form";
    }
}
//METHODS BELOW ARE PROBABLY NOT NEEDED
if (!function_exists('isAskingForPrice')) {
    function isAskingForPrice($message)
    {
        return true;
    }
}

if (!function_exists('userAsksForDemo')) {
    function userAsksForDemo($message): string
    {
        return "To place an order, fill out your address in the following google form";
    }
}
if (!function_exists('userAsksForAddress')) {
    function userAsksForAddress($message): string
    {
        return "To place an order, fill out your address in the following google form";
    }
}
if (!function_exists('userAsksForMoreDetails')) {
    function userAsksForMoreDetails($message): string
    {
        return "To place an order, fill out your address in the following google form";
    }
}
if (!function_exists('userAsksForUseCase')) {
    function userAsksForUseCase($message): string
    {
        return "To place an order, fill out your address in the following google form";
    }
}
if (!function_exists('userAsksForDeliveryWay')) {
    function userAsksForDeliveryWay($message): string
    {
        return "To place an order, fill out your address in the following google form";
    }
}
if (!function_exists('userAsksForDeliveryTime')) {
    function userAsksForDeliveryTime($message): string
    {
        return "To place an order, fill out your address in the following google form";
    }
}
if (!function_exists('userAsksForPincodeAvailability')) {
    function userAsksForPincodeAvailability($message): string
    {
        return "To place an order, fill out your address in the following google form";
    }
}

if (!function_exists('userSaysOk')) {
    function userSaysOk($message): string
    {
        return "To place an order, fill out your address in the following google form";
    }
}

if (!function_exists('priceIsHighInComparison')) {
    function priceIsHighInComparison($message)
    {

    }
}
if (!function_exists('priceIsHighGenerally')) {
    function priceIsHighGenerally($message)
    {

    }
}
if (!function_exists('askingBulkOrderPrice')) {
    function askingBulkOrderPrice($message)
    {

    }
}
if (!function_exists('isReadyToOrder')) {


}
if (!function_exists('isAskingForWholesaleOrBulk')) {

}

