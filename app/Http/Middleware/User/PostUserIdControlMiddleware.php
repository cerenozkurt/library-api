<?php

namespace App\Http\Middleware\User;

use App\Models\Post;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostUserIdControlMiddleware
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
        $post = Post::where('user_id', $user_id)->pluck('id');
        if (in_array($request->route('post'),  $post->toArray())) {
            return $next($request);
        }
        return $apiresponse->apiResponse(false, 'Post not found.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }
}
