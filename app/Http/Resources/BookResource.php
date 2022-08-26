<?php

namespace App\Http\Resources;

use App\Models\Author;
use App\Models\Category;
use App\Models\Media;
use App\Models\Publisher;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'name' => $this->name,
            'isbn' => $this->isbn,
            'page_count' => $this->page_count,
            'author' => Author::find($this->author_id)->name,
            'category' => Category::find($this->category_id)->name,
            'publisher' => Publisher::find($this->publisher_id)->name,
            'read_count' => $this->read_count,
            'photo' =>  $this->media_id ? asset('books/' . Media::find($this->media_id)->filename) : null, 
        ];
    }
}
