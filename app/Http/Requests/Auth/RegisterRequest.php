<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;

use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'firstname'             =>  'required|string',
            'lastname'              =>  'required|string',
            'email'                 =>  'required|email|unique:users',
            'password'              =>  [
                'required',
                'string',
                'min:8',
                // 'confirmed',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            // 'password_confirmation' =>  'required|same:password',
            'mobile_num'            =>  'required|min:10',
            'address'               =>  'required|string|min:5',
            // card creds
            'plan_id'               =>  'required|string',
            'card_number'           =>  'required|string',
            'exp_month'             =>  'required|string',
            'exp_year'              =>  'required|string',
            'cvc'                   =>  'required|string',
            
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
