<?php

namespace App\Http\Resources;

use App\Models\BookQuotes;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        switch ($request->route()->getActionMethod()) {
            case 'postStore':
                return [
                    'post' => new PostResource(Post::find($this->post_id)),
                    'comment' => $this->comment
                ];
                break;
            case 'bookQuotesStore':
                return [
                    'book_quotes' => new BookQuotesResource(BookQuotes::find($this->book_quotes_id)),
                    'comment' => $this->comment
                ];
                break;
            case 'update' || 'show':
                /*if($this->post_id == null){
                    return [
                        'book_quotes' => new BookQuotesResource(BookQuotes::find($this->book_quotes_id)),
                        'comment' => $this->comment
                    ];
                }
                return [
                    'post' => new PostResource(Post::find($this->post_id)),
                    'comment' => $this->comment
                 ];*/
                return [
                    'user' => new UserResource(User::find($this->user_id)),
                    'comment' => $this->comment
                ];
        }
    }
}
