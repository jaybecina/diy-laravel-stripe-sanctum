<?php

namespace App\Http\Controllers\Api\Auth\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;

//custom
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Arr;
//use Illuminate\Support\Str;

use App\Models\User;
use App\Http\Requests\Auth\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    protected function sendResetResponse(ResetPasswordRequest $request){
        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user['password'] = Hash::make($request->password);
                $user->save();

                DB::commit();

                return response()->json([
                    'status' => 200,
                    'data' => '',
                    'message' => "Success! password has been changed"
                ], 200);
            } else {
                // Rollback and then redirect
                // back to form with errors
                DB::rollback();

                $response = response()->json([
                    'status' => 422,
                    'message' => $e->getMessage()
                ], 422);
            }

        } catch(\Exception $e) {
            // Rollback and then redirect
            // back to form with errors
            DB::rollback();

            $response = response()->json([
                'status' => 422,
                'message' => $e->getMessage()
            ], 422);

            throw new HttpResponseException($response);

        } catch(\Throwable $e) {
            DB::rollback();

            $response = response()->json([
                'status' => 422,
                'message' => $e->getMessage()
            ], 422);

            throw new HttpResponseException($response);
        }
    }
}
