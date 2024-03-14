<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('logError')) {
    function logError($th, $message = ''): int
    {
        if($message){
            Log::error($message);
        }
        report($th);
        return 1;
    }
}

if (!function_exists('logMe')) {
    function logMe(string $message, $context = null): int
    {
        if($context){
            if(!is_array($context)){
                $context = ["Context" => $context];
            }
        }else{
            $context = [];
        }
        Log::info($message,$context);
        return 1;
    }
}
