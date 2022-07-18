<?php

namespace App\Http\Resources;

use App\Models\Books;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBookResource extends JsonResource
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

            'books' => new BookResource(Books::find($this->book_id))
                                  
        ];
    }
}
