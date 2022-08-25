<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Check;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Location;
use App\Models\StockStore;
use App\Models\UniteType;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Validator;
use File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Xlsximport;

class StockStoreController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stockStore = StockStore::leftJoin('location as lc', 'lc.id', '=', 'stock_store.location_id')
                ->leftJoin('item as im', 'im.id', '=', 'stock_store.item_id')
                ->leftJoin('unite_type as utp', 'utp.id', '=', 'stock_store.unite_type_id')
                ->select('*', 'stock_store.id as id_stock_store')
                ->get();

            return Datatables::of($stockStore)
                ->addIndexColumn()
                ->addColumn('name_location', function ($stockStore) {
                    $output = $stockStore->name_location;
                    return $output;
                })
                ->addColumn('code_item', function ($stockStore) {
                    $output = $stockStore->code_item;
                    return $output;
                })
                ->addColumn('name_item', function ($stockStore) {
                    $output = $stockStore->name_item;
                    return $output;
                })
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
                ->addColumn('name_unite_type', function ($stockStore) {
                    $output = $stockStore->name_unite_type;
                    return $output;
                })
                ->addColumn('store_stock_store', function ($stockStore) {
                    $output = number_format($stockStore->store_stock_store, 0);
                    return $output;
                })
                ->addColumn('action', function ($stockStore) {
                    $profile = Check::getProfile();
                    $button = '-';
                    if ($profile->role == 'admin') {
                        $button = '<a href="' . route('admin.stockStore.edit', $stockStore->id_stock_store) . '" class="btn btn-sm btn-warning btn-edit" data-id="' . $stockStore->id_stock_store . '"  data-toggle="modal" data-target="#modalForm"><i class="fas fa-pencil-alt"></i> Edit</a>
                    <form class="d-inline" method="post" action="' . route('admin.stockStore.destroy', $stockStore->id_stock_store) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                       ' . method_field('delete') . '
                            <button type="submit" class="btn btn-sm btn-danger btn-delete" data-id="' . $stockStore->id_stock_store . '"><i class="fas fa-trash-alt"></i> Hapus</button>
                        </form>
                    ';
                    }

                    $output = '<div class="text-center">
                        ' . $button . '
                        </div>';
                    return $output;
                })
                ->rawColumns(['action', 'picture_item'])
                ->make();
        }
        return view('admin.stockStore.index', [
            'location' => Location::all(),
            'item' => Item::all(),
            'uniteType' => UniteType::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'location_id' => [function ($attribute, $value, $fail) {
                if ($_POST['page'] == 'add') {
                    if ($value == '' || $value == null) {
                        $fail('Lokasi wajib diisi');
                    }
                }
            },],
            'item_id' => [function ($attribute, $value, $fail) {
                if ($_POST['page'] == 'add') {
                    if ($value == '' || $value == null) {
                        $fail('Item wajib diisi');
                    }
                }
            },],
            'unite_type_id' => [function ($attribute, $value, $fail) {
                if ($_POST['page'] == 'add') {
                    if ($value == '' || $value == null) {
                        $fail('Jenis tipe wajib diisi');
                    }
                }
            },],
        ], [
            'required' => ':attribute wajib diisi',
            'image' => ':attribute harus berupa gambar',
            'max' => ':attribute tidak boleh lebih dari :max',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => 'Terjadi kesalahan',
                'result' => $validator->errors(),
            ]);
        }

        // halaman page

        if ($_POST['page'] == 'add') {
            $stockStore = StockStore::create([
                'location_id' => $request->input('location_id'),
                'item_id' => $request->input('item_id'),
                'unite_type_id' => $request->input('unite_type_id'),
                'users_id' => Auth::id(),
            ]);

            if ($stockStore) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil menambahkan stockStore",
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => "Gagal menambahkan stockStore",
                ]);
            }
        } else {
            // menyimpan data file yang diupload ke variabel $file
            $id = $request->input('id');
            $location_id = $request->input('location_id');
            if ($location_id == null) {
                $location_id = $request->input('old_location_id');
            }
            $item_id = $request->input('item_id');
            if ($item_id == null) {
                $item_id = $request->input('old_item_id');
            }
            $unite_type_id = $request->input('unite_type_id');
            if ($unite_type_id == null) {
                $unite_type_id = $request->input('old_unite_type_id');
            }
            $stockStore = StockStore::where('id', $id)->update([
                'location_id' => $location_id,
                'item_id' => $item_id,
                'unite_type_id' => $unite_type_id,
                'users_id' => Auth::id(),
            ]);

            if ($stockStore) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil update stockStore",
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => "Gagal update stockStore",
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

        $stockStore = StockStore::leftJoin('location as lc', 'lc.id', '=', 'stock_store.location_id')
            ->leftJoin('item as im', 'im.id', '=', 'stock_store.item_id')
            ->leftJoin('unite_type as utp', 'utp.id', '=', 'stock_store.unite_type_id')
            ->select('*', 'stock_store.id as id_stock_store', 'lc.id as id_location', 'im.id as id_item', 'utp.id as id_unite_type')
            ->where('stock_store.id', $id)
            ->first();

        if ($stockStore) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil mengambil stockStore",
                'result' => $stockStore
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal mengambil stockStore",
            ]);
        }
    }

    public function destroy($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        // delete file
        $stockStore = StockStore::destroy($id);
        if ($stockStore) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil menghapus stockStore",
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal menghapus stockStore",
            ]);
        }
    }

    public function getLocation()
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $search = request()->input('search');
        $page = request()->input('page');

        $limit = 15;
        $page_limit = $page * $limit;
        $page_offset = $page_limit - $limit;

        $location = Location::offset($page_offset)
            ->limit($limit)
            ->get();
        if ($search != null) {
            $location = Location::where('name_location', 'like', '%' . $search . '%')
                ->offset($page_offset)
                ->limit($limit)
                ->get();
        }
        $result = [];
        foreach ($location as $key => $v_location) {
            $result[] = [
                'id' => $v_location->id,
                'text' => $v_location->name_location,
            ];
        }
        $data = [
            'results' => $result,
            'total_count' => Location::all()->count()
        ];
        return response()->json($data, 200);
    }

    public function getItem()
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $search = request()->input('search');
        $page = request()->input('page');

        $limit = 15;
        $page_limit = $page * $limit;
        $page_offset = $page_limit - $limit;

        $item = Item::select('*', DB::raw('concat(`code_item`," ",`name_item`) as codeNameItem'))
            ->offset($page_offset)
            ->limit($limit)
            ->get();
        if ($search != null) {
            $item = Item::select('*', DB::raw('concat(`code_item`," ",`name_item`) as codeNameItem'))
                ->orWhere(DB::raw("CONCAT(`code_item`, ' ', `name_item`)"), 'like', '%' . $search . '%')
                ->orWhere('description_item', 'like', '%' . $search . '%')
                ->offset($page_offset)
                ->limit($limit)
                ->get();
        }
        $result = [];
        foreach ($item as $key => $v_item) {
            $result[] = [
                'id' => $v_item->id,
                'text' => $v_item->codeNameItem
            ];
        }
        $data = [
            'results' => $result,
            'total_count' => Item::all()->count()
        ];
        return response()->json($data, 200);
    }

    public function getUniteType()
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $search = request()->input('search');
        $page = request()->input('page');

        $limit = 15;
        $page_limit = $page * $limit;
        $page_offset = $page_limit - $limit;

        $uniteType = UniteType::offset($page_offset)
            ->limit($limit)
            ->get();
        if ($search != null) {
            $uniteType = UniteType::where('name_unite_type', 'like', '%' . $search . '%')
                ->offset($page_offset)
                ->limit($limit)
                ->get();
        }
        $result = [];
        foreach ($uniteType as $key => $v_uniteType) {
            $result[] = [
                'id' => $v_uniteType->id,
                'text' => $v_uniteType->name_unite_type
            ];
        }
        $data = [
            'results' => $result,
            'total_count' => UniteType::all()->count()
        ];
        return response()->json($data, 200);
    }
    public function import(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'import' => 'required|mimes:csv,xls,xlsx',
        ], [
            'mimes' => ':attribute file tidak didukung :values',
            'required' => ':attribute wajib diisi',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => 'Terjadi kesalahan',
                'result' => $validator->errors(),
            ]);
        }

        $arr_file = explode('.', $_FILES['import']['name']);
        $extension = end($arr_file);

        if ('csv' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } else {
            $reader = new Xlsximport;
        }

        $spreadsheet = $reader->load($_FILES['import']['tmp_name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        // location
        $dataLocation = [];
        $getLocation = Location::all();
        foreach ($getLocation as $key => $vLocation) {
            $dataLocation[$vLocation->name_location] = $vLocation->id;
        }

        // item
        $dataItem = [];
        $getItem = Item::all();
        foreach ($getItem as $key => $vItem) {
            $dataItem[$vItem->code_item] = $vItem->id;
        }

        // location
        $dataUniteType = [];
        $getUniteType = UniteType::all();
        foreach ($getUniteType as $key => $vUniteType) {
            $dataUniteType[$vUniteType->name_unite_type] = $vUniteType->id;
        }

        for ($i = 2; $i < count($sheetData); $i++) {
            $cek = $sheetData[$i]['2'];
            if ($cek != null) {

                $count[] = $i;
                $data[] = [
                    'location_id' => $dataLocation[$sheetData[$i]['3']],
                    'item_id' => $dataItem[$sheetData[$i]['4']],
                    'unite_type_id' => $dataUniteType[$sheetData[$i]['6']],
                    'users_id' => Auth::id(),
                    'store_stock_store' => $sheetData[$i]['7'],
                ];
            }
        }

        $insert = StockStore::insert($data);
        if ($insert > 0) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil import stock barang sebanyak " . count($data),
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal import stock barang",
            ]);
        }
    }
}
