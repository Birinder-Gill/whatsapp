<?php

namespace App\Http\Middleware;

use App\Services\WhatsAppApiService;
use Closure;
use Illuminate\Http\Request;

class CheckCallMiddleware
{



    protected WhatsAppApiService $waService;

    public function __construct(WhatsAppApiService $waService)
    {
        $this->waService = $waService;
    }

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
                $call = $data['data']['call'];
                $content =  "*ASK QUERY*\n------------------------\n";
                $content = $content.'*Number:* ' . substr(explode("@", $call['from'])[0], -10) . "\n";
                $this->waService->sendWhatsAppMessage('120363318263831708@g.us', $content);
                logMe("CALL AYA",$data);
                return response("It is a call", 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $next($request);
    }
}
