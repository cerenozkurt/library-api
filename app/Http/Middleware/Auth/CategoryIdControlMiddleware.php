<?php

namespace App\Http\Middleware\Auth;

use App\Models\Category;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CategoryIdControlMiddleware
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
            $category = Category::findorfail($request->route('category'));
            if ($category) {
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
                    'error' => 'Category Not Found',
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
