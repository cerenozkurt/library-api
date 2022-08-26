<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class BookRequest extends BaseFormRequest
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
                    'isbn' => ['required', 'digits:13'],
                    'name' => ['required', 'min:5', 'max:100'],
                    'page_count' => ['required', 'integer', 'max:9000'],
                    'publisher_id' => ['required', 'exists:publishers,id'],
                    'category_id' => ['required', 'exists:categories,id'],
                    'author_id' => ['required', 'exists:authors,id']
                ];
                break;
            case 'update':
                return [
                    'isbn' => ['digits:13'],
                    'name' => ['min:5', 'max:100'],
                    'page_count' => ['integer', 'max:9000'],
                    'publisher_id' => ['exists:publishers,id'],
                    'category_id' => ['exists:categories,id'],
                    'author_id' => ['exists:authors,id']
                ];
                break;
            case 'uploadBookPicture':
                return [
                    'image' => 'required|image',
                ];
                break;
        }
    }
}
