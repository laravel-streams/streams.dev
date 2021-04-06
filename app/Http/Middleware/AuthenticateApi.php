<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
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
        if (in_array(App::environment(), ['local', 'testing'])) {
            return $next($request);
        }

        if (Request::get('token') !== Config::get('streams.api.token')) {
            return abort(403);
        }

        return $next($request);
    }
}
