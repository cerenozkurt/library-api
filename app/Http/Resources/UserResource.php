<?php

namespace App\Http\Resources;

use App\Models\Media;
use App\Models\UserRoles;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'mail' => $this->email,
            'photo' =>  $this->media_id ? asset('profile/' . Media::find($this->media_id)->filename) : null, 
            'role' => UserRoles::find($this->role_id)->name
        ];
    }
}
