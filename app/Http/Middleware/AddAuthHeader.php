<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AddAuthHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Cookie::get('_token')) {
            $token = Cookie::get('_token');
            $request->headers->add(['Authorization' => 'Bearer ' . $token]);
        }

        \Log::info($request->path());
        \Log::info($request->headers);
        return $next($request);
    }
}
