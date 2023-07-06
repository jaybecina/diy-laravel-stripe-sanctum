<?php

namespace App\Http\Controllers\Api\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Http\Requests\Auth\MoodleRegisterRequest;

class MoodleRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(MoodleRegisterRequest $request)
    {
        $client = new \GuzzleHttp\Client;

        DB::beginTransaction();

        try{
            $user = User::where('email', $request->email)->first();

            if(!$user) {
                return response(['status' => 400, 'message' => 'Error Moodle: Must have existing credentials for moodle registration'], 400);
            }

            $https_url = env("MOODLE_HTTPS_URL"). '/webservice/rest/server.php?wstoken=' .env("MOODLE_WSTOKEN"). '&wsfunction=auth_email_signup_user&moodlewsrestformat=json&firstname=' .$request->firstname. '&lastname=' .$request->lastname. '&email=' .$request->email. '&username=' .$user->username. '&password=' .$request->password;

            $response = $client->get($https_url);

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
            'status'   => 200,
            'data'     => json_decode($response->getBody()->getContents()),
            'message'  => 'User registered in successfully in moodle'
        ], 200);
        
    }
}
