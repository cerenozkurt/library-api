<?php

namespace App\Http\Resources;

use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'post_id' => $this->id, 
            'title' => $this->title == null ? '' : $this->title,
            'comment' => $this->post,
            'user' => new UserResource(User::find($this->user_id)),
            'like' => [
                'count' => Like::where('post_id',$this->id)->count(),
                'users' => UserResource::collection(User::wherein('id',Like::where('post_id',$this->id)->select('user_id')->get())->get())
            ]

        ];
    }
}
