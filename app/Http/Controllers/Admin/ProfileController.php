<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use File;
use Exception;


class ProfileController extends Controller
{
    public function index()
    {
        $users = User::where('id', Auth::id())->first();
        $profile = $users->profile()->first();

        return view('admin.profile.index', [
            'users' => $users,
            'profile' => $profile
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_profile' => 'required',
            'gender_profile' => 'required',
            'telephone_profile' => 'required',
            'address_profile' => 'required',
            'email' => ['required', function ($attribute, $value, $fail) {
                $email = $_POST['email'];
                $checkEmail = User::where('email', $email)
                    ->where('id', '!=', $_POST['id'])->count();
                if ($checkEmail > 0) {
                    $fail('Email sudah digunakan');
                }
            },],
            'password' => 'confirmed',
            'picture_profile' => 'image|max:2048',
        ], [
            'required' => ':attribute wajib diisi',
            'confirmed' => ':attribute tidak sama dengan password confirmation',
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

        try {
            $data = $request->all();
            unset($data['_token']);


            $id = $data['id'];
            $password = $request->input('password_old');
            $password_users = $request->input('password');
            if ($password_users != null) :
                $password = Hash::make($password_users);
            endif;
            $users = User::where('id', $id)->update([
                'name' => $data['name_profile'],
                'email' => $data['email'],
                'password' => $password,
            ]);

            $file = $request->file('picture_profile');
            $picture_profile = $this->uploadFile($file, $id);
            $profile = Profile::where('users_id', $id)->update([
                'name_profile' => $data['name_profile'],
                'gender_profile' => $data['gender_profile'],
                'telephone_profile' => $data['telephone_profile'],
                'address_profile' => $data['address_profile'],
                'picture_profile' => $picture_profile,
                'status_profile' => 'complete',
            ]);
            if ($users || $profile) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil mengubah data profile",
                    'result' => Profile::join('users', 'users.id', '=', 'profile.users_id')->where('users.id', $id)->first(),
                ]);
            } else {
                return ResponseFormatter::error([
                    'status' => 400,
                    'message' => "Gagal mengubah data profile",
                ]);
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Terjadi kesalahan",
                'result' => $error
            ]);
        }
    }

    private function uploadFile($file, $users_id = null)
    {
        if ($file != null) {
            // delete file
            $this->deleteFile($users_id);
            // nama file
            $fileExp =  explode('.', $file->getClientOriginalName());
            $name = $fileExp[0];
            $ext = $fileExp[1];
            $name = time() . '-' . str_replace(' ', '-', $name) . '.' . $ext;

            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'image/users/';

            // upload file
            $file->move($tujuan_upload, $name);
        } else {
            if ($users_id == null) {
                $name = 'default.png';
            } else {
                $user = Profile::where('users_id', $users_id)->first();
                $name = $user->picture_profile;
            }
        }

        return $name;
    }

    private function deleteFile($users_id = null)
    {
        if ($users_id != null) {
            $profile = Profile::where('users_id', '=', $users_id)->first();
            $gambar = public_path() . '/image/users/' . $profile->picture_profile;
            if (file_exists($gambar)) {
                if ($profile->picture_profile != 'default.png') {
                    File::delete($gambar);
                }
            }
        }
    }
}
