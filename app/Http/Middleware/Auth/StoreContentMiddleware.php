<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreContentMiddleware
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
            $rules = [
                'name' => ['required','max:50'] 
            ];

            $validation = Validator::make($request->all(), $rules);
            if($validation->fails()){
                return response()->json([
                    'status code' => 403,
                    'success' => false,
                    'message' => $validation->errors()->all(),
                ]);
            }
            else{
                return $next($request);
            }

        } catch (\Exception $e) {

                return response()->json([
                    'status code' => 400,
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            
            
        }
    }
}
