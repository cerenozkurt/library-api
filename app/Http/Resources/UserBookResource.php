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
            'status' => $this->read_status($this->status),
            'book' => new BookResource(Books::find($this->book_id)),
            'point' => $this->point,
            'comment' => $this->comment
        ];
    }

    public function read_status($status)
    {
        switch ($status) {
            case 'will_read':
                return 'I will read';
                break;
            case 'readed':
                return 'I read';
                break;
            case 'reading':
                return 'I reading';
                break;
        }
    }
}
