<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends ApiResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postStore(CommentRequest $request, $id)
    {
        $user_id = auth()->user()->id;
        

        $comment = Comment::create([
            'user_id' => $user_id,
            'post_id' => $id,
            'book_quotes_id' => null,
            'comment' => $request->comment
        ]);

        if($comment){
            return $this->apiResponse(true, 'Comment added.', 'comment', new CommentResource($comment), JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, 'Comment no added.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    public function bookQuotesStore(CommentRequest $request, $id)
    {
        $user_id = auth()->user()->id;
        
        $comment = Comment::create([
            'user_id' => $user_id,
            'post_id' => null,
            'book_quotes_id' => $id,
            'comment' => $request->comment
        ]);

        if($comment){
            return $this->apiResponse(true, 'Comment added.', 'comment', new CommentResource($comment), JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, 'Comment no added.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = Comment::find($id);
        if($comment){
            return $this->apiResponse(true, 'Comment showed.', 'comment', new CommentResource($comment), JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, 'Comment no showed.', null, null, JsonResponse::HTTP_NOT_FOUND);
        
    }

    public function update(CommentRequest $request, $id)
    {
        
        $comment = Comment::find($id);
        $comment->comment = $request->comment ?? $comment->comment;
        $comment->save();
        if($comment){
            return $this->apiResponse(true, 'Comment updated.', 'comment', new CommentResource($comment), JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, 'Comment no updated.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);
        $comment->delete();

        if($comment){
            return $this->apiResponse(true, 'Comment deleted.', null, null, JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, 'Comment no deleted.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    public function commentsOfThePost($id)
    {
        $comments =  Comment::where('post_id',$id)->orderby('created_at')->get();

        return $this->apiResponse(true, 'Post comments', 'comments', CommentResource::collection($comments), JsonResponse::HTTP_OK);
    }

    public function commentsOfTheQuotes($id)
    {
        $comments =  Comment::where('book_quotes_id',$id)->orderby('created_at')->get();

        return $this->apiResponse(true, 'Book Quotes comments', 'comments', CommentResource::collection($comments), JsonResponse::HTTP_OK);
    }

    public function userComments($id)
    {
        $comments =  Comment::where('user_id',$id)->orderby('created_at')->get();

        return $this->apiResponse(true, 'Users comments', 'comments', CommentResource::collection($comments), JsonResponse::HTTP_OK);
    }
}
