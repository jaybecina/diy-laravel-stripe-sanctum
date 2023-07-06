<?php

namespace App\Http\Controllers\Api\Auth\Customer;

use App\Http\Controllers\Api\Auth\Customer\MoodleRegisterController;
use App\Http\Controllers\Api\Customer\SubscriptionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\StripeController;
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
    protected $moodle,$stripe,$subscription;

    public function __construct(){
        $this->moodle       = new MoodleRegisterController;
        $this->stripe       = new StripeController;
        $this->subscription = new SubscriptionController;
    }
    /**
     * Registration Req
     */
    // public function register(RegisterRequest $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Validate, then create if valid
    //         $user = User::create([
    //             'first_name' => $request->firstname,
    //             'last_name' => $request->lastname,
    //             'email' => $request->email,
    //             'username' => null,
    //             'password' => bcrypt($request->password),
    //             'mobile_num' => $request->mobile_num,
    //             'address' => $request->address,
    //             'updated_at' => NULL
    //         ]);

    //         $customer_user = User::where('email', $user->email)->first();

    //         $customer_user->username = $customer_user->email.''.$customer_user->id;
    //         $customer_user->save();

    //         $customer_user->assignRole(['Customer']);
    //         $customer_user->givePermissionTo(['can create', 'can read', 'can update']);
    
    //         $accessToken = $user->createToken('myAccount')->accessToken;

    //     } catch(\Exception $e)
    //     {
    //         // Rollback and then redirect
    //         // back to form with errors
    //         DB::rollback();

    //         return response(['status' => 400, 'message' => $e->getMessage()], 400);

    //     } catch(\Throwable $e)
    //     {
    //         DB::rollback();

    //         return response(['status' => 400, 'message' => $e->getMessage()], 400);
    //     }

    //     DB::commit();

    //     return response()->json([
    //         'status' => 200,
    //         'data'   => $user,
    //         'roles'  => $customer_user->getRoleNames(), 
    //         'accessToken'  => $accessToken
    //     ], 200);
    // }

    public function register(RegisterRequest $request)
    {
        // return $this->moodle->register_moodle();
        DB::beginTransaction();

        try {
            // Validate, then create if valid
            $user = User::create([
                'first_name'    => $request->firstname,
                'last_name'     => $request->lastname,
                'email'         => $request->email,
                'username'      => null,
                'password'      => bcrypt($request->password),
                'mobile_num'    => $request->mobile_num,
                'address'       => $request->address,
                'updated_at'    => NULL
            ]);

            $customer_user = User::where('email', $user->email)->first();

            $customer_user->username = $customer_user->email.''.$customer_user->id;

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
            'message'=>'success'
            // 'status' => 200,
            // 'data'   => $user,
            // 'roles'  => $customer_user->getRoleNames(), 
            // 'accessToken'  => $accessToken
        ], 200);
    }

}
