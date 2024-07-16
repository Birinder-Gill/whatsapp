<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Seshac\Shiprocket\Shiprocket;

class ShipRocketController extends Controller
{
    function initShipRocket(Request $request)
    {
        $token =  Shiprocket::getToken();
        $orderDetails = [
            // refer above url for required parameters 
            'per_page' => 20,
        ];
        $response =  Shiprocket::order($token)->getOrders($orderDetails);
        dd($response);
    }

    function wpOrderCreated(Request $request)
    {
        return response("Success", 200);
    }
}
