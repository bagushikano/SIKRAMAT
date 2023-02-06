<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->guard()->user() == NULL) {
            if(!in_array(auth()->guard()->user()->role, $roles)){
                return redirect()->back();
            }else{
                return $next($request);
            }  
        }else{
            return redirect(route('login-post'));
        }
    }
}
