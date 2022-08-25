<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Xlsximport;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }

        if ($request->ajax()) {
            $location = Location::all();

            return Datatables::of($location)
                ->addIndexColumn()
                ->addColumn('action', function ($location) {
                    $button = '<a href="' . route('admin.location.edit', $location->id) . '" class="btn btn-sm btn-warning btn-edit" data-id="' . $location->id . '"  data-toggle="modal" data-target="#modalForm"><i class="fas fa-pencil-alt"></i> Edit</a>
                    <form class="d-inline" method="post" action="' . route('admin.location.destroy', $location->id) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                       ' . method_field('delete') . '
                            <button type="submit" class="btn btn-sm btn-danger btn-delete" data-id="' . $location->id . '"><i class="fas fa-trash-alt"></i> Hapus</button>
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
        return view('admin.location.index');
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
            'name_location' => 'required',
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
            $location = Location::create([
                'name_location' => $request->input('name_location'),
            ]);

            if ($location) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil menambahkan lokasi",
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => "Gagal menambahkan lokasi",
                ]);
            }
        } else {
            // menyimpan data file yang diupload ke variabel $file
            $id = $request->input('id');
            $location = Location::where('id', $id)->update([
                'name_location' => $request->input('name_location'),
            ]);

            if ($location) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil update lokasi",
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => "Gagal update lokasi",
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
        $location = Location::find($id);
        if ($location) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil mengambil lokasi",
                'result' => $location
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal mengambil lokasi",
            ]);
        }
    }

    public function destroy($id)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        // delete file
        $location = Location::destroy($id);
        if ($location) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil menghapus lokasi",
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal menghapus lokasi",
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
                $data[$sheetData[$i]['3']] = [
                    'name_location' => ($sheetData[$i]['3']),
                ];
            }
        }
        $db = array_values($data);
        $insert = Location::insert($data);
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
