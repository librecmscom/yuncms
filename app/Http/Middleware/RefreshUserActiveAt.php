<?php

namespace App\Http\Middleware;

use App\Jobs\User\RefreshUserLastActiveAt;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefreshUserActiveAt
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::hasUser()) {
            \mt_rand(0, 9) > 5 || RefreshUserLastActiveAt::dispatchAfterResponse($request->user());
        }
        return $next($request);
    }
}
