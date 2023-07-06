<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardDetailResource;
use App\Models\CardDetail;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    //
    protected $stripe;

    public function __construct() 
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    }

    public function get_card_details($id){
        $card_details = new CardDetailResource(CardDetail::findOrFail($id));

        return $card_details;
    }

    public function create_payment_method($card_details){
        $payment_method = $this->stripe->paymentMethods->create([
            'type' => 'card',
            'card'=>[
                'number'=> $card_details['card_number'],
                'exp_month'=> $card_details['exp_month'],
                'exp_year'=> $card_details['exp_year'],
                'cvc'=> $card_details['cvc'],
            ]
          ]);

        return $payment_method;
    }

    public function create_card_token($card_details){
        $card_token = $this->stripe->tokens->create([
            'card'=>[
                'number'=> $card_details['card_number'],
                'exp_month'=> $card_details['exp_month'],
                'exp_year'=> $card_details['exp_year'],
                'cvc'=> $card_details['cvc'],
            ]
        ]);

        $card_detail = new CardDetail;
        $card_detail->user_id = $card_details['user_id'];
        $card_detail->card_credentials = json_encode($card_token);
        $card_detail->is_default = 1;
        $card_detail->save();
    }
}
