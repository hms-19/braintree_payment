<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Currency;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(){

        $currencies = Currency::all();

        // client token
        $gateway = new \Braintree\Gateway([
            'environment' => env('BRAINTREE_ENVIRONMENT'),
            'merchantId' => env("BRAINTREE_MERCHANT_ID"),
            'publicKey' => env("BRAINTREE_PUBLIC_KEY"),
            'privateKey' => env("BRAINTREE_PRIVATE_KEY")
        ]);
        $clientToken = $gateway->clientToken()->generate();

        return view('payment',['currencies' => $currencies,'clientToken' => $clientToken]);
    }

    public function checkout_order(Request $request){

        $valid = Validator::make($request->all(),[
            'customer_name' => 'required',
            'price' => 'required',
            'currency_id' => 'required',
            'holder_name' => 'required'
        ]);

        if($valid->fails()){
            return response()->json([
                'errors' => $valid->errors()
            ]);
        }
        
        // DB::beginTransaction();

        try {
            $nonceFromTheClient = $request->input('nonce');

            $gateway = new \Braintree\Gateway([
                'environment' => env('BRAINTREE_ENVIRONMENT'),
                'merchantId' => env("BRAINTREE_MERCHANT_ID"),
                'publicKey' => env("BRAINTREE_PUBLIC_KEY"),
                'privateKey' => env("BRAINTREE_PRIVATE_KEY")
            ]);

            $user = $gateway->customer()->create([
                'firstName' => 'Mike',
                'lastName' => 'Jones',
                'company' => 'Jones Co.',
                'email' => 'mike.jones@example.com',
                'phone' => '281.330.8004',
                'fax' => '419.555.1235',
                'website' => 'http://example.com'
            ]);
        

            $paymentMethod = $gateway->paymentMethod()->create([
                'customerId' => $user->customer->id,
                'paymentMethodNonce' => $nonceFromTheClient
            ]);

            // Need to check payment method and currency


            $result = $gateway->transaction()->sale([
                'amount' => $request->price,
                'paymentMethodNonce' => $nonceFromTheClient,
                'options' => [
                    'submitForSettlement' => True
                ]
            ]);

            if($result->success){
                $order = new Order();
                $order->customer_name = $request->customer_name;
                $order->price = $request->customer_name;
                $order->currency_id = $request->currency_id;
                $order->holder_name = $request->customer_name;
                $order->card_no = $result->transaction->creditCard['bin'] + $result->transaction->creditCard['last4'];
                $order->card_cvv = '333';
                $order->expired_month = $result->transaction->creditCard['expirationMonth'];
                $order->expired_year = $result->transaction->creditCard['expirationYear'];
                
                $order->save();
            }

        } catch(ValidationException $e)
        {
            // DB::rollback();
            
            return response()->json([
                'error' => $e->message
            ]);
        }
        
       

        return response()->json([
            'success' => true,
            'message' => 'Payment Successfully',
            'data' => $result
        ]);
    }
    
}
