<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\UniteType;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Xlsximport;



class UniteTypeController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        if ($request->ajax()) {
            $uniteType = UniteType::all();

            return Datatables::of($uniteType)
                ->addIndexColumn()
                ->addColumn('action', function ($uniteType) {
                    $button = '<a href="' . route('admin.uniteType.edit', $uniteType->id) . '" class="btn btn-sm btn-warning btn-edit" data-id="' . $uniteType->id . '"  data-toggle="modal" data-target="#modalForm"><i class="fas fa-pencil-alt"></i> Edit</a>
                    <form class="d-inline" method="post" action="' . route('admin.uniteType.destroy', $uniteType->id) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                       ' . method_field('delete') . '
                            <button type="submit" class="btn btn-sm btn-danger btn-delete" data-id="' . $uniteType->id . '"><i class="fas fa-trash-alt"></i> Hapus</button>
                        </form>
                    
                    ';
                    $output = '<div class="text-center">
                        ' . $button . '
                        </div>';
                    return $output;
                })
                ->rawColumns(['action'])
                ->make();
        }
        return view('admin.uniteType.index');
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
        //
        $validator = Validator::make($request->all(), [
            'name_unite_type' => 'required',
        ], [
            'required' => ':attribute wajib diisi',
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
            $uniteType = UniteType::create([
                'name_unite_type' => $request->input('name_unite_type'),
            ]);

            if ($uniteType) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil menambahkan jenis tipe",
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => "Gagal menambahkan jenis tipe",
                ]);
            }
        } else {
            // menyimpan data file yang diupload ke variabel $file
            $id = $request->input('id');
            $uniteType = UniteType::where('id', $id)->update([
                'name_unite_type' => $request->input('name_unite_type'),
            ]);

            if ($uniteType) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil update jenis tipe",
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => "Gagal update jenis tipe",
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
        $uniteType = UniteType::find($id);
        if ($uniteType) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil mengambil jenis tipe",
                'result' => $uniteType
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal mengambil jenis tipe",
            ]);
        }
    }

    public function destroy($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        // delete file
        $uniteType = UniteType::destroy($id);
        if ($uniteType) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil menghapus jenis tipe",
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal menghapus jenis tipe",
            ]);
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
                $data[$sheetData[$i]['6']] = [
                    'name_unite_type' => ($sheetData[$i]['6']),
                ];
            }
        }
        $db = array_values($data);
        $insert = UniteType::insert($data);
        if ($insert > 0) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil import jenis tipe sebanyak " . count($db),
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal import jenis tipe",
            ]);
        }
    }
}
