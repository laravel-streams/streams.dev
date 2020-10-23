<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Request;

class AuthenticateApi
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
        if (!in_array(Request::ip(), ['127.0.0.1', '::1'])) {
            return redirect(403);
        }

        return $next($request);
    }
}
