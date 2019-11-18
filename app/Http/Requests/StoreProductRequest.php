<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string',
            'price'=> 'required|numeric|major'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => [
                'code' => 'Error-1',
                'title' => 'A name is required'
            ],
            'price.required' => [
                'code' => 'Error-1',
                'title' => 'A price is required'
            ],
            'price.numeric' => [
                'code' => 'Error-1',
                'title' => 'The price has to be numeric'
            ],
            'price.major' => [
                'code' => 'Error-1',
                'title' => 'The price has to be more than 0 (zero)'
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator)) -> errors();
        $errorMessage = '';

        foreach ($errors as $message) {
            $errorMessage = $message;
        }

        throw new HttpResponseException(
            response()->json(['errors' => $errorMessage], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
