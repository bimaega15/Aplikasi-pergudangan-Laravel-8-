<?php

namespace App\Helpers;

use App\Models\Item;
use App\Models\StockStore;
use Illuminate\Http\Client\Request;

class CheckedIncomingGoods
{
    public static function getIncomingGoods()
    {
        $checkedIncomingGoods = session()->get('checkedIncomingGoods');
        return $checkedIncomingGoods;
    }

    public static function addToIncomingGoods($id, $bandingId)
    {

        $checkedIncomingGoods = session()->get('checkedIncomingGoods', []);
        if (isset($checkedIncomingGoods[$bandingId])) {
            if ($id == null) {
                unset($checkedIncomingGoods[$bandingId]);
            }
        } else {
            if ($id != null) {
                $checkedIncomingGoods[$id] = $id;
            }
        }
        session()->put('checkedIncomingGoods', $checkedIncomingGoods);
    }
}
