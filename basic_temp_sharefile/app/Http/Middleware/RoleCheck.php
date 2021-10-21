<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            // I included this check because you have it, but it really should be part of your 'auth' middleware, most likely added as part of a route group.
            return redirect('login');
        }

        $user = Auth::user();

        foreach ($roles as $role) {
            // Check if user has the role This check will depend on how your roles are set up
            if ($user->role_access === $role) {
                return $next($request);
            }
        }

        return redirect('/');
    }
}
