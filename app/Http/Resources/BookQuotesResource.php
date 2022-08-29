<?php

namespace App\Http\Resources;

use App\Models\Books;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class BookQuotesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => new UserResource(User::find($this->user_id)),
            'book' =>new BookResource(Books::find($this->book_id)),
            'title' => $this->title,
            'quotes' => $this->quotes,
            'page' => $this->page,
           
        ];
    }
}
