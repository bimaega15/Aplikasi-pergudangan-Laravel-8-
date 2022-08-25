<?php

namespace App\Helpers;

use App\Models\Item;
use App\Models\StockStore;
use Illuminate\Http\Client\Request;

class Cartout
{
    public static function getCart()
    {
        $cart = session()->get('cartOut');
        return $cart;
    }

    public static function addToCart($id)
    {
        $cart = session()->get('cartOut', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $stockStore = StockStore::find($id);
            $cart[$id] = [
                "quantity" => 1,
            ];
        }
        session()->put('cartOut', $cart);
    }

    public static function update($request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cartOut');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cartOut', $cart);
        }
    }
    public static function remove($request)
    {
        if ($request->id) {
            $cart = session()->get('cartOut');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cartOut', $cart);
            }
        }
    }
}
