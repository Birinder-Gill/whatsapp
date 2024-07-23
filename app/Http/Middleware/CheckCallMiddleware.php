<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCallMiddleware
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
            $data = request()->json()->all();
            if ($data['event'] === 'call') {
                // $content =  "*CONFIRM ORDER*\n------------------------\n";
                // $content = $content.'*Number:* ' . substr(explode("@", $to)[0], -10) . "\n";
                // $content = $content . '*Address: ' . ($response['address']) . "*\n";
                //  $this->waService->sendWhatsAppMessage('120363318263831708@g.us', $content);
                logMe("CALL AYA",$data);
                return response("It is a call", 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $next($request);
    }
}
