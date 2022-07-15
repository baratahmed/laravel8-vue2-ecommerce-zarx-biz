<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Processing;
use App\Models\Product;
use Illuminate\Http\Request;
use Stripe;

class CartsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.checkout');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Getting Product Details
        if(!isset($request->product_id)){

            return [
                'message' => 'Cart Items Returned',
                'items'   => Cart::where('user_id',auth()->id())->sum('quantity')
            ];        
        }

        $product = Product::find($request->product_id);
        // Check item is in cart or not
        $productFoundInCart = Cart::select('id')->where('product_id',$request->product_id)->get();

        if($productFoundInCart->isEmpty()){
            $cart =  Cart::create([ 
                'product_id' => $product->id,
                'quantity'   => 1,
                'price'      => $product->sale_price,
                'user_id'    => auth()->id(),
            ]);
        }else{
            // Incrementing Product Quantity
            $cart = Cart::where('product_id',$request->product_id)->increment('quantity');
        }        
        
        // Check user cart items
        $userItems = Cart::where('user_id',auth()->id())->sum('quantity');

        if($cart){
            return [
                'message' => 'Cart Updated',
                'items'   => $userItems
            ];
        }

        // dd($product);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getCartItemsForCheckout()
    {
        $cartItems = Cart::with('product')->where('user_id', auth()->user()->id)->get();

        $finalData = [];

        $amount = 0;

        if(isset($cartItems))
        {
            foreach($cartItems as $cartItem)
            {
                if($cartItem->product)
                {
                    if($cartItem->product->id == $cartItem->product_id)
                    {
                        $finalData[$cartItem->product_id]['id'] = $cartItem->product_id;
                        $finalData[$cartItem->product_id]['name'] = $cartItem->product->name;
                        $finalData[$cartItem->product_id]['quantity'] = $cartItem->quantity;
                        $finalData[$cartItem->product_id]['sale_price'] = $cartItem->price;
                        $finalData[$cartItem->product_id]['total'] = $cartItem->price * $cartItem->quantity;
                        $amount += $cartItem->price * $cartItem->quantity;
                        $finalData['totalAmount'] = $amount;
                    }                    
                }
            }
        }

        return response()->json($finalData);
    }


    public function processPayment(Request $request)
    {
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $address = $request->get('address');
        $city = $request->get('city');
        $state = $request->get('state');
        $zipCode = $request->get('zipCode');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $country = $request->get('country');
        $cardType = $request->get('cardType');
        $expirationMonth = $request->get('expirationMonth');
        $expirationYear = $request->get('expirationYear');
        $cvv = $request->get('cvv');
        $cardNumber = $request->get('cardNumber');

        $amount = $request->get('amount');

        $orders = $request->get('order');
        $ordersArray = [];

        // Getting Order Details.

        foreach($orders as $order)
        {
            if(isset($order['id']))
            {
                $ordersArray[$order['id']]['order_id'] = $order['id'];
                $ordersArray[$order['id']]['quantity'] = $order['quantity'];
            }
        }
 
       
        // Process payment.

        $stripe = Stripe::make(env('STRIPE_KEY') ?? 'sk_test_51IyHqaBTGpLS02saT35DzKHxN5NauyOhB7cde8vSuHS8pHTYxH2TbtygIXVTTMSoK9ON5qLzTiRbfBKUj63nyezK00jleGAPxH');


        $token = $stripe->tokens()->create([
            'card' => [
                'number' => $cardNumber,
                'exp_month' => $expirationMonth,
                'exp_year' => $expirationYear,
                'cvc'=> $cvv,
            ]]
        );


        // dd($token); 
        // array:8 [
        //     "id" => "tok_1LLTfABTGpLS02saZn88LA1A"
        //     "object" => "token"
        //     "card" => array:22 [
        //       "id" => "card_1LLTfABTGpLS02sah6ilmAx0"
        //       "object" => "card"
        //       "address_city" => null
        //       "address_country" => null
        //       "address_line1" => null
        //       "address_line1_check" => null
        //       "address_line2" => null
        //       "address_state" => null
        //       "address_zip" => null
        //       "address_zip_check" => null
        //       "brand" => "Visa"
        //       "country" => "US"
        //       "cvc_check" => "unchecked"
        //       "dynamic_last4" => null
        //       "exp_month" => 2
        //       "exp_year" => 2024
        //       "fingerprint" => "MBp59zsRwb0dmhfa"
        //       "funding" => "credit"
        //       "last4" => "4242"
        //       "metadata" => []
        //       "name" => null
        //       "tokenization_method" => null
        //     ]
        //     "client_ip" => "103.73.106.241"
        //     "created" => 1657811352
        //     "livemode" => false
        //     "type" => "card"
        //     "used" => false
        //   ]
    
        if(!$token['id']){
            session()->flush('error', 'Stripe Token generation failed');
            return;
        }

        // Create a customer stripe.

        $customer = $stripe->customers()->create([
            'name' => $firstName.' '.$lastName,
            'email' => $email,
            'phone' => $phone,
            'address' => [
                'line1' => $address,
                'postal_code' => $zipCode,
                'city' => $city,
                'state' => $state,
                'country' => $country,
            ],
            'shipping' => [
                'name' => $firstName.' '.$lastName,
                'address' => [
                    'line1' => $address,
                    'postal_code' => $zipCode,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                ],
            ],
            'source' => $token['id'],
        ]);


        // Code for charging the client in Stripe.

        $charge = $stripe->charges()->create([
            'customer' => $customer['id'],
            'currency' => 'USD',
            'amount' => $amount,
            'description' => 'Payment for order',
        ]);


        if($charge['status'] == "succeeded")
        {
            // Capture the details from stripe.

            $customerIdStripe = $charge['id'];
            $amountRec = $charge['amount'];
            $client_id = auth()->id();

            $processingDetails = Processing::create([
                'client_id' => $client_id,
                'client_name' => $firstName.' '.$lastName,
                'client_address' => json_encode([
                                        'line1' => $address,
                                        'postal_code' => $zipCode,
                                        'city' => $city,
                                        'state' => $state,
                                        'country' => $country,
                                    ]),
                'order_details' => json_encode($ordersArray),
                'amount' => $amount,
                'currency' => $charge['currency'],
            ]);


            if($processingDetails)
            {
                // Clear the cart after payment success.

                Cart:: where('user_id', $client_id)->delete();

                return ['success'=> 'Order completed successfully'];
            }
            
        }
        else
        {
            return ['error'=> 'Order failed contact support'];
        }
    }
}
