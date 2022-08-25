<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingGoods extends Model
{
    use HasFactory;
    protected $table = 'incoming_goods';
    protected $guarded = ['id'];

    public static function joinAllTable($id = [], $single_id = null)
    {
        $get = IncomingGoods::join('stock_store as ss', 'ss.id', '=', 'incoming_goods.stock_store_id')
            ->leftJoin('location as lc', 'lc.id', '=', 'ss.location_id')
            ->leftJoin('item as itm', 'itm.id', '=', 'ss.item_id')
            ->leftJoin('unite_type as ut', 'ut.id', '=', 'ss.unite_type_id')
            ->leftJoin('users as u', 'u.id', '=', 'incoming_goods.users_id')
            ->leftJoin('profile as p', 'p.users_id', '=', 'u.id')
            ->select('incoming_goods.id', 'lc.name_location', 'itm.code_item', 'itm.name_item', 'itm.picture_item', 'ut.name_unite_type', 'p.name_profile', 'incoming_goods.stock_incoming_goods', 'incoming_goods.date_of_entry_incoming_goods', 'ss.id as id_stock_store');
        if ($id != null && count($id) > 0) {
            $get = $get->whereIn('incoming_goods.id', $id);
            return $get->get();
        }
        if ($single_id != null) {
            $get = $get->where('incoming_goods.id', $single_id);
            return $get->first();
        }
        return $get->get();
    }
}