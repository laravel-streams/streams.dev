<?php

namespace App\Http\Middleware;

use Illuminate\Support\Arr;
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
        if (in_array(Request::ip(), config('streams.whitelist', []))) {
            return $next($request);
        }

        $key = Arr::get($_COOKIE, 'cp_key');

        if ($key && $key == config('streams.cp_key')) {
            return $next($request);
        }

        return abort(403);
    }
}
