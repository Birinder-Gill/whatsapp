<?php

namespace App\Http\Middleware;

use App\Models\KillSwitch;
use App\Models\MessageLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageLoggerMiddleware
{
    /*
     * THIS MIDDLEWARE WILL BE USED TO CREATE Conversation, WhatsappLead and WhatsappMessage.`
     * WhatsappMessage TABLE WILL STORE ONLY INCOMING MESSAGES AND THEIR COUNTER.`
     * HERE fromMe WILL BE ALWAYS FALSE.`
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $data = request()->json()->all()['data']['message']['_data'];
            $message = $data['body'];
            $from = $data['from'];

            $messageNumber = detectManualMessage($from, $message);

            if (($messageNumber > -1)) {
                createConvo($from);

                if ($messageNumber === 0) {
                    createNewLead($from);
                }

                if ($messageNumber === 1) {
                    createHotLead($from);
                }
            } else {
                return response("Not today bro", 200);
            }
        } catch (\Throwable $th) {
            report($th);
        }

        return $next($request);
    }
}
