<?php

namespace App\Http\Controllers\Api\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Http\Requests\Auth\RegisterRequest;

class RegisterController extends Controller
{
    /**
     * Registration Req
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Validate, then create if valid
            $user = User::create([
                'first_name' => $request->firstname,
                'last_name' => $request->lastname,
                'email' => $request->email,
                'username' => null,
                'password' => bcrypt($request->password),
                'mobile_num' => $request->mobile_num,
                'updated_at' => NULL
            ]);

            $admin_user = User::where('email', $user->email)->first();

            $admin_user->username = $admin_user->email.''.$admin_user->id;
            $admin_user->save();

            $admin_user->assignRole(['Admin']);
            $admin_user->givePermissionTo(['can create', 'can read', 'can update', 'can delete']);

            $accessToken = $user->createToken('myAccount')->accessToken;
        
        } catch(\Exception $e)
        {
            // Rollback and then redirect
            // back to form with errors
            DB::rollback();

            return response(['status' => 400, 'message' => $e->getMessage()], 400);

        } catch(\Throwable $e)
        {
            DB::rollback();

            return response(['status' => 400, 'message' => $e->getMessage()], 400);
        }

        DB::commit();

        return response()->json([
            'status'       => 200,
            'data'         => $user,
            'roles'        => $customer_user->getRoleNames(), 
            'accessToken'  => $accessToken
        ], 200);
    }

}
