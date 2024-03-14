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
           logMe("MessageLoggerMiddleware:: $from ", request()->json()->all());

            if (str_starts_with($message, '*From:* ') && $fromMe) {
                return response("Done bro", 200); // Block the request
            }

            $messageNumber = detectManualMessage($fromMe ? $to : $from, $message, $fromMe);
            logMe("MessageLoggerMiddleware:: $from  ", ['messageNumber' => $messageNumber]);

            if (($messageNumber > -1 || config('app.product') === "Tags")
                && (!(request()->json()->all()['data']["media"]))
            ) {
                createConvo($fromMe ? $to : $from, $fromMe);
                if (isset($data['notifyName'])) {
                    $personName = $data['notifyName'];
                } else {
                    $lastRow = getLatestMessage($fromMe ? $to : $from);
                    if ($lastRow) {
                        $personName = $lastRow->displayName;
                    }
                }
               logMe("MessageLoggerMiddleware:: $from ", ['personName' => $personName]);

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
               logMe("MessageLoggerMiddleware:: $from ",['message' => $message]);
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
        $all = request()->json()->all();
        if (
            array_key_exists("json", $all)
            && array_key_exists("killSwitch", $all['json'])
            && $all['json']["killSwitch"]
        ) {
           logMe("MessageLoggerMiddleware:: $from Kill Switch activates ");

            return response("Done bro", 200); // Block the request
        }
        return $next($request);
    }
}
