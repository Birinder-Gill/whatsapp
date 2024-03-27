<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OpenAiTokenCheckMiddleware
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
        $match = base64_encode("bsgillrandomuniquestring74635uihjncv894");
        $header = $request->header('X-match-Header');

        if ($match === $header) {
            return $next($request);
        }
        return response("403 Denied MF", 403);
    }
}
