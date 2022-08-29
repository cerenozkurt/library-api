<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LibraryRequest extends BaseFormRequest
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
            case 'userAddToLibrary':
                return [
                    'book_id' => ['required', 'exists:books,id'],
                    'status' => ['required','in:will_read,readed,reading'],

                ];
                break;
            case 'updateStatus':
                return [
                    'status' => ['in:will_read,readed,reading'],
                ];
                break;
            case 'updateComment':
                return [
                    'comment' => ['min:50','max:2000']
                ];

            case 'updatePoint':
                return [
                    'point' => ['integer', 'min:1','max:10'],
                ];
        }
    }
   
}
