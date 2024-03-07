<?php

namespace App\Http\Middleware;

use App\Models\KillSwitch;
use App\Models\MessageLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageLoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $data = request()->json()->all()['data']['message']['_data'];
            $fromMe = $data['id']['fromMe'];
            $message = $data['body'];
            $to = $data['to'];
            $from = $data['from'];


            $messageNumber = detectManualMessage($from, $message, $fromMe);
            Log::debug('MessageLoggerMiddleware',request()->json()->all());
            Log::debug('MessageLoggerMiddleware ',["Message number"=>$messageNumber]);

            if ($messageNumber > -1 && (!(request()->json()->all()['data']["media"]))) {
                if (isset($data['notifyName'])) {
                    $personName = $data['notifyName'];
                } else {
                    $lastRow = getLatestMessage($fromMe ? $to : $from);
                    if ($lastRow) {
                        $personName = $lastRow->displayName;
                    }
                }
                if ($messageNumber === 0) {
                    if ($fromMe) {
                        $message = "Info message......";
                    }
                }
                MessageLog::create(
                    [
                        "from" => $fromMe ? $to : $from,
                        "fromMe" => $fromMe,
                        "displayName" => $personName ?? "-/-",
                        "messageText" => $message,
                        "counter" => $messageNumber
                    ]
                );
            }
        } catch (\Throwable $th) {
            report($th);
        }
        if ($fromMe) {
            return response("Access denied", 403); // Block the request
        }
        return $next($request);
    }
}
