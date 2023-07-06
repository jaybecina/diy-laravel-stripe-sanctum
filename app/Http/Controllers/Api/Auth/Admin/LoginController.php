<?php

namespace App\Http\Controllers\Api\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Socialite;
use Str;

use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if( !auth()->attempt( $credentials )) {
            return response()->json([
                'status' => 401,
                'message' => 'Error Authentication: Invalid login credentials',
                'details' => 'Authentication failed'
            ], 401);
        }

        $role = auth()->user()->getRoleNames();

        $admin_roles = ["Super Admin", "Admin"];

        if (!in_array($role[0], $admin_roles)) {
            return response()->json([
                'status' => 403,
                'message' => 'Error Authentication: Unauthorized account'
            ], 403);
        }

        $accessToken = auth()->user()->createToken('auth_token')->accessToken;

        return response([
            'status'        => 200,
            'data'          => auth()->user(), 
            'roles'          => auth()->user()->getRoleNames(), 
            'accessToken'   => $accessToken,
            'message'       => 'User logged in successfully'
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function google(LoginRequest $request)
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function google_redirect(LoginRequest $request)
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function apple(LoginRequest $request)
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function apple_redirect(LoginRequest $request)
    {
        //
    }
}