<?php

use App\Models\CommandLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

if (!function_exists('logError')) {
    function logError($th, $message = ''): int
    {
        if ($message) {
            Log::error($message);
        }
        report($th);
        return 1;
    }
}

if (!function_exists('commandLog')) {
    function commandLog(string $command, string $message, bool $fromScheduler, string $level = 'info'): bool
    {
        $result = CommandLog::create([
            "command" => $command,
            "level" => $level,
            "message" => $message,
            "date" => Carbon::now('Asia/Kolkata')->format('Y-m-d'),
            "fromScheduler" => $fromScheduler,
        ]);
        if ($result) return true;
        return false;
    }
}


if (!function_exists('logMe')) {
    function logMe(string $message, $context = null): int
    {
        if ($context) {
            if (!is_array($context)) {
                $context = ["Context" => $context];
            }
        } else {
            $context = [];
        }
        Log::info($message, $context);
        return 1;
    }
}
