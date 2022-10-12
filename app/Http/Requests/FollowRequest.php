<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FollowRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'friend_id' => ['required', 'integer', 'exists:users,id']
        ];
    }
}
