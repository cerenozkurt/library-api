<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PostRequest extends BaseFormRequest
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
    public function rules(Request $request)
    {
        switch ($request->route()->getActionMethod()) {
            case 'store':
                return [
                    //'user_id' =>  ['integer',  'exists:users,id'],
                    'post' => ['required', 'max:3000',],
                    'title' => ['max:150']
                ];
                break;
            case 'update':
                return [
                    //'user_id' =>  ['integer',  'exists:users,id'],
                    'post' => ['max:3000',],
                    'title' => ['max:150']
                ];
                break;
        }
    }
}
