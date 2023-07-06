<?php

namespace App\Http\Controllers\Api\Auth\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Socialite;
use Laravel\Socialite\Two\User as ProviderUser;
use Str;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;

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
        $password = '';
        // If there is token it is from social
        if($request->token) {
            $password = Crypt::decryptString($request->password);
        } else {
            $password = $request->password;
        }

        $credentials = [
            'email' => $request->email,
            'password' => $password
        ];

        if( !auth()->attempt( $credentials )) {
            return response()->json([
                'status' => 401,
                'message' => 'Error Authentication: Invalid login credentials'
            ], 401);
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
     * Redirect the user to the Google authentication page.
     *
     * @return Response
     */
    public function google()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

     /**
     * Obtain the user information from Google.
     *
     * @return Response
     */
    public function google_redirect()
    {
        $user = Socialite::driver('google')->stateless()->user();
        // Auth::login($authUser, true);

        // OAuth 2.0 providers...
        $accessToken = $user->token;
        $refreshToken = $user->refreshToken;
        $expiresIn = $user->expiresIn;
        $userId = $user->getId();
        $userEmail = $user->getEmail();
        $password = ucfirst($user->getEmail()). '' .$user->getId();
        $enc_pw = Crypt::encryptString($password);
    
        // // OAuth 1.0 providers...
        // $token = $user->token;
        // $tokenSecret = $user->tokenSecret;
    
        // // All providers...
        // $user->getId();
        // $user->getNickname();
        // $user->getName();
        // $user->getEmail();
        // $user->getAvatar();

        DB::beginTransaction();

        try {
            $returnUser = null;

            $linkedUser = User::where('provider', 'google')
                ->where('provider_id', $user->getId())
                ->first();

            if ($linkedUser) {
                $returnUser = $linkedUser;
            } else {   

                User::create([
                    'first_name' => $user->getName(),
                    'last_name' => $user->getName(),
                    'username' => $user->getEmail(). '' .$user->getId(),
                    'token' => $accessToken,
                    'email' => $user->getEmail(),
                    'password' => bcrypt($password),
                    'provider_id' => $user->getId(),
                    'provider' => 'google',
                    'has_socialite' => true,
                ]);

                $customeruser = User::where('email', $user->email)->first();

                $customeruser->assignRole(['Customer']);
                $customeruser->givePermissionTo(['can create', 'can read', 'can update']);

                if($customeruser->has_moodle == false) {

                    // if you're submitting via get method then plus(+) is an issue in URL and replaced by white space.
                    //  You need to encode parameters before passing, so that it won't remove plus(+).
                    // The rawurlencoded will be sent into the url
                    if (strpos($customeruser->email, '+') != false) { 
                        $email = rawurlencode(str_replace('+', '%2B', $customeruser->email));
                    } else {
                        $email = $customeruser->email;
                    }

                    if (strpos($customeruser->username, '+') != false) { 
                        $username = rawurlencode(str_replace('+', '-', $customeruser->username));
                    } else {
                        $username = $customeruser->email;
                    }

                    // Moodle Register
                    $https_url = env("MOODLE_HTTPS_URL"). '/webservice/rest/server.php?wstoken=' .env("MOODLE_WSTOKEN"). '&wsfunction=auth_email_signup_user&moodlewsrestformat=json&firstname=' .$customeruser->first_name. '&lastname=' .$customeruser->last_name. '&email=' .$email. '&username=' .$username. '&password=' .$customeruser->password;

                    $client = new \GuzzleHttp\Client;

                    $response = $client->get($https_url);

                    if(!$response) {
                        return response(['status' => 400, 'message' => json_decode($response->getBody()->getContents())], 400);
                    }

                    $customeruser->has_moodle = true;
                    $customeruser->save();
                } 

                $returnUser = $customeruser;
            }
        
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

        // $data = [
        //     'user_id'       => $userId,
        //     'firstname'     => $returnUser->first_name, 
        //     'lastname'      => $returnUser->last_name, 
        //     'provider'      => $returnUser->provider,
        //     'provider_id'   => $returnUser->provider_id,
        //     'accessToken'   => $accessToken,
        //     'refreshToken'  => $refreshToken,
        //     'enc_password'      => $enc_pw
        // ];

        // return response([
        //     'status'        => 200,
        //     'password'      => $password,
        //     'enc_password'  => $enc_pw,
        //     'decrypt_pword' => Crypt::decryptString($enc_pw),
        //     'accessToken'   => $accessToken,
        //     'message'       => 'User logged in successfully'
        // ], 200);

        $url = env('REACT_APP_BASE_URL') . '/validate-social-login?email=' .$userEmail. '&pword=' .$enc_pw. '&token=' .$accessToken;

        return redirect()->away($url);

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
