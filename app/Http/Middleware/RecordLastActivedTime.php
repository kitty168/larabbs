<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class RecordLastActivedTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 如果时登录用户的话
        if(Auth::check()) {
            Auth::user()->recordLastActivedAt();
        }

        return $next($request);
    }
}
