<?php

namespace App\Http\Controllers\Api\Auth\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Http\Requests\Auth\MoodleRegisterRequest;
use Illuminate\Support\Facades\Http;

class MoodleRegisterController extends Controller
{
    protected $moodle_api;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->moodle_api = env("MOODLE_HTTPS_URL");
    }
    
    public function register_moodle($user = []){
        return $this->moodle_api;
    }

    public function register(MoodleRegisterRequest $request)
    {
        $client = new \GuzzleHttp\Client;

        DB::beginTransaction();

        try{
            $user = User::where('email', $request->email)->first();

            if(!$user) {
                return response(['status' => 400, 'message' => 'Error Moodle: Must have existing credentials for moodle registration'], 400);
            }

            // if you're submitting via get method then plus(+) is an issue in URL and replaced by white space.
            //  You need to encode parameters before passing, so that it won't remove plus(+).
            // The rawurlencoded will be sent into the url
            if (strpos($request->email, '+') != false) { 
                $email = rawurlencode(str_replace('+', '%2B', $request->email));
            } else {
                $email = $request->email;
            }

            if (strpos($user->username, '+') != false) { 
                $username = rawurlencode(str_replace('+', '-', $user->username));
            } else {
                $username = $request->email;
            }

            $https_url = env("MOODLE_HTTPS_URL"). '/webservice/rest/server.php?wstoken=' .env("MOODLE_WSTOKEN"). '&wsfunction=auth_email_signup_user&moodlewsrestformat=json&firstname=' .$request->firstname. '&lastname=' .$request->lastname. '&email=' .$email. '&username=' .$username. '&password=' .$request->password;

            $response = $client->get($https_url);

            if(!$response) {
                return response(['status' => 400, 'message' => json_decode($response->getBody()->getContents())], 400);
            }

            $user->has_moodle = true;
            $user->save();
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {

            // Rollback and then redirect
            // back to form with errors
            DB::rollback();

            return response(['status' => 400, 'message' => $e->getMessage()], 400);
        }
        catch (\GuzzleHttp\Exception\ServerException $e) {

            DB::rollback();

            return response(['status' => 500, 'message' => $e->getMessage()], 500);
        }

        DB::commit();

        return response([
            'status'  => 200,
            'data'    => json_decode($response->getBody()->getContents()),
            'message' => 'User registered in successfully in moodle'
        ], 200);
        
    }
}
