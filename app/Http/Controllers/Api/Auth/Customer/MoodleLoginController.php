<?php

namespace App\Http\Controllers\Api\Auth\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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
 
        // if you're submitting via get method then plus(+) is an issue in URL and replaced by white space.
        //  You need to encode parameters before passing, so that it won't remove plus(+).
        // The rawurlencoded will be sent into the url
        if (strpos($request->email, '+') != false) { 
            $email = rawurlencode(str_replace('+', '%2B', $request->email));
        } else {
            $email = $request->email;
        }

        $https_url = env("MOODLE_HTTPS_URL"). '/webservice/rest/server.php?wstoken=' .env("MOODLE_WSTOKEN"). '&wsfunction=auth_userkey_request_login_url&moodlewsrestformat=json&user[email]=' .$email;

        // If there is token it is from social
        if($request->token) {
            $password = Crypt::decryptString($request->password);
        } else {
            $password = $request->password;
        }

        $credentials = [
            'email' => $request->email,
            'password' => $password,
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
            ]);
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            return response(['status' => 400, 'message' => $e->getMessage()], 400);
        }
        catch (\GuzzleHttp\Exception\ServerException $e) {
            return response(['status' => 500, 'message' => $e->getMessage()], 500);
        }
        
    }
}
