<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Http\Controllers\Api\Auth\Customer\MoodleRegisterController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\Api\Customer\SubscriptionController;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Exception;

class AuthController extends Controller
{
    //
    protected $moodle,$stripe,$subscription;

    public function __construct(){
        $this->moodle       = new MoodleRegisterController;
        $this->stripe       = new StripeController;
        $this->subscription = new SubscriptionController;
    }

    public function register(RegisterRequest $request)
    {
        // $generated_password = Str::random(8);

        DB::beginTransaction();

        try {
            // Validate, then create if valid
            $user = User::create([
                'first_name'    => $request->firstname,
                'last_name'     => $request->lastname,
                'email'         => $request->email,
                // 'username'      => null,
                'password'      => Hash::make($request->password),
                'mobile_num'    => $request->mobile_num,
                'address'       => $request->address,
                'updated_at'    => NULL
            ]);

            $customer_user = User::where('email', $user->email)->first();

            $card_details = [
                'user_id'       => $customer_user->id,
                'card_number'   =>$request->card_number,
                'exp_month'     =>$request->exp_month,
                'exp_year'      =>$request->exp_year,
                'cvc'           =>$request->cvc,
            ];


            if($customer_user->save()){
                // moodle registration

                // return $this->moodle->register_moodle();

                //    subscription process

                $payment_method = $this->stripe->create_payment_method($card_details);

                // return $payment_method->id;

                $sub_data = [
                    'user_id'       =>$user->id,
                    'email'         => $request->email,
                    'paymentMethod' => $payment_method->id, //visa as default temp
                    'plan_id'       => $request->plan_id,
                    'fullname'      => $request->firstname.' '.$request->lastname,
                    'postalCode'    => $request->postal_code
                ];

                // $subscription = new SubscriptionController;
                
                if($this->subscription->subscribe($sub_data)){

                    $this->stripe->create_card_token($card_details);
                }

                // return $sub_data;
            }

            $customer_user->assignRole(['Customer']);
            $customer_user->givePermissionTo(['can create', 'can read', 'can update']);
    
            $accessToken = $user->createToken('myAccount')->accessToken;

        } catch(\Exception $e)
        {
            DB::rollback();

            return response(['error' => $e->getMessage()], 400);

        } catch(\Throwable $e)
        {
            DB::rollback();

            return response(['error' => $e->getMessage()], 400);
        }

        DB::commit();

        // send mail 
        // try{
        //     $subject = "Registration Successful";
        //     $header = "You have successfully created an account.";
        //     $content = "Password: ".$generated_password;
            
        //     Mail::to($user->email)->send(new SendMail($subject,$header,$content));
        // }catch(Exception $e){
        //     return response(['error' => $e->getMessage()], 400);
        // }
        
        return response()->json([
            'message'=>'Registration successful.',
        ], 201);
    }
    
    public function login(Request $request){
        $user_exist = User::where('email',$request->email)->first();
        if(!$user_exist){
			return response()->json(["error" => "This email address is not subscribed"], Response::HTTP_UNAUTHORIZED);
        }

        if (!Auth::attempt($request->only("username", "email", "password"))) {
			return response()->json(["error" => "Invalid Credential"], Response::HTTP_UNAUTHORIZED);
		}

		$user = Auth::user();

		$token = $user->createToken("token")->plainTextToken;

		$cookie = cookie("jwt", $token,60 * 24); //1 day cookie expiration

		return response()->json(["user" => $user, "accessToken" => $token])->withCookie($cookie);
    }

    public function admin_login(Request $request){
        $user_exist = User::where('email',$request->email)->first();

        if(!$user_exist->hasRole('Super Admin')){
            return response()->json(['error'=>'You need to have admin access to proceed.'],401);
        }

        if(!$user_exist){
			return response()->json(["error" => "This email address is not subscribed"], Response::HTTP_UNAUTHORIZED);
        }

        if (!Auth::attempt($request->only("username", "email", "password"))) {
			return response()->json(["error" => "Invalid Credential"], Response::HTTP_UNAUTHORIZED);
		}

		$user = Auth::user();

		$token = $user->createToken("token")->plainTextToken;

		$cookie = cookie("jwt", $token,60 * 24); //1 day cookie expiration

		return response()->json(["user" => $user, "accessToken" => $token])->withCookie($cookie);
    }
    
    public function logout(){
        $cookie = Cookie:: forget('jwt');

        return response([
            'message'=>'Success'
        ])->withCookie($cookie);
    }

    public function forgot_password(Request $request){
        // check if email exist
        $user = User::where('email',$request->email)->first();
        
        if(!$user){
            return response(['error' => 'Email does not exist.'], 404);
        }

        $email = $request->email;
        
        $code = Str::upper(Str::random(5));
            
        DB::beginTransaction();

        try{
            $reset = new PasswordReset;
            $reset->email = $email;
            $reset->code = $code;
            if($reset->save()){
                try {
                    $subject = "Password Reset";
                    $header = "We've received a password reset request for the account associated with ".$email.'.';
                    // $content = "Reset Code: ".$code;
                    Mail::to($email)->send(new SendMail($subject,$header,'',$code,$email));

                    DB::commit();
            
                    return response()->json(['message' => 'Code has been sent, please check your email.'], 200);
                }catch(\Exception $e){
                    DB::rollBack();
                    return response(['error' => $e->getMessage()], 400);
                }
            }
        }catch(Exception $e){
            DB::rollBack();
            return response(['error' => $e->getMessage()], 400);
        }
    }

    public function validate_code(Request $request){
        $password_resets = PasswordReset::where('email',$request->email)->latest()->first();

        if($password_resets->code != $request->reset_code){
            return response(['error' => 'Invalid code.'], 400);
        }
        
        return response([],200);
    }

    public function change_password(Request $request){
        // check if the code sent is the latest
        $email = $request->email;
        // $code = $request->reset_code;
        $password = Hash::make($request->password);
        // $password_resets = PasswordReset::where('email',$email)->latest()->first();
        
        // if($password_resets->code != $code){
        //     return response(['error' => 'Invalid code.'], 400);
        // }
        DB::beginTransaction();
        try{
            $user = User::where('email',$email)->first();
            $user->password = $password;
            if($user->update()){
                DB::commit();
                return response()->json(['message' => 'Password updated successfully.'], 200);
            }
        }catch(Exception $e){
            DB::rollBack();
            return response(['error' => $e->getMessage()], 400);
        }

    }

    public function test(){
        return 'authenticated';
    }
}
