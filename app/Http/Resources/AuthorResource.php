<?php

namespace App\Http\Resources;

use App\Models\Media;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
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
            'photo' =>  $this->media_id ? asset('authors/' . Media::find($this->media_id)->filename) : null, 
            'read_count' => $this->read_count,
            'books' => BookResource::collection($this->books)->pluck('name'),
        ];
    }
}
