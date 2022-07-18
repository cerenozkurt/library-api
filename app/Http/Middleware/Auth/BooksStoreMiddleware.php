<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BooksStoreMiddleware
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
                'isbn' => ['required', 'digits:13'],
                'name' => ['required', 'min:5', 'max:100'],
                'page_count' => ['required', 'integer', 'max:9000'],
                'publisher_id' => ['required', 'exists:publishers,id'],
                'category_id' => ['required', 'exists:categories,id'],
                'author_id' => ['required', 'exists:authors,id']

            ];

            $validation = Validator::make($request->all(), $rules);
            if ($validation->fails()) {
                return response()->json([
                    'status code' => 403,
                    'success' => false,
                    'message' => $validation->errors()->all(),
                ]);
            } else {
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
