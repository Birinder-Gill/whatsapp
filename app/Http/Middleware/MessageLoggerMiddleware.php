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
            dd(request()->json()->all());
            $data = request()->json()->all()['data']['message']['_data'];

            $fromMe = $data['id']['fromMe'];
            $message = $data['body'];
            $to = $data['to'];
            $from = $data['from'];
            Log::info("MessageLoggerMiddleware::", request()->json()->all());

            if (str_starts_with($message, '*From:* ') && $fromMe) {
                return response("Done bro", 200); // Block the request
            }

            $messageNumber = detectManualMessage($fromMe ? $to : $from, $message, $fromMe);

            if (($messageNumber > -1 || config('app.product') === "Tags")
             && (!(request()->json()->all()['data']["media"]))) {
                if (isset($data['notifyName'])) {
                    $personName = $data['notifyName'];
                } else {
                    $lastRow = getLatestMessage($fromMe ? $to : $from);
                    if ($lastRow) {
                        $personName = $lastRow->displayName;
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
            } else if ((request()->json()->all()['data']["media"])) {
                if ($messageNumber === 1 && $fromMe && isset($data["caption"])) {
                    $message = "Info message......";
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
            }
        } catch (\Throwable $th) {
            report($th);
        }
        if ($fromMe) {
            return response("Done bro", 200); // Block the request
        }
        if(array_key_exists("killSwitch",request()->json()->all())){

            return response("Done bro", 200); // Block the request
        }
        return $next($request);
    }
}
