<?php

namespace App\Http\Middleware;

use App\Models\KillSwitch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            return $this->notHappening($request, $next);
        }

        if (isset($data['author']) && $fromMe) {
            KillSwitch::create([
                "from" => $to,
                "kill" => true,
                "kill_message" => $message,
            ]);
            return $this->notHappening($request, $next);
        }
        //Only messages from the user will go ahead.
        if ($fromMe) {
            return $this->notHappening($request, $next);
        }

        return $next($request);
    }

    function notHappening(Request $request, Closure $next)
    {
        return response("Bas ho gya", 200); // Block the request
    }
}
