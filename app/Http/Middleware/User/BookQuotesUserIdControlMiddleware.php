<?php

namespace App\Http\Middleware\User;

use App\Models\BookQuotes;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookQuotesUserIdControlMiddleware
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
        $apiresponse = app('App\Http\Controllers\ApiResponseController');
        $user_id = auth()->user()->id;
        $quotes = BookQuotes::where('user_id', $user_id)->pluck('id');
        if (in_array($request->route('quotes'),  $quotes->toArray())) {
            return $next($request);
        }
        return $apiresponse->apiResponse(false, 'Book quotes not found.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }
}
