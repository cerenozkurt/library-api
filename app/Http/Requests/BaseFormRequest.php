<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BaseFormRequest extends FormRequest
{
     /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $apiresponse = app('App\Http\Controllers\ApiResponseController');

        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            $apiresponse->apiResponse(false, $validator->errors()->all(), null, null, JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}


