<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Check;
use App\Http\Controllers\Controller;
use App\Models\ExitItem;
use App\Models\IncomingGoods;
use App\Models\Item;
use App\Models\Location;
use App\Models\UniteType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $location = Location::all()->count();
        $item = Item::all()->count();
        $uniteType = UniteType::all()->count();
        $incomingGoods = IncomingGoods::all()->count();
        $exitItem = ExitItem::all()->count();
        $users = User::all()->count();

        // incoming goods
        $reportValueIncomingGoods = IncomingGoods::select(DB::raw("SUM(stock_incoming_goods) as countStockIn"))
            ->groupby(DB::raw("DATE_FORMAT(date_of_entry_incoming_goods, '%m-%Y')"))
            ->get()->pluck('countStockIn')->toArray();

        $reportLabelIncomingGoods = IncomingGoods::select(DB::raw("DATE_FORMAT(date_of_entry_incoming_goods, '%m-%Y') as date_of_entry_incoming_goods"))
            ->groupby(DB::raw("DATE_FORMAT(date_of_entry_incoming_goods, '%m-%Y')"))
            ->get()->pluck('date_of_entry_incoming_goods')->toArray();

        $dataIncomingGoods = [];
        foreach ($reportLabelIncomingGoods as $key => $value) {
            $exp = explode('-', $value);
            $bulan = $exp[0];

            $dataIncomingGoods['01'] = 0;
            $dataIncomingGoods['02'] = 0;
            $dataIncomingGoods['03'] = 0;
            $dataIncomingGoods['04'] = 0;
            $dataIncomingGoods['05'] = 0;
            $dataIncomingGoods['06'] = 0;
            $dataIncomingGoods['07'] = 0;
            $dataIncomingGoods['08'] = 0;
            $dataIncomingGoods['09'] = 0;
            $dataIncomingGoods['10'] = 0;
            $dataIncomingGoods['11'] = 0;
            $dataIncomingGoods['12'] = 0;


            $passingValue = (int) $reportValueIncomingGoods[$key];
            $getHasil = Check::getBulanLaporan($bulan, $passingValue);
            $merge = array_replace($dataIncomingGoods, $getHasil);
        }
        $merge = array_values($merge);
        $valueIncomingGoods = json_encode($merge);


        // exit item
        $reportValueExitItem = ExitItem::select(DB::raw("SUM(stock_exit_item) as countStockOut"))
            ->groupby(DB::raw("DATE_FORMAT(out_date_exit_item, '%m-%Y')"))
            ->get()->pluck('countStockOut');

        $reportLabelExitItem = ExitItem::select(DB::raw("DATE_FORMAT(out_date_exit_item, '%m-%Y') as out_date_exit_item"))
            ->groupby(DB::raw("DATE_FORMAT(out_date_exit_item, '%m-%Y')"))
            ->get()->pluck('out_date_exit_item');

        $dataExitItem = [];
        foreach ($reportLabelExitItem as $key => $value) {
            $exp = explode('-', $value);
            $bulan = $exp[0];

            $dataExitItem['01'] = 0;
            $dataExitItem['02'] = 0;
            $dataExitItem['03'] = 0;
            $dataExitItem['04'] = 0;
            $dataExitItem['05'] = 0;
            $dataExitItem['06'] = 0;
            $dataExitItem['07'] = 0;
            $dataExitItem['08'] = 0;
            $dataExitItem['09'] = 0;
            $dataExitItem['10'] = 0;
            $dataExitItem['11'] = 0;
            $dataExitItem['12'] = 0;


            $passingValue = (int) $reportValueExitItem[$key];
            $getHasil = Check::getBulanLaporan($bulan, $passingValue);
            $merge = array_replace($dataExitItem, $getHasil);
        }
        $merge = array_values($merge);
        $valueExitItem = json_encode($merge);

        $tahun = date('Y');
        $januari = 'Januari ' . $tahun;
        $februari = 'Februari ' . $tahun;
        $maret = 'Maret ' . $tahun;
        $april = 'April ' . $tahun;
        $mei = 'Mei ' . $tahun;
        $juni = 'Juni ' . $tahun;
        $juli = 'Juli ' . $tahun;
        $agustus = 'Agustus ' . $tahun;
        $september = 'September ' . $tahun;
        $oktober = 'Oktober ' . $tahun;
        $november = 'November ' . $tahun;
        $desember = 'Desember ' . $tahun;

        return view('admin.home.index', [
            'location' => $location,
            'item' => $item,
            'uniteType' => $uniteType,
            'incomingGoods' => $incomingGoods,
            'exitItem' => $exitItem,
            'users' => $users,

            'reportValueIncomingGoods' => $reportValueIncomingGoods,
            'reportLabelIncomingGoods' => $reportLabelIncomingGoods,
            'reportValueExitItem' => $reportValueExitItem,
            'reportLabelExitItem' => $reportLabelExitItem,

            'januari' => $januari,
            'februari' => $februari,
            'maret' => $maret,
            'april' => $april,
            'mei' => $mei,
            'juni' => $juni,
            'juli' => $juli,
            'agustus' => $agustus,
            'september' => $september,
            'oktober' => $oktober,
            'november' => $november,
            'desember' => $desember,

            'valueIncomingGoods' => $valueIncomingGoods,
            'valueExitItem' => $valueExitItem,
        ]);
    }
}
