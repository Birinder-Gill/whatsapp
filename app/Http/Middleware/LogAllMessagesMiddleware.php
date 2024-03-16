<?php

namespace App\Http\Middleware;

use App\Models\LeadRecord;
use App\Models\MessageLog;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class LogAllMessagesMiddleware
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
        $data = request()->json()->all()['data']['message']['_data'];
        $fromMe = $data['id']['fromMe'];
        $message = $this->makeMessage();

        /// THIS WOULD BE THE MESSAGE SENT BY US AS LEAD SYSTEM
        if (str_starts_with($message, '*From:* ') && $fromMe) {
            return response("Done bro", 200); // Block the request
        }

        $to = $data['to'];
        $from = $data['from'];
        $counter = 0;
        $personName = (isset($data['notifyName'])) ? $data['notifyName'] : '';
        $lastRow = MessageLog::where('from', $to)->where('to', $from)->orderBy('id', 'desc')->first();


        if ($lastRow) {
            if (!$personName) {
                $personName = $lastRow->displayName;
            }
            $counter = $lastRow->counter;
        }

        if (!$fromMe) {
            $counter = $counter + 1;
        }

        $result = MessageLog::create([
            "from" => $from,
            "displayName" => $personName,
            "to" => $to,
            "counter" => $counter,
            "messageText" => $message,
            "fromMe" => $fromMe,
        ]);
        if ($result) {
            LeadRecord::updateOrCreate([
                "from" => $fromMe ? $to : $from
            ], [
                "from" => $fromMe ? $to : $from,
                "leadSent" => false,
                'last_message_at' => Carbon::now('Asia/Kolkata'),
            ]);
        }

        return $next($request);
    }

    function makeMessage(): string
    {
        $data = request()->json()->all()['data']['message']['_data'];
        $message = $data['body'];
        $media = request()->json()->all()['data']["media"];

        if ($media) {
            if (isset($data["caption"]) && $data["caption"]) {
                $message = $data["caption"];
            } else {
                if (isset($media['filename']) && $media['filename']) {
                    $message = $media['filename'];
                } else {
                    $message = "Media";
                }
            }
        }

        if (strlen($message) > 50) {
            $message = substr($message, 0, 50) . '...';
        }

        return $message;
    }
}
