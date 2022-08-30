<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends ApiResponseController
{
    public function __construct()
    {
        $this->middleware('post.id.control')->only('show');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderby('created_at','desc')->paginate(10);
        return PostResource::collection($posts);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $user_id = auth()->user()->id;

        $post = Post::create([
            'user_id' => $user_id,
            'post' => $request->post,
            'title' =>  $request->title ?? null,
        ]);
        if ($post) {
            return $this->apiResponse(true, 'Post added.', 'post', new PostResource($post), JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, 'Post not added.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $post = Post::find($id);
        return $this->apiResponse(true, 'Post showed.', 'post', new PostResource($post), JsonResponse::HTTP_OK);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, $id)
    {

        $user_id = auth()->user()->id;

        $post = Post::find($id);

        $update = $post->update([
            'user_id' => $user_id,
            'title' =>  $request->title ?? $post->title,
            'post' =>  $request->post ?? $post->post
        ]);

        if ($update) {
            return $this->apiResponse(true, 'Post updated.', 'post', new PostResource($post), JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, 'Post not updated.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $delete = $post->delete();

        if ($delete) {
            return $this->apiResponse(true, 'Post deleted.', null, null, JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, 'Post not deleted.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    public function usersPosts($id)
    {
        $post = Post::where('user_id', $id)->get();
        return $this->apiResponse(true, 'Users posts.', 'posts', PostResource::collection($post), JsonResponse::HTTP_OK);

    }

    public function myPosts()
    {
        $user_id = auth()->user()->id;
        $post = Post::where('user_id', $user_id)->get();
        return $this->apiResponse(true, 'My posts.', 'posts', PostResource::collection($post), JsonResponse::HTTP_OK);

    }
    
}
