<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExitItem extends Model
{
    use HasFactory;
    protected $table = 'exit_item';
    protected $guarded = ['id'];

    public static function joinAllTable($id = [], $single_id = null, $value = null, $from_date = null, $to_date = null)
    {
        $get = ExitItem::join('stock_store as ss', 'ss.id', '=', 'exit_item.stock_store_id')
            ->leftJoin('location as lc', 'lc.id', '=', 'ss.location_id')
            ->leftJoin('item as itm', 'itm.id', '=', 'ss.item_id')
            ->leftJoin('unite_type as ut', 'ut.id', '=', 'ss.unite_type_id')
            ->leftJoin('users as u', 'u.id', '=', 'exit_item.users_id')
            ->leftJoin('profile as p', 'p.users_id', '=', 'u.id')
            ->select('exit_item.id', 'lc.name_location', 'itm.code_item', 'itm.name_item', 'itm.picture_item', 'ut.name_unite_type', 'p.name_profile', 'exit_item.stock_exit_item', 'exit_item.out_date_exit_item', 'ss.id as id_stock_store');
        if (!(empty($id))) {
            $get = $get->whereIn('exit_item.id', $id);
            return $get->get();
        }
        if ($single_id != null) {
            $get = $get->where('exit_item.id', $single_id);
            return $get->first();
        }
        if ($value != null && $value != 'all') {
            $dateFirst = date('Y-m-d');
            $lessDate = date('Y-m-d', strtotime($value, strtotime($dateFirst)));
            $get = $get->whereBetween(DB::raw('DATE_FORMAT(exit_item.out_date_exit_item, "%Y-%m-%d")'), [$lessDate, $dateFirst]);
        }
        if ($from_date != null && $to_date != null) {
            $dateFirst = date('Y-m-d', strtotime($from_date));
            $dateLast = date('Y-m-d', strtotime($to_date));

            $get = $get->whereBetween(DB::raw('DATE_FORMAT(exit_item.out_date_exit_item, "%Y-%m-%d")'), [$dateFirst, $dateLast]);
        }
        return $get->get();
    }
}
