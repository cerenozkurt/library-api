<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

class ProfileRequest extends BaseFormRequest
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
            case 'uploadProfilePicture':
                return [
                    'image' => 'required|image',
                ];
                break;
           
           
        }
    }
}
