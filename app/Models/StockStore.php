<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockStore extends Model
{
    use HasFactory;
    protected $table = 'stock_store';
    protected $guarded = ['id'];

    public static function joinTable($id = [])
    {
        $getStock = StockStore::leftJoin('location as lc', 'lc.id', '=', 'stock_store.location_id')
            ->leftJoin('item as im', 'im.id', '=', 'stock_store.item_id')
            ->leftJoin('unite_type as utp', 'utp.id', '=', 'stock_store.unite_type_id')
            ->select('lc.name_location', 'im.code_item', 'im.name_item', 'utp.name_unite_type', 'im.picture_item', 'stock_store.store_stock_store', 'stock_store.id');
        if ($id != null && count($id) > 0) {
            $getStock = $getStock->whereIn('stock_store.id', $id);
        }
        $getStock = $getStock->get();
        return $getStock;
    }
}
