<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
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
}
