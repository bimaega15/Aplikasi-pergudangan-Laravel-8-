<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Validator;
use File;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Xlsximport;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        if ($request->ajax()) {
            $item = Item::all();

            return Datatables::of($item)
                ->addIndexColumn()
                ->addColumn('picture_item', function ($item) {
                    $pictureItem = $item->picture_item != null ? public_path() . '/image/item/' . $item->picture_item : false;
                    if (file_exists($pictureItem)) {
                        $pictureItemFix = asset('image/item/' . $item->picture_item);
                    } else {
                        $pictureItemFix = asset('image/item/default.png');
                    }
                    $output = '
                    <a data-gallery="photoviewer" data-title="' . $item->name_item . '" href="' . $pictureItemFix . '" alt="gambar-' . $item->id . '" data-group="a">
                        <img src="' . $pictureItemFix . '" class="w-100 img-thumbnail"></img>
                    </a>
                    ';
                    return $output;
                })
                ->addColumn('action', function ($item) {
                    $button = '<a href="' . route('admin.item.edit', $item->id) . '" class="btn btn-sm btn-warning btn-edit" data-id="' . $item->id . '"  data-toggle="modal" data-target="#modalForm"><i class="fas fa-pencil-alt"></i> Edit</a>
                    <form class="d-inline" method="post" action="' . route('admin.item.destroy', $item->id) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                       ' . method_field('delete') . '
                            <button type="submit" class="btn btn-sm btn-danger btn-delete" data-id="' . $item->id . '"><i class="fas fa-trash-alt"></i> Hapus</button>
                        </form>
                    
                    ';
                    $output = '<div class="text-center">
                        ' . $button . '
                        </div>';
                    return $output;
                })
                ->rawColumns(['action', 'picture_item'])
                ->make();
        }
        return view('admin.item.index');
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
            'code_item' => ['required', function ($attribute, $value, $fail) {
                $code_item = $_POST['code_item'];
                if ($_POST['page'] == 'add') {
                    $checkCodeItem = Item::where('code_item', $code_item)->count();
                    if ($checkCodeItem > 0) {
                        $fail('Code item sudah digunakan');
                    }
                } else {
                    $checkCodeItem = Item::where('code_item', $code_item)
                        ->where('id', '!=', $_POST['id'])->count();
                    if ($checkCodeItem > 0) {
                        $fail('Code item sudah digunakan');
                    }
                }
            },],
            'name_item' => 'required',
            'picture_item' => 'image|max:1024',
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
            $file = $request->file('picture_item');
            $picture_item = $this->uploadFile($file);
            $item = Item::create([
                'code_item' => $request->input('code_item'),
                'name_item' => $request->input('name_item'),
                'description_item' => $request->input('description_item'),
                'picture_item' => $picture_item,
            ]);

            if ($item) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil menambahkan item",
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => "Gagal menambahkan item",
                ]);
            }
        } else {
            // menyimpan data file yang diupload ke variabel $file
            $id = $request->input('id');
            $file = $request->file('picture_item');
            $picture_item = $this->uploadFile($file, $id);
            $item = Item::where('id', $id)->update([
                'code_item' => $request->input('code_item'),
                'name_item' => $request->input('name_item'),
                'description_item' => $request->input('description_item'),
                'picture_item' => $picture_item,
            ]);

            if ($item) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil update item",
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => "Gagal update item",
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
        $item = Item::find($id);
        if ($item) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil mengambil item",
                'result' => $item
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal mengambil item",
            ]);
        }
    }

    public function destroy($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        // delete file
        $this->deleteFile($id);
        $item = Item::destroy($id);
        if ($item) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil menghapus item",
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal menghapus item",
            ]);
        }
    }

    private function uploadFile($file, $item_id = null)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        if ($file != null) {
            // delete file
            $this->deleteFile($item_id);
            // nama file
            $fileExp =  explode('.', $file->getClientOriginalName());
            $name = $fileExp[0];
            $ext = $fileExp[1];
            $name = time() . '-' . str_replace(' ', '-', $name) . '.' . $ext;

            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'image/item/';

            // upload file
            $file->move($tujuan_upload, $name);
        } else {
            if ($item_id == null) {
                $name = 'default.png';
            } else {
                $item = Item::where('id', $item_id)->first();
                $name = $item->picture_item;
            }
        }

        return $name;
    }

    private function deleteFile($item_id = null)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        if ($item_id != null) {
            $item = Item::where('id', '=', $item_id)->first();
            $gambar = public_path() . '/image/item/' . $item->picture_item;
            if (file_exists($gambar)) {
                if ($item->picture_item != 'default.png') {
                    File::delete($gambar);
                }
            }
        }
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
        for ($i = 2; $i < count($sheetData); $i++) {
            $cek = $sheetData[$i]['2'];
            if ($cek != null) {
                $count[] = $i;
                $data[$sheetData[$i]['4']] = [
                    'code_item' => ($sheetData[$i]['4']),
                    'name_item' => ($sheetData[$i]['5']),
                ];
            }
        }
        $db = array_values($data);
        $insert = Item::insert($data);
        if ($insert > 0) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil import lokasi barang sebanyak " . count($db),
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal import lokasi barang",
            ]);
        }
    }
}
