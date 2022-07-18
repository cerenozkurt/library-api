<?php

namespace App\Http\Middleware\Auth;

use App\Models\Publisher;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PublisherIdControlMiddleware
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
            $publisher = Publisher::findorfail($request->route('publisher'));
            if ($publisher) {
                return $next($request);
            }
            else{
                return response()->json([
                    'status_code' => 500,
                    'success' => false,
                    'message' => "Something went wrong",
                ]);
            }
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'status code' => 404,
                    'success' => false,
                    'error' => 'publisher Not Found',
                ]);
            } else {
                
                return response()->json([
                    'status code' => 401,
                    'succes' => false,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
}
