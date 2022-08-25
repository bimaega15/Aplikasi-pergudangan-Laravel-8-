<?php

namespace App\Helpers;

use App\Models\Item;
use App\Models\StockStore;
use Illuminate\Http\Client\Request;

class CheckedExitItem
{
    public static function getExitItem()
    {
        $checkedExitItem = session()->get('checkedExitItem');
        return $checkedExitItem;
    }

    public static function addToExitItem($id, $bandingId)
    {

        $checkedExitItem = session()->get('checkedExitItem', []);
        if (isset($checkedExitItem[$bandingId])) {
            if ($id == null) {
                unset($checkedExitItem[$bandingId]);
            }
        } else {
            if ($id != null) {
                $checkedExitItem[$id] = $id;
            }
        }
        session()->put('checkedExitItem', $checkedExitItem);
    }
}
