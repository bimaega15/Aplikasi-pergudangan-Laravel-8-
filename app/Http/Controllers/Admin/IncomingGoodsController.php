<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Cart;
use App\Helpers\CheckedIncomingGoods;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\IncomingGoods;
use App\Models\Item;
use App\Models\StockStore;
use App\Models\UniteType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Validator;
use File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;
use App\Helpers\Check;
use Illuminate\Support\Facades\Gate;

class IncomingGoodsController extends Controller
{
    public function index(Request $request)
    {
        $incomingGoods = IncomingGoods::joinAllTable();
        if ($request->ajax()) {
            $incomingGoods = IncomingGoods::joinAllTable();
            return Datatables::of($incomingGoods)
                ->addIndexColumn()
                ->addColumn('check_id', function ($incomingGoods) {
                    $checked = new CheckedIncomingGoods();
                    $getChecked = $checked::getIncomingGoods();
                    $valChecked = isset($getChecked[$incomingGoods->id]) ? 'checked' : '';
                    $output = '
                    <div class="form-check">
                        <input class="form-check-input check_id" data-id="' . $incomingGoods->id . '" type="checkbox" value="' . $incomingGoods->id . '" ' . $valChecked . '>
                        <label class="form-check-label">
                        </label>
                    </div>
                    ';
                    return $output;
                })
                ->addColumn('picture_item', function ($incomingGoods) {
                    $pictureItem = $incomingGoods->picture_item != null ? public_path() . '/image/item/' . $incomingGoods->picture_item : false;
                    if (file_exists($pictureItem)) {
                        $pictureItemFix = asset('image/item/' . $incomingGoods->picture_item);
                    } else {
                        $pictureItemFix = asset('image/item/default.png');
                    }
                    $output = '
                    <a data-gallery="photoviewer" data-title="' . $incomingGoods->name_item . '" href="' . $pictureItemFix . '" alt="gambar-' . $incomingGoods->id . '" data-group="a">
                        <img src="' . $pictureItemFix . '" class="w-100 img-thumbnail"></img>
                    </a>
                    ';
                    return $output;
                })
                ->addColumn('action', function ($incomingGoods) {
                    $myProfile = Check::getProfile();
                    $button = '-';
                    if ($myProfile->role == 'admin') :
                        $button = '<a href="' . route('admin.incomingGoods.edit', $incomingGoods->id) . '" class="btn btn-sm btn-warning btn-edit" data-id="' . $incomingGoods->id . '"  data-toggle="modal" data-target="#modalForm"><i class="fas fa-pencil-alt"></i> Edit</a>
                    <form class="d-inline" method="post" action="' . route('admin.incomingGoods.destroy', $incomingGoods->id) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                       ' . method_field('delete') . '
                            <button type="submit" class="btn btn-sm btn-danger btn-delete" data-id="' . $incomingGoods->id . '"><i class="fas fa-trash-alt"></i> Hapus</button>
                        </form>
                    
                    ';
                    endif;
                    $output = '<div class="text-center">
                        ' . $button . '
                        </div>';
                    return $output;
                })
                ->rawColumns(['action', 'picture_item', 'check_id'])
                ->make();
        }
        return view('admin.incomingGoods.index');
    }

    public function loadDataTable(Request $request)
    {
        if ($request->ajax()) {
            $id = [];
            $cart = new Cart();
            $getCart = $cart::getCart();
            $stockStore = [];
            if ($getCart != null) {
                foreach ($getCart as $id_cart => $value) {
                    $id[] = $id_cart;
                }

                $stockStore = StockStore::leftJoin('location as lc', 'lc.id', '=', 'stock_store.location_id')
                    ->leftJoin('item as im', 'im.id', '=', 'stock_store.item_id')
                    ->leftJoin('unite_type as utp', 'utp.id', '=', 'stock_store.unite_type_id')
                    ->select('lc.name_location', 'im.code_item', 'im.name_item', 'utp.name_unite_type', 'im.picture_item', 'stock_store.store_stock_store', 'stock_store.id')
                    ->whereIn('stock_store.id', $id)
                    ->get();
            }


            return Datatables::of($stockStore)
                ->addIndexColumn()
                ->addColumn('picture_item', function ($stockStore) {
                    $pictureItem = $stockStore->picture_item != null ? public_path() . '/image/item/' . $stockStore->picture_item : false;
                    if (file_exists($pictureItem)) {
                        $pictureItemFix = asset('image/item/' . $stockStore->picture_item);
                    } else {
                        $pictureItemFix = asset('image/item/default.png');
                    }
                    $output = '
                    <a data-gallery="photoviewer" data-title="' . $stockStore->name_item . '" href="' . $pictureItemFix . '" alt="gambar-' . $stockStore->id . '" data-group="a">
                        <img src="' . $pictureItemFix . '" class="w-100 img-thumbnail"></img>
                    </a>
                    ';
                    return $output;
                })
                ->addColumn('store_stock_store', function ($stockStore) {
                    $cart = session()->get('cart');
                    if (isset($cart[$stockStore->id])) {
                        $valueStock = $cart[$stockStore->id]['quantity'];
                    } else {
                        $valueStock = $stockStore->store_stock_store;
                    }
                    $output = '
                    <div class="form-group">
                        <input type="number" class="input-stock form-control" placeholder="Berapa Stok barang" data-id="' . $stockStore->id . '" value="' . $valueStock . '">
                    </div>
                    ';
                    return $output;
                })
                ->addColumn('action', function ($stockStore) {
                    $button = '
                    <form class="d-inline" method="post" action="' . route('admin.incomingGoods.removePost', $stockStore->id) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                       ' . method_field('delete') . '
                            <button type="submit" class="btn btn-sm btn-danger btn-delete-cart" data-id="' . $stockStore->id . '"><i class="fas fa-trash-alt"></i> Hapus</button>
                        </form>
                    ';
                    $output = '<div class="text-center">
                        ' . $button . '
                        </div>';
                    return $output;
                })
                ->rawColumns(['action', 'picture_item', 'store_stock_store'])
                ->make();
        }
    }

    public function loadDataTableEditMultiple(Request $request)
    {
        if ($request->ajax()) {
            $checked = new CheckedIncomingGoods();
            $getChecked = $checked::getIncomingGoods();
            $getChecked = array_values($getChecked);
            $incomingGoods = IncomingGoods::joinAllTable($getChecked);

            return Datatables::of($incomingGoods)
                ->addIndexColumn()
                ->addColumn('stock_incoming_goods', function ($incomingGoods) {
                    $valueStock = $incomingGoods->stock_incoming_goods;
                    if (session()->has('incomingGoods.updateMultiple.' . $incomingGoods->id)) {
                        $valueStock = session()->get('incomingGoods.updateMultiple.' . $incomingGoods->id);
                    }
                    $output = '
                    <div class="form-group">
                        <input type="number" name="stock_incoming_goods[' . $incomingGoods->id . ']" class="input-stock form-control" placeholder="Berapa Stok barang" data-id="' . $incomingGoods->id . '" value="' . $valueStock . '">
                    </div>
                    ';
                    return $output;
                })
                ->addColumn('picture_item', function ($incomingGoods) {
                    $pictureItem = $incomingGoods->picture_item != null ? public_path() . '/image/item/' . $incomingGoods->picture_item : false;
                    if (file_exists($pictureItem)) {
                        $pictureItemFix = asset('image/item/' . $incomingGoods->picture_item);
                    } else {
                        $pictureItemFix = asset('image/item/default.png');
                    }
                    $output = '
                <a data-gallery="photoviewer" data-title="' . $incomingGoods->name_item . '" href="' . $pictureItemFix . '" alt="gambar-' . $incomingGoods->id . '" data-group="a">
                    <img src="' . $pictureItemFix . '" class="w-100 img-thumbnail"></img>
                </a>
                ';
                    return $output;
                })
                ->rawColumns(['picture_item', 'stock_incoming_goods'])
                ->make();
        }
    }

    public function create()
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        if (request()->ajax()) {
            $search = request()->input('search');
            $page = request()->input('page');

            $limit = 15;
            $page_limit = $page * $limit;
            $page_offset = $page_limit - $limit;

            $stockStore = StockStore::leftJoin('location as lc', 'lc.id', '=', 'stock_store.location_id')
                ->leftJoin('item as im', 'im.id', '=', 'stock_store.item_id')
                ->leftJoin('unite_type as utp', 'utp.id', '=', 'stock_store.unite_type_id')
                ->select('*', 'stock_store.id as id_stock_store', DB::raw('concat(lc.name_location," ",im.code_item," ",im.name_item," ",utp.name_unite_type) as stock_store_item'))
                ->offset($page_offset)
                ->limit($limit)
                ->get();


            if ($search != null) {
                $stockStore = StockStore::leftJoin('location as lc', 'lc.id', '=', 'stock_store.location_id')
                    ->leftJoin('item as im', 'im.id', '=', 'stock_store.item_id')
                    ->leftJoin('unite_type as utp', 'utp.id', '=', 'stock_store.unite_type_id')
                    ->select('*', 'stock_store.id as id_stock_store', DB::raw('concat(lc.name_location," ",im.code_item," ",im.name_item," ",utp.name_unite_type) as stock_store_item'))
                    ->where(DB::raw('concat(lc.name_location," ",im.code_item," ",im.name_item," ",utp.name_unite_type)'), 'like', '%' . $search . '%')
                    ->offset($page_offset)
                    ->limit($limit)
                    ->get();
            }
            $result = [];
            foreach ($stockStore as $key => $v_stockStore) {
                $result[] = [
                    'id' => $v_stockStore->id_stock_store,
                    'text' => $v_stockStore->stock_store_item
                ];
            }

            $count = StockStore::leftJoin('location as lc', 'lc.id', '=', 'stock_store.location_id')
                ->leftJoin('item as im', 'im.id', '=', 'stock_store.item_id')
                ->leftJoin('unite_type as utp', 'utp.id', '=', 'stock_store.unite_type_id')
                ->select('*', 'stock_store.id as id_stock_store', DB::raw('concat(lc.name_location," ",im.code_item," ",im.name_item," ",utp.name_unite_type) as stock_store_item'))
                ->get()->count();

            $data = [
                'results' => $result,
                'total_count' => $count
            ];
            return response()->json($data, 200);
        }

        return view('admin.incomingGoods.create');
    }

    public function store(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        $page = $request->input('page');
        if ($page == 'add') {
            $cart = session()->get('cart');
            if ($cart == null) {
                session()->flash('error', 'Anda belum input cart');
                return redirect()->route('admin.incomingGoods.create');
            }

            $dataIncomingGoods = [];
            foreach ($cart as $id => $v_cart) {
                $dataIncomingGoods[] = [
                    'stock_store_id' => $id,
                    'users_id' => Auth::id(),
                    'date_of_entry_incoming_goods' => Carbon::now()->toDateTimeString(),
                    'stock_incoming_goods' => $v_cart['quantity'],
                ];
            }
            // insert incoming goods
            $insertIncomingGoods = IncomingGoods::insert($dataIncomingGoods);
            if ($insertIncomingGoods) {
                foreach ($dataIncomingGoods as $key => $value) {
                    $stockStore = StockStore::find($value['stock_store_id']);
                    $getStore = $stockStore->store_stock_store;
                    $totalStore = $getStore + $value['stock_incoming_goods'];

                    $stockStore->update([
                        'store_stock_store' => $totalStore
                    ]);
                }

                // destroy session cart
                session()->forget('cart');
                session()->flash('success', 'Berhasil menambahkan transaksi barang masuk');
                return redirect()->route('admin.incomingGoods.index');
            } else {
                session()->flash('error', 'Gagal menambahkan transaksi barang masuk');
                return redirect()->route('admin.incomingGoods.index');
            }
        } else {
            //
            $validator = Validator::make($request->all(), [
                'stock_incoming_goods' => 'required|numeric',
            ], [
                'required' => ':attribute wajib diisi',
                'numeric' => ':attribute harus berupa angka',
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'status' => 400,
                    'message' => 'Terjadi kesalahan',
                    'result' => $validator->errors(),
                ]);
            }

            $stock_store_id = (int) $request->input('stock_store_id');
            if ($stock_store_id == null) {
                $stock_store_id = (int) $request->input('old_stock_store_id');
            }
            $id = $request->input('id');

            // update stock store
            $incomingGoods = IncomingGoods::find($id);
            $stockStore = StockStore::find($stock_store_id);

            $stockIncomingGoods = $incomingGoods->stock_incoming_goods;
            $stockStoreId = $incomingGoods->stock_store_id;

            $stock_incoming_goods = $request->input('stock_incoming_goods');
            if ($stockStoreId == $stock_store_id) {
                if ($stockIncomingGoods < $stock_incoming_goods) {
                    // jadinya ditambah ke stock store
                    $updateStockStore = $stock_incoming_goods - $stockIncomingGoods;
                    $totalStock  = ($stockStore->store_stock_store + $updateStockStore);
                    StockStore::where('id', $stock_store_id)->update([
                        'store_stock_store' => $totalStock
                    ]);
                }
                if ($stockIncomingGoods > $stock_incoming_goods) {
                    // jadinya dikurang ke stock store
                    $updateStockStore = $stockIncomingGoods - $stock_incoming_goods;
                    $totalStock = ($stockStore->store_stock_store - $updateStockStore);
                    StockStore::where('id', $stock_store_id)->update([
                        'store_stock_store' => $totalStock
                    ]);
                }
            }

            // update transaksi barang masuk
            $stock_incoming_goods = $request->input('stock_incoming_goods');
            $dataIncomingGoods = [
                'stock_store_id' => $stock_store_id,
                'users_id' => Auth::id(),
                'date_of_entry_incoming_goods' => Carbon::now()->toDateTimeString(),
                'stock_incoming_goods' => $stock_incoming_goods,
            ];
            $update = IncomingGoods::where('id', $id)->update($dataIncomingGoods);
            if ($update) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil mengubah data transaksi barang masuk",
                ]);
            } else {
                return ResponseFormatter::error([
                    'status' => 400,
                    'message' => "Gagal mengubah data transaksi barang masuk",
                ]);
            }
        }
    }

    public function updateMultiple(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        session()->put('incomingGoods.updateMultiple', $request->input('stock_incoming_goods'));
        $request->validate([
            'stock_incoming_goods' => [function ($attribute, $value, $fail) {
                $errVal = '';
                foreach ($value as $id => $val) {
                    $get = IncomingGoods::joinAllTable([], $id);
                    if ($val == null) {
                        $errVal .= 'Barang ' . $get->name_item . ' jumlah stock tidak boleh kosong <br>';
                    }
                    if ($val == 0) {
                        $errVal .= 'Barang ' . $get->name_item . ' jumlah stock tidak boleh 0 <br>';
                    }
                    if ($val < 0) {
                        $errVal .= 'Barang ' . $get->name_item . ' jumlah stock tidak boleh kurang dari 0 <br>';
                    }
                }
                if ($errVal != null && $errVal != '') {
                    $fail($errVal);
                }
            },]
        ]);

        $dataIncomingGoods = $request->input('stock_incoming_goods');
        foreach ($dataIncomingGoods as $id => $value) {
            # code...
            // update stock store
            $incomingGoods = IncomingGoods::find($id);
            $stockStore = StockStore::find($incomingGoods->stock_store_id);

            $stockIncomingGoods = $incomingGoods->stock_incoming_goods;
            $stockStoreId = $incomingGoods->stock_store_id;

            $stock_incoming_goods = $value;
            if ($stockIncomingGoods < $stock_incoming_goods) {

                // jadinya ditambah ke stock store
                $updateStockStore = $stock_incoming_goods - $stockIncomingGoods;
                $totalStock  = ($stockStore->store_stock_store + $updateStockStore);
                StockStore::where('id', $incomingGoods->stock_store_id)->update([
                    'store_stock_store' => $totalStock
                ]);
            }
            if ($stockIncomingGoods > $stock_incoming_goods) {

                // jadinya dikurang ke stock store
                $updateStockStore = $stockIncomingGoods - $stock_incoming_goods;
                $totalStock = ($stockStore->store_stock_store - $updateStockStore);
                StockStore::where('id', $incomingGoods->stock_store_id)->update([
                    'store_stock_store' => $totalStock
                ]);
            }

            $updateIncomingGoods[] = $incomingGoods->update([
                'stock_incoming_goods' => $value
            ]);
        }

        // destroy session cart
        session()->forget(['incomingGoods', 'checkedIncomingGoods']);
        session()->flash('success', 'Berhasil update ' . count($updateIncomingGoods) . ' transaksi barang masuk');
        return redirect()->route('admin.incomingGoods.index');
    }



    public function show($id)
    {
    }


    public function edit($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        $incomingGoods  = IncomingGoods::joinAllTable([], $id);

        if ($incomingGoods) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil mengambil data transaksi barang masuk",
                'result' => $incomingGoods
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal mengambil data transaksi barang masuk",
            ]);
        }
    }

    public function destroy($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        $incomingGoods = IncomingGoods::find($id);
        $stock_store_id = $incomingGoods->stock_store_id;
        $stockStore = StockStore::find($stock_store_id);

        $getStockIncomingGoods = $incomingGoods->stock_incoming_goods;
        $totalStock = $stockStore->store_stock_store - $getStockIncomingGoods;

        // before update
        $stockStore->update([
            'store_stock_store' => $totalStock
        ]);

        $incomingGoods = IncomingGoods::destroy($id);
        if ($incomingGoods) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil menghapus transaksi barang masuk",
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal menghapus transaksi barang masuk",
            ]);
        }
    }

    public function postCart($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $cart = new Cart();
        $cart::addToCart($id);
        $getCart = $cart::getCart();

        return ResponseFormatter::success([
            'status' => 200,
            'message' => "Berhasil menambahkan cart",
            'result' => $getCart
        ]);
    }

    public function updateCart($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $cart = new Cart();
        $std = new stdClass();
        $std->id = $id;
        $std->quantity = request()->input('value');

        $updateCart = $cart::update($std);
        $getCart = $cart::getCart();

        return ResponseFormatter::success([
            'status' => 200,
            'message' => "Berhasil mengupdate cart",
            'result' => $getCart
        ]);
    }

    public function removePost($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $cart = new Cart();
        $std = new stdClass();
        $std->id = $id;

        $removeCart = $cart::remove($std);
        $getCart = $cart::getCart();

        return ResponseFormatter::success([
            'status' => 200,
            'message' => "Berhasil menghapus cart",
            'result' => $getCart
        ]);
    }

    public function checkedPost()
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $id = request()->input('id');
        $dataId = request()->input('dataId');
        $checked = new CheckedIncomingGoods();
        $checked::addToIncomingGoods($id, $dataId);
        $getChecked = $checked::getIncomingGoods();

        return ResponseFormatter::success([
            'status' => 200,
            'message' => "Berhasil menampilkan check post",
            'result' => $getChecked
        ]);
    }

    public function checkedPostMultiple()
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $id = request()->input('id');
        $dataId = request()->input('dataId');
        foreach ($id as $key => $r_id) {
            $val_id = $r_id;
            $val_dataId = $dataId[$key];
            $checked = new CheckedIncomingGoods();
            $checked::addToIncomingGoods($val_id, $val_dataId);
        }
        $getChecked = $checked::getIncomingGoods();
        return ResponseFormatter::success([
            'status' => 200,
            'message' => "Berhasil menampilkan check post",
            'result' => $getChecked
        ]);
    }

    public function editMultiple()
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        return view('admin.incomingGoods.editMultiple');
    }
    // public function loadInputStock()
    // {
    //     $cart = new Cart();
    //     $getCart = $cart::getCart();
    //     $id = [];
    //     foreach ($getCart as $id_cart => $value) {
    //         $id[] = $id_cart;
    //     }
    //     $stockStore = StockStore::leftJoin('location as lc', 'lc.id', '=', 'stock_store.location_id')
    //         ->leftJoin('item as im', 'im.id', '=', 'stock_store.item_id')
    //         ->leftJoin('unite_type as utp', 'utp.id', '=', 'stock_store.unite_type_id')
    //         ->select('lc.name_location', 'im.code_item', 'im.name_item', 'utp.name_unite_type', 'im.picture_item', 'stock_store.store_stock_store', 'stock_store.id')
    //         ->whereIn('stock_store.id', $id)
    //         ->get();

    //     return ResponseFormatter::success([
    //         'status' => 200,
    //         'message' => "Berhasil menampilkan data stock",
    //         'result' => $stockStore
    //     ]);
    // }
}
