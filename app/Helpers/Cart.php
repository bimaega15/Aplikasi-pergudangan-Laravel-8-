<?php

namespace App\Helpers;

use App\Models\Item;
use App\Models\StockStore;
use Illuminate\Http\Client\Request;

class Cart
{
    public static function getCart()
    {
        $cart = session()->get('cart');
        return $cart;
    }

    public static function addToCart($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $stockStore = StockStore::find($id);
            $cart[$id] = [
                "quantity" => 1,
            ];
        }
        session()->put('cart', $cart);
    }

    public static function update($request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
        }
    }
    public static function remove($request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
        }
    }
}
