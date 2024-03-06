<?php

namespace App\Http\Middleware;

use App\Models\KillSwitch;
use App\Models\MessageLog;
use Closure;
use Illuminate\Http\Request;

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
        MessageLog::create(
            [
                "from" => '$fromMe ? $to : $from',
                "fromMe" => true,
                "displayName" => '$personName',
                "messageText" => '$message',
                "counter" => 1
            ]
        );
        $data = request()->json()->all()['data']['message']['_data'];
        $fromMe = $data['id']['fromMe'];
        $message = $data['body'];
        $to = $data['to'];
        $from = $data['from'];
        $personName = $data['notifyName'];
        $messageNumber = detectManualMessage($from, $message, $fromMe);
        if (KillSwitch::where([
            "from" => $fromMe ? $to : $from,
            "kill" => true,
        ])->exists()) {
            return response("Access denied", 403); // Block the request
        }

        if ($messageNumber > -1 && (!(request()->json()->all()['data']["media"]))) {
            if ($messageNumber === 0 && $fromMe) {
                $message = "Info message......";
            }
            MessageLog::create(
                [
                    "from" => $fromMe ? $to : $from,
                    "fromMe" => $fromMe,
                    "displayName" => $personName,
                    "messageText" => $message,
                    "counter" => $messageNumber
                ]
            );
        }


        return $next($request); // Allow the request to proceed
    }
}
