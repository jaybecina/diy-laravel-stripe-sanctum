<?php

namespace App\Http\Controllers\Api\Auth\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//custom
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Mail\ResetPasswordMail;

use App\Http\Requests\Auth\ForgotPasswordRequest;

class ForgotPasswordController extends Controller
{
    protected function sendResetLinkResponse(ForgotPasswordRequest $request)
    {
        $email = $request->only('email');

        $token = Str::random(60);

        $base_url = env('REACT_APP_BASE_URL');

        try {
            Mail::to($email)->send(new ResetPasswordMail($email['email'], $token, 'customer'));

            $response = [
                'status' => 200, 
                'data' => '',
                'message' => "Success! password reset link has been sent to your email"
            ];
    
            return response()->json($response, 200);
        }catch(\Exception $e){
            return response(['status' => 400, 'message' => $e->getMessage()], 400);
        }
    }
}
