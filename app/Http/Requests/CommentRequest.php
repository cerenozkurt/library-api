<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CommentRequest extends BaseFormRequest
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
            case 'postStore':
                return [
                    'comment' => ['required', 'max:300']
                ];
                break;
            case 'bookQuotesStore':
                return [
                    'comment' => ['required', 'max:300']
                ];
                break;
            case 'update':
                return [
                    'comment' => ['max:300']
                ];
                break;
        }
    }
}
