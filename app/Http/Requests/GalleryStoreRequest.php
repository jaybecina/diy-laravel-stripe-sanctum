<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;

use Illuminate\Http\Exceptions\HttpResponseException;

class GalleryStoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'sku'               =>  'required|string',
            'name'              =>  'required|string',
            'colour'            =>  'required|string',
            'uid'               =>  'required|string',
            'src'               =>  'required|string',
            'length'            =>  'numeric',
            'width'             =>  'numeric',
            'height'            =>  'numeric',
            'tags'              =>  'string',
            'price'             =>  'numeric',
            'discount'          =>  'numeric',
            'quantity'          =>  'required|numeric',
            'has_supplier'      =>  'boolean'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        // $errors->messages()

        $error_message = Arr::flatten($errors->messages());

        $response = response()->json([
            'status' => 422,
            'message' => $error_message
        ], 422);

        throw new HttpResponseException($response);
    }
}
