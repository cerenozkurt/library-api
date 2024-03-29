<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserAuthRequest extends BaseFormRequest
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
            case 'register':
                return [
                    'name' => ['required', 'string'],
                    'email' => ['required', 'string', 'email', 'unique:users,email'],
                    'password' => ['required', 'string'],
                ];
                break;
            case 'login':
                return [
                    'password' => ['required', 'string'],
                    'email' => ['required', 'string', 'email'],
                ];
                break;
            case 'update':
                return [
                    'name' => ['string'],
                    'email' => ['string', 'email'],
                    'password' => ['string'],
                ];
                break;
            case 'editProfile':
                return [
                    'name' => ['string'],
                    'email' => ['string', 'email'],
                    'password' => ['string'],
                ];
                break;
            case 'roleAssignment':
                return [
                    'role_id' => ['required', 'exists:user_roles,id']
                ];
                break;
           
        }
    }


}
