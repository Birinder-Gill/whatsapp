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
        // Log::info("KillSwitchMiddleware::".$message , $data);

        if (KillSwitch::where([
            "from" => $fromMe ? $to : $from,
            "kill" => true,
        ])->exists()) {
            return response("Bas ho gya", 200); // Block the request
        }

        if (isset($data['author']) && $fromMe) {
            KillSwitch::create([
                "from" => $to,
                "kill" => true,
                "kill_message" => $message,
            ]);
            return response("Bas ho gya", 200); // Block the request
        }
        return $next($request);
    }
}
