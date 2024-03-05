<?php

namespace App\Http\Middleware;

use App\Models\KillSwitch;
use Closure;
use Illuminate\Http\Request;

class KillSwitchMiddleware
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
        $message = $data['body'];
        $to = $data['to'];
        $from = $data['from'];

        if (KillSwitch::where([
            "from" => $fromMe ? $to : $from,
            "kill" => true,
        ])->exists()) {
            return response('Access denied', 403); // Block the request
        }


        if ($fromMe) {
            KillSwitch::create([
                "from" => $to,
                "kill" => true,
                "kill_message" => "Middleware " . $message . " " . $data['type'],
            ]);

            return response('Access denied', 403); // Block the request
        }
        return $next($request); // Allow the request to proceed
    }
}
