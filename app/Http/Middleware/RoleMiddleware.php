<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, ... $roles)
    {
    
        $user = Auth::user();
        dd($user->hasRole('admin'));
        foreach($roles as $role) {
            if($user->hasRole($role))
                return $next($request);
        }
    
        return response()->json('Role guard', 401);
    }
}

