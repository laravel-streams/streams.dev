<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Request;

class AuthenticateCp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        dump(Request::ips());
        dd(config('streams.whitelist', []));
        if (!in_array(Request::ip(), config('streams.whitelist', []))) {
            return abort(403);
        }

        return $next($request);
    }
}
