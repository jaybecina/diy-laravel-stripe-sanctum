<?php

namespace App\Http\Controllers\Api\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Auth\MoodleLoginRequest;

class MoodleLoginController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(MoodleLoginRequest $request)
    {
        $client = new \GuzzleHttp\Client;

        $email = $request->email;

        if($request->email == "admin@admin.com") {
            $email = "hello@nxt.work";
        }

        $https_url = env("MOODLE_HTTPS_URL"). '/webservice/rest/server.php?wstoken=' .env("MOODLE_WSTOKEN"). '&wsfunction=auth_userkey_request_login_url&moodlewsrestformat=json&user[email]='.$email;

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if( !auth()->attempt( $credentials )) {
            return response()->json([
                'status' => 401,
                'message' => 'Error Moodle: Invalid login credentials'
            ], 401);
        }

        try{
            $response = $client->get($https_url);

            return response([
                'status'        => 200,
                'data'          => json_decode($response->getBody()->getContents()),
                'message'       => 'User logged in successfully in moodle'
            ], 200);
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            return response(['status' => 400, 'message' => $e->getMessage()], 400);
        }
        catch (\GuzzleHttp\Exception\ServerException $e) {
            return response(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }
}
