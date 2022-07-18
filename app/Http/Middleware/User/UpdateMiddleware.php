<?php

namespace App\Http\Middleware\User;

use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UpdateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = User::findorfail($request->route('user'));
            
            if (User::where('email', '=', $request->email)->first() == null || $user->email == $request->email) {
                return $next($request);
            } else {
                return response()->json([
                    'status code' => 401,
                    'success' => false,
                    'message' => 'Email already exists.',
                ]);
            }
        } catch (\Exception $e) {

            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'status code' => 404,
                    'success' => false,
                    'error' => 'ModelNotFoundException | User Not Found',
                ]);
                return response()->json([
                    'status code' => 401,
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            }
        }


        //

    }
}
