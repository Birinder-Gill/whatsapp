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
        // dd(KillSwitchMiddleware::class);
        $data = request()->json()->all()['data']['message']['_data'];
        $fromMe = $data['id']['fromMe'];
        logMe("KillSwitchMiddleware::",["Data"=>$data]);
        $message = $data['body'];
        $to = $data['to'];
        $from = $data['from'];
        // logMe("KillSwitchMiddleware::".$message , $data);

        if (KillSwitch::where([
            "from" => $fromMe ? $to : $from,
            "kill" => true,
        ])->exists()) {
            return $this->notHappening($request,$next);
        }

        if (isset($data['author']) && $fromMe) {
            KillSwitch::create([
                "from" => $to,
                "kill" => true,
                "kill_message" => $message,
            ]);
            return $this->notHappening($request,$next);
        }
        return $next($request);
    }

    function notHappening(Request $request, Closure $next) {
        logMe("KillSwitchMiddleware:: notHappening");
        if (config('app.product') === "Tags") {
            $requestData = $request->json()->all();
            $requestData['killSwitch'] = true;
            $request->merge(['json' => $requestData]);
            return $next($request);
        }
        return response("Bas ho gya", 200); // Block the request
    }
}
