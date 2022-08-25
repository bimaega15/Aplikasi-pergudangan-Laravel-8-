<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use File;
use Illuminate\Support\Facades\Gate;

class ConfigurationController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        if ($request->ajax()) {
            $data = Configuration::all()->count();
            $output = [];
            if ($data == 0) {
                $output = [
                    'status' => 400,
                    'message' => 'Data kosong',
                    'result' => [
                        'page' => 'add',
                        'data' => [],
                    ],
                ];
            } else {
                $output = [
                    'status' => 200,
                    'message' => 'Berhasil ambil data konfigurasi',
                    'result' => [
                        'page' => 'edit',
                        'data' => Configuration::first(),
                    ],
                ];
            }
            return ResponseFormatter::success($output);
        }
        return view('admin.configuration.index');
    }
    public function store(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        $validator = Validator::make($request->all(), [
            'name_configuration' => 'required',
            'address_configuration' => 'required',
            'telephone_configuration' => 'required|numeric',
            'email_configuration' => 'required|email',
            'created_by_configuration' => 'required',
            'picture_configuration' => 'image|max:2048',
        ], [
            'required' => ':attribute wajib diisi',
            'image' => ':attribute harus berupa gambar',
            'max' => ':attribute tidak boleh lebih dari :max',
            'email' => ':attribute harus valid',
            'integer' => ':attribute harus berupa angka',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => 'Terjadi kesalahan',
                'result' => $validator->errors(),
            ]);
        }

        $page = $request->input('page');
        if ($page == 'add') {
            $file = $request->file('picture_configuration');
            $picture_configuration = $this->uploadFile($file);
            $data = [
                'name_configuration' => $request->input('name_configuration'),
                'address_configuration' => $request->input('address_configuration'),
                'picture_configuration' => $picture_configuration,
                'description_configuration' => $request->input('description_configuration'),
                'telephone_configuration' => $request->input('telephone_configuration'),
                'email_configuration' => $request->input('email_configuration'),
                'created_by_configuration' => $request->input('created_by_configuration'),
            ];
            $insert = Configuration::create($data);
            if ($insert) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => 'Berhasil setting konfigurasi',
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => 'Gagal setting konfigurasi',
                ]);
            }
        } else {
            $file = $request->file('picture_configuration');
            $id = $request->input('id');
            $picture_configuration = $this->uploadFile($file, $id);
            $data = [
                'name_configuration' => $request->input('name_configuration'),
                'address_configuration' => $request->input('address_configuration'),
                'picture_configuration' => $picture_configuration,
                'description_configuration' => $request->input('description_configuration'),
                'telephone_configuration' => $request->input('telephone_configuration'),
                'email_configuration' => $request->input('email_configuration'),
                'created_by_configuration' => $request->input('created_by_configuration'),
            ];
            $update = Configuration::find($id)->update($data);
            if ($update) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => 'Berhasil setting konfigurasi',
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => 'Gagal setting konfigurasi',
                ]);
            }
        }
    }
    private function uploadFile($file, $id = null)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        if ($file != null) {
            // delete file
            $this->deleteFile($id);
            // nama file
            $fileExp =  explode('.', $file->getClientOriginalName());
            $name = $fileExp[0];
            $ext = $fileExp[1];
            $name = time() . '-' . str_replace(' ', '-', $name) . '.' . $ext;

            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'image/konfigurasi/';

            // upload file
            $file->move($tujuan_upload, $name);
        } else {
            if ($id == null) {
                $name = 'default.png';
            } else {
                $configuration = Configuration::where('id', $id)->first();
                $name = $configuration->picture_configuration;
            }
        }

        return $name;
    }

    private function deleteFile($id = null)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        if ($id != null) {
            $configuration = Configuration::where('id', '=', $id)->first();
            $gambar = public_path() . '/image/konfigurasi/' . $configuration->picture_configuration;
            if (file_exists($gambar)) {
                if ($configuration->picture_configuration != 'default.png') {
                    File::delete($gambar);
                }
            }
        }
    }
}
