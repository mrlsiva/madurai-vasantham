<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $loginRoute  Login page to send guests to, e.g. 'admin:spin.login'
     */
    public function handle(Request $request, Closure $next, string $loginRoute = 'chitfund.login'): Response
    {
        if(!Auth::guard('admin')->check()){
            return redirect()->route($loginRoute);
        }
        return $next($request);
    }
}
