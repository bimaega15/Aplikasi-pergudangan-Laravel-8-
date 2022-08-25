<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Cart;
use App\Helpers\Cartout;
use App\Helpers\Check;
use App\Helpers\CheckedExitItem;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ExitItem;
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
use Illuminate\Support\Facades\Gate;
use PhpParser\Node\Expr\Exit_;
use stdClass;

class ExitItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $exitItem = ExitItem::joinAllTable();

            return Datatables::of($exitItem)
                ->addIndexColumn()
                ->addColumn('check_id', function ($exitItem) {
                    $checked = new CheckedExitItem();
                    $getChecked = $checked::getExitItem();
                    $valChecked = isset($getChecked[$exitItem->id]) ? 'checked' : '';
                    $output = '
                    <div class="form-check">
                        <input class="form-check-input check_id" data-id="' . $exitItem->id . '" type="checkbox" value="' . $exitItem->id . '" ' . $valChecked . '>
                        <label class="form-check-label">
                        </label>
                    </div>
                    ';
                    return $output;
                })
                ->addColumn('picture_item', function ($exitItem) {
                    $pictureItem = $exitItem->picture_item != null ? public_path() . '/image/item/' . $exitItem->picture_item : false;
                    if (file_exists($pictureItem)) {
                        $pictureItemFix = asset('image/item/' . $exitItem->picture_item);
                    } else {
                        $pictureItemFix = asset('image/item/default.png');
                    }
                    $output = '
                    <a data-gallery="photoviewer" data-title="' . $exitItem->name_item . '" href="' . $pictureItemFix . '" alt="gambar-' . $exitItem->id . '" data-group="a">
                        <img src="' . $pictureItemFix . '" class="w-100 img-thumbnail"></img>
                    </a>
                    ';
                    return $output;
                })
                ->addColumn('action', function ($exitItem) {
                    $myProfile = Check::getProfile();
                    $button = '-';
                    if ($myProfile->role == 'admin') :
                        $button = '<a href="' . route('admin.exitItem.edit', $exitItem->id) . '" class="btn btn-sm btn-warning btn-edit" data-id="' . $exitItem->id . '"  data-toggle="modal" data-target="#modalForm"><i class="fas fa-pencil-alt"></i> Edit</a>
                    <form class="d-inline" method="post" action="' . route('admin.exitItem.destroy', $exitItem->id) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                       ' . method_field('delete') . '
                            <button type="submit" class="btn btn-sm btn-danger btn-delete" data-id="' . $exitItem->id . '"><i class="fas fa-trash-alt"></i> Hapus</button>
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
        return view('admin.exitItem.index');
    }

    public function loadDataTable(Request $request)
    {
        if ($request->ajax()) {
            $id = [];
            $cart = new Cartout();
            $getCart = $cart::getCart();
            $stockStore = [];
            if ($getCart != null) {
                foreach ($getCart as $id_cart => $value) {
                    $id[] = $id_cart;
                }
                $stockStore = StockStore::joinTable($id);
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
                    $cart = session()->get('cartOut');
                    if (isset($cart[$stockStore->id])) {
                        $valueStock = $cart[$stockStore->id]['quantity'];
                    } else {
                        $valueStock = $stockStore->store_stock_store;
                    }
                    $output = '
                    <div class="form-group">
                        <input type="number" name="stock_exit_item[' . $stockStore->id . ']" class="input-stock form-control" placeholder="Berapa Stok barang" data-id="' . $stockStore->id . '" value="' . $valueStock . '">
                    </div>
                    ';
                    return $output;
                })
                ->addColumn('action', function ($stockStore) {
                    $button = '
                    <form class="d-inline" method="post" action="' . route('admin.exitItem.removePost', $stockStore->id) . '">
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
            $checked = new CheckedExitItem();
            $getChecked = $checked::getExitItem();
            $getChecked = array_values($getChecked);
            $exitItem = ExitItem::joinAllTable($getChecked);

            return Datatables::of($exitItem)
                ->addIndexColumn()
                ->addColumn('stock_exit_item', function ($exitItem) {
                    $valueStock = $exitItem->stock_exit_item;
                    if (session()->has('exitItem.updateMultiple.' . $exitItem->id)) {
                        $valueStock = session()->get('exitItem.updateMultiple.' . $exitItem->id);
                    }
                    $output = '
                    <div class="form-group">
                        <input type="number" name="stock_exit_item[' . $exitItem->id . ']" class="input-stock form-control" placeholder="Berapa Stok barang" data-id="' . $exitItem->id . '" value="' . $valueStock . '">
                    </div>
                    ';
                    return $output;
                })
                ->addColumn('picture_item', function ($exitItem) {
                    $pictureItem = $exitItem->picture_item != null ? public_path() . '/image/item/' . $exitItem->picture_item : false;
                    if (file_exists($pictureItem)) {
                        $pictureItemFix = asset('image/item/' . $exitItem->picture_item);
                    } else {
                        $pictureItemFix = asset('image/item/default.png');
                    }
                    $output = '
                <a data-gallery="photoviewer" data-title="' . $exitItem->name_item . '" href="' . $pictureItemFix . '" alt="gambar-' . $exitItem->id . '" data-group="a">
                    <img src="' . $pictureItemFix . '" class="w-100 img-thumbnail"></img>
                </a>
                ';
                    return $output;
                })
                ->rawColumns(['picture_item', 'stock_exit_item'])
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

        return view('admin.exitItem.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $page = $request->input('page');
        if ($page == 'add') {
            $request->validate([
                'stock_exit_item' => [function ($attribute, $value, $fail) {
                    $idStockStore = [];
                    foreach ($value as $id => $val) {
                        $idStockStore[] = $id;
                    }
                    $message = '';

                    $stockStore = StockStore::joinTable($idStockStore);
                    foreach ($stockStore as $key => $r_val) {
                        if (isset($value[$r_val->id])) {
                            if ($r_val->store_stock_store < $value[$r_val->id]) {
                                $message .= 'Barang : ' . $r_val->name_location . ' ' . $r_val->code_item . ' ' . $r_val->name_item . ' ' . $r_val->name_unite_type . ' telah melebihi batas stock gudang yaitu: ' . $r_val->store_stock_store . ' <br>';
                            }
                        }
                    }
                    if ($message != '' && $message != null) {
                        $fail($message);
                    }
                },]
            ]);

            $cart = session()->get('cartOut');
            $id = [];
            $getCart = $cart;
            $stockStore = [];
            foreach ($getCart as $id_cart => $value) {
                $id[] = $id_cart;
            }
            $stockStore = StockStore::joinTable($id);

            $dataExitItem = [];
            foreach ($cart as $id => $v_cart) {
                $dataExitItem[] = [
                    'stock_store_id' => $id,
                    'users_id' => Auth::id(),
                    'out_date_exit_item' => Carbon::now()->toDateTimeString(),
                    'stock_exit_item' => $v_cart['quantity'],
                ];
            }
            // insert incoming goods
            $insertExitItem = ExitItem::insert($dataExitItem);
            if ($insertExitItem) {
                foreach ($dataExitItem as $key => $value) {
                    $stockStore = StockStore::find($value['stock_store_id']);
                    $getStore = $stockStore->store_stock_store;
                    $totalStore = $getStore - $value['stock_exit_item'];

                    $stockStore->update([
                        'store_stock_store' => $totalStore
                    ]);
                }

                // destroy session cart
                session()->forget('cartOut');
                session()->flash('success', 'Berhasil menambahkan transaksi barang keluar');
                return redirect()->route('admin.exitItem.index');
            } else {
                session()->flash('error', 'Gagal menambahkan transaksi barang keluar');
                return redirect()->route('admin.exitItem.index');
            }
        } else {
            //
            $validator = Validator::make($request->all(), [
                'stock_exit_item' => [
                    function ($attribute, $value, $fail) {
                        $stock_store_id = isset($_POST['stock_store_id']) ? $_POST['stock_store_id'] : (int) $_POST['old_stock_store_id'];

                        $id = $_POST['id'];
                        $stockStore = StockStore::find($stock_store_id);
                        $exitItem = ExitItem::find($id);
                        $stockExitItem = $exitItem->stock_exit_item;

                        $stock_exit_item = $_POST['stock_exit_item'];
                        if ($stockExitItem < $stock_exit_item) {
                            // jadinya dikurang ke stock store
                            $updateStockStore = $stock_exit_item - $stockExitItem;
                            $totalStock  = ($stockStore->store_stock_store - $updateStockStore);
                            if ($totalStock < 0) {
                                $message = 'Input barang keluar telah melebihi jumlah stock barang di gudang yaitu: ' . $stockStore->store_stock_store;
                                $fail($message);
                            }
                        }
                    },
                ]
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
            $exitItem = ExitItem::find($id);
            $stockStore = StockStore::find($stock_store_id);

            $stockExitItem = $exitItem->stock_exit_item;
            $stockStoreId = $exitItem->stock_store_id;

            $stock_exit_item = $request->input('stock_exit_item');
            if ($stockStoreId == $stock_store_id) {
                if ($stockExitItem < $stock_exit_item) {
                    // jadinya dikurang ke stock store
                    $updateStockStore = $stock_exit_item - $stockExitItem;
                    $totalStock  = ($stockStore->store_stock_store - $updateStockStore);
                    StockStore::where('id', $stock_store_id)->update([
                        'store_stock_store' => $totalStock
                    ]);
                }
                if ($stockExitItem > $stock_exit_item) {
                    // jadinya ditambah ke stock store
                    $updateStockStore = $stockExitItem - $stock_exit_item;
                    $totalStock = ($stockStore->store_stock_store + $updateStockStore);
                    StockStore::where('id', $stock_store_id)->update([
                        'store_stock_store' => $totalStock
                    ]);
                }
            }

            // update transaksi barang keluar
            $stock_exit_item = $request->input('stock_exit_item');
            $dataExitItem = [
                'stock_store_id' => $stock_store_id,
                'users_id' => Auth::id(),
                'out_date_exit_item' => Carbon::now()->toDateTimeString(),
                'stock_exit_item' => $stock_exit_item,
            ];
            $update = ExitItem::where('id', $id)->update($dataExitItem);
            if ($update) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil mengubah data transaksi barang keluar",
                ]);
            } else {
                return ResponseFormatter::error([
                    'status' => 400,
                    'message' => "Gagal mengubah data transaksi barang keluar",
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        $exitItem  = ExitItem::joinAllTable([], $id);

        if ($exitItem) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil mengambil data transaksi barang keluar",
                'result' => $exitItem
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal mengambil data transaksi barang keluar",
            ]);
        }
    }

    public function destroy($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $exitItem = ExitItem::find($id);
        $stock_store_id = $exitItem->stock_store_id;
        $stockStore = StockStore::find($stock_store_id);

        $getStockExitItem = $exitItem->stock_exit_item;
        $totalStock = $stockStore->store_stock_store + $getStockExitItem;

        // before update
        $stockStore->update([
            'store_stock_store' => $totalStock
        ]);

        $exitItem = ExitItem::destroy($id);
        if ($exitItem) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil menghapus transaksi barang keluar",
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal menghapus transaksi barang keluar",
            ]);
        }
    }

    public function postCart($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $cart = new Cartout();
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

        $cart = new Cartout();
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

        $cart = new Cartout();
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
    public function editMultiple()
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        return view('admin.exitItem.editMultiple');
    }
    public function checkedPost()
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $id = request()->input('id');
        $dataId = request()->input('dataId');
        $checked = new CheckedExitItem();
        $checked::addToExitItem($id, $dataId);
        $getChecked = $checked::getExitItem();

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
            $checked = new CheckedExitItem();
            $checked::addToExitItem($val_id, $val_dataId);
        }
        $getChecked = $checked::getExitItem();
        return ResponseFormatter::success([
            'status' => 200,
            'message' => "Berhasil menampilkan check post",
            'result' => $getChecked
        ]);
    }

    public function updateMultiple(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        session()->put('exitItem.updateMultiple', $request->input('stock_exit_item'));

        $request->validate([
            'stock_exit_item' => [function ($attribute, $value, $fail) {
                $message = '';
                foreach ($value as $id => $r_val) {
                    $exitItem = ExitItem::find($id);
                    $stockExitItem = $exitItem->stock_exit_item;
                    $getStockStore = StockStore::find($exitItem->stock_store_id);
                    $stock_exit_item = $r_val;

                    if ($stockExitItem < $stock_exit_item) {
                        // jadinya ditambah ke stock store
                        $updateStockStore = $stock_exit_item - $stockExitItem;
                        $totalStock  = ($getStockStore->store_stock_store - $updateStockStore);
                    }
                    if ($stockExitItem > $stock_exit_item) {
                        // jadinya dikurang ke stock store
                        $updateStockStore = $stockExitItem - $stock_exit_item;
                        $totalStock = ($getStockStore->store_stock_store + $updateStockStore);
                    }
                    if ($totalStock < 0) {
                        $message .= 'Jumlah barang keluar: ' . $exitItem->name_item . ' kurang dari stock gudang yaitu: ' . $getStockStore->store_stock_store . ' <br>';
                    }
                }
                if ($message != '' && $message != null) {
                    $fail($message);
                }
            },]
        ]);

        $dataIncomingGoods = $request->input('stock_exit_item');
        foreach ($dataIncomingGoods as $id => $value) {
            # code...
            // update stock store
            $exitItem = ExitItem::find($id);
            $stockStore = StockStore::find($exitItem->stock_store_id);

            $stockExitItem = $exitItem->stock_exit_item;

            $stock_exit_item = $value;
            if ($stockExitItem < $stock_exit_item) {

                // jadinya ditambah ke stock store
                $updateStockStore = $stock_exit_item - $stockExitItem;
                $totalStock  = ($stockStore->store_stock_store - $updateStockStore);
                StockStore::where('id', $exitItem->stock_store_id)->update([
                    'store_stock_store' => $totalStock
                ]);
            }
            if ($stockExitItem > $stock_exit_item) {

                // jadinya dikurang ke stock store
                $updateStockStore = $stockExitItem - $stock_exit_item;
                $totalStock = ($stockStore->store_stock_store + $updateStockStore);
                StockStore::where('id', $exitItem->stock_store_id)->update([
                    'store_stock_store' => $totalStock
                ]);
            }

            $updateIncomingGoods[] = $exitItem->update([
                'stock_exit_item' => $value
            ]);
        }

        // destroy session cart
        session()->forget(['exitItem', 'checkedExitItem']);
        session()->flash('success', 'Berhasil update ' . count($updateIncomingGoods) . ' transaksi barang keluar');
        return redirect()->route('admin.exitItem.index');
    }

    // public function loadInputStock()
    // {
    //     $cart = new Cartout();
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
