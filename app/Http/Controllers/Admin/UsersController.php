<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UsersController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
        if ($request->ajax()) {
            $User = User::join('profile', 'profile.users_id', '=', 'users.id')
                ->select('*', 'users.id as users_id')
                ->get();

            return Datatables::of($User)
                ->addIndexColumn()
                ->addColumn('action', function ($User) {
                    $myId  = Auth::id();
                    $button = '
                    <a href="' . route('admin.users.edit', $User->users_id) . '" class="btn btn-primary shadow btn-xs sharp mr-1 btn-edit" data-id="' . $User->users_id . '" data-toggle="modal" data-target="#modalForm"><i class="fa fa-pencil"></i></a>';

                    if ($User->id != $myId) {
                        $button .= '
                    <form class="d-inline form-delete" method="post" action="' . route('admin.users.destroy', $User->users_id) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">  
                        ' . method_field('delete') . '
                        <button type="submit" class="btn btn-danger btn-delete shadow btn-xs sharp" data-id="' . $User->users_id . '"><i class="fa fa-trash"></i></button>
                    </form>                 
                    ';
                    }

                    $output = '<div class="text-center">
                        ' . $button . '
                        </div>';
                    return $output;
                })
                ->addColumn('gender_profile', function ($User) {
                    $output = $User->gender_profile == 'L' ? 'Laki-laki' : 'Perempuan';
                    return $output;
                })
                ->addColumn('picture_profile', function ($User) {
                    $gambar = $User->picture_profile != null ? public_path() . '/image/users/' . $User->picture_profile : false;
                    if (file_exists($gambar)) {
                        $gambarFix = asset('image/users/' . $User->picture_profile);
                    } else {
                        $gambarFix = asset('image/users/default.png');
                    }

                    $output = '
                        <img src="' . $gambarFix . '" class="w-100 img-thumbnail"></img>
                    ';


                    return $output;
                })
                ->rawColumns(['picture_profile', 'action'])
                ->make();
        }
        return view('admin.users.index');
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
            'email' => [
                'required', function ($attribute, $value, $fail) {
                    $email = $_POST['email'];
                    if ($_POST['page'] == 'add') {
                        $checkEmail = User::where('email', $email)->count();
                        if ($checkEmail > 0) {
                            $fail('Email sudah digunakan');
                        }
                    } else {
                        $checkEmail = User::where('email', $email)
                            ->where('id', '!=', $_POST['id'])->count();

                        if ($checkEmail > 0) {
                            $fail('Email sudah digunakan');
                        }
                    }
                }, 'email'
            ],
            'password' => [
                function ($attribute, $value, $fail) {
                    if ($_POST['page'] == 'add') {
                        $password = $_POST['password'];
                        $password_confirmation = $_POST['password_confirmation'];
                        if ($password == null) {
                            $fail('Password wajib diisi');
                        }
                        if ($password_confirmation != $password) {
                            $fail('Password tidak sama dengan password confirmation');
                        }
                    } else {
                        $password = $_POST['password'];
                        $password_confirmation = $_POST['password_confirmation'];
                        if ($password != null && $password_confirmation != null) {
                            if ($password_confirmation != $password) {
                                $fail('Password tidak sama dengan password confirmation');
                            }
                        }
                    }
                },
            ],
            'password_confirmation' =>  [
                function ($attribute, $value, $fail) {
                    if ($_POST['page'] == 'add') {
                        $password = $_POST['password'];
                        $password_confirmation = $_POST['password_confirmation'];
                        if ($password_confirmation == null) {
                            $fail('Password confirmation wajib diisi');
                        }
                        if ($password_confirmation != $password) {
                            $fail('Password tidak sama dengan password confirmation');
                        }
                    } else {
                        $password = $_POST['password'];
                        $password_confirmation = $_POST['password_confirmation'];
                        if ($password != null && $password_confirmation != null) {
                            if ($password_confirmation != $password) {
                                $fail('Password tidak sama dengan password confirmation');
                            }
                        }
                    }
                },
            ],
            'role' => 'required',
            'name_profile' => 'required',
            'gender_profile' => 'required',
            'telephone_profile' => 'required',
            'address_profile' => 'required',
            'picture_profile' => 'image|max:1024',
        ], [
            'email' => ':attribute tidak sesuai format',
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
            // menyimpan data file yang diupload ke variabel $file
            $file = $request->file('picture_profile');
            $picture_profile = $this->uploadFile($file);

            $user = User::create([
                'name' => $request->input('name_profile'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role' => $request->input('role'),
            ]);

            $profile = Profile::create([
                'name_profile' => $request->input('name_profile'),
                'gender_profile' => $request->input('gender_profile'),
                'telephone_profile' => $request->input('telephone_profile'),
                'address_profile' => $request->input('address_profile'),
                'picture_profile' => $picture_profile,
                'status_profile' => 'complete',
                'users_id' => $user->id
            ]);

            if ($user || $profile) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil menambahkan users",
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => "Gagal menambahkan users",
                ]);
            }
        } else {
            // menyimpan data file yang diupload ke variabel $file
            $file = $request->file('picture_profile');
            $id = $request->input('id');
            $picture_profile = $this->uploadFile($file, $id);

            $password = $request->input('password_old');
            $password_users = $request->input('password');
            if ($password_users != null) {
                $password = Hash::make($password_users);
            }

            $user = User::where('id', $id)->update([
                'name' => $request->input('name_profile'),
                'email' => $request->input('email'),
                'password' => $password,
                'role' => $request->input('role'),
            ]);

            $profile = Profile::where('users_id', $id)->update([
                'name_profile' => $request->input('name_profile'),
                'gender_profile' => $request->input('gender_profile'),
                'telephone_profile' => $request->input('telephone_profile'),
                'address_profile' => $request->input('address_profile'),
                'picture_profile' => $picture_profile,
            ]);

            if ($user || $profile) {
                return ResponseFormatter::success([
                    'status' => 200,
                    'message' => "Berhasil update users",
                ]);
            } else {
                return ResponseFormatter::success([
                    'status' => 400,
                    'message' => "Gagal update users",
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
        $User = User::join('profile', 'profile.users_id', '=', 'users.id')
            ->where('users.id', '=', $id)
            ->select('*', 'users.id as users_id')
            ->first();
        if ($User) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil mengambil users",
                'result' => $User
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal mengambil users",
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
        $User = User::destroy($id);
        if ($User) {
            return ResponseFormatter::success([
                'status' => 200,
                'message' => "Berhasil menghapus users",
            ]);
        } else {
            return ResponseFormatter::error([
                'status' => 400,
                'message' => "Gagal menghapus users",
            ]);
        }
    }

    private function uploadFile($file, $users_id = null)
    {
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
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
        if (!Gate::allows('user-admin')) {
            abort(403);
        }
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
