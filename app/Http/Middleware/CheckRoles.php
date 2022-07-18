<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        $user = Auth::user();
        $role = explode('|',$roles);

         if (in_array($user->role_id, $role)) {

           return $next($request);
          
        }
        else{
            return response()->json([
                'status code' => 401,
                'success' => false,
                'message' => 'Unauthorized login attempt.']);
        }
       

    }
}
