<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;
use Stripe;
use Illuminate\Support\Facades\Hash;

use App\Models\Plan;
use App\Models\User;

use App\Http\Requests\CheckSubscriptionStatusRequest;
use App\Http\Requests\SubscriptionStoreRequest;
use App\Http\Requests\SubscriptionUpdateRequest;
use App\Http\Requests\SubscriptionCancelRequest;

class SubscriptionController extends Controller
{
    protected $stripe;

    public function __construct() 
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function check_subscription_status(CheckSubscriptionStatusRequest $request)
    {
        try {
            $email = $request->only('email');
            $user = User::where('email', $email)->first();

            if(!empty($user)) {
                if($user->subscriptions->count() > 0) {
                    $subscription = $user->subscriptions()->latest()->first();

                    $stripe_var = $user->subscriptions()->first();
                    $plan = Plan::where('stripe_plan', $stripe_var->stripe_price)->first();
                    $mergedData = [
                        'plan'         => $plan,
                        'subscription' => $subscription
                    ];
                    $message= 'This account is already subscribed';
                } else {
                    return response(['status' => 400, 'message' => 'Account not subscribed!'], 400);
                }
                // $subscription = $user->subscription('default')->stripe_plan;
            } else {
                return response(['status' => 400, 'message' => 'Account not found!'], 400);
            }

        } catch(\Exception $e) {
            return response(['status' => 400, 'message' => $e->getMessage()], 400);

        } catch(\Throwable $e) {
            return response(['status' => 400, 'message' => $e->getMessage()], 400);
        }
        
        return response()->json([
            'status' => 200,
            'data' => $mergedData,
            'message' => $message
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function subscribe($sub_data){
        $plan = Plan::findOrFail($sub_data['plan_id']);
        // $user = $request->user();
        $user = User::find($sub_data['user_id']);
        $paymentMethod = $sub_data['paymentMethod'];

        $user->createOrGetStripeCustomer();
        $user->updateDefaultPaymentMethod($paymentMethod);
        $user->newSubscription('default', $plan->stripe_plan)
            ->create($paymentMethod, [
                'email'     => $sub_data['email'],
                'fullname'  => $sub_data['fullname'],
                'postalCode' => $sub_data['postalCode']
            ]);
            
        // $mergedData = [
        //     'plan'         => $plan,
        //     'subscription' => $subscription
        // ];
        
        // return response()->json([
        //     'status'   => 200,
        //     'data'     => $mergedData,
        //     'message'  => 'Your plan subscribed successfully'
        // ], 200);
        return true;
    }
    public function store(SubscriptionStoreRequest $request)
    {
        $plan = Plan::findOrFail($request->plan_id);
        
        $user = $request->user();
        $paymentMethod = $request->paymentMethod;

        $user->createOrGetStripeCustomer();
        $user->updateDefaultPaymentMethod($paymentMethod);
        $subscription = $user->newSubscription('default', $plan->stripe_plan)
            ->create($paymentMethod, [
                'email'     => $user->email,
                'fullname'  => $request->fullname,
                'postalCode' => $request->postalCode
            ]);

        $mergedData = [
            'plan'         => $plan,
            'subscription' => $subscription
        ];
        
        return response()->json([
            'status'   => 200,
            'data'     => $mergedData,
            'message'  => 'Your plan subscribed successfully'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(SubscriptionUpdateRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        $subscription = $user->subscription('default')->noProrate()->swap($request->stripe_plan);
        // $subscription = $user->newSubscription('default', $plan->stripe_plan)
        //     ->create($paymentMethod, [
        //         'email'     => $user->email,
        //         'fullname'  => $request->fullname,
        //         'postalCode' => $request->postalCode
        //     ]);

        $plan = Plan::where('stripe_plan', $request->stripe_plan)->first();

        $mergedData = [
            'plan'         => $plan,
            'subscription' => $subscription
        ];
        
        return response()->json([
            'status'   => 200,
            'data'     => $mergedData,
            'message'  => 'Your plan updated successfully'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancel(SubscriptionCancelRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        $plan = Plan::where('stripe_plan', $request->stripe_plan)->first();

        if(empty($user)) {
            return response(['status' => 400, 'message' => 'No user found!'], 400);
        }

        if(empty($plan)) {
            return response(['status' => 400, 'message' => 'No plan found!'], 400);
        }

        $subscription = $user->subscription('default')->cancelNow();
        
        // $subscription = $user->newSubscription('default', $plan->stripe_plan)
        //     ->create($paymentMethod, [
        //         'email'     => $user->email,
        //         'fullname'  => $request->fullname,
        //         'postalCode' => $request->postalCode
        //     ]);
        
        return response()->json([
            'status'   => 200,
            'data'     => $subscription,
            'message'  => 'Your plan cancelled successfully'
        ], 200);
    }
}
