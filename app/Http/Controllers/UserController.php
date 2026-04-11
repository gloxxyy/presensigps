<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $jurusan = DB::table('jurusan')->orderBy('kode_jurusan')->get();
        $role = DB::table('roles')->orderBy('id')->get();
        $query = User::query();
        $query->select('users.id', 'users.name', 'email', 'nama_jurusan', 'roles.name as role', 'kode_sekolah');
        $query->leftJoin('jurusan', 'users.kode_jurusan', '=', 'jurusan.kode_jurusan');
        $query->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id');
        $query->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id');
        if (!empty($request->name)) {
            $query->where('users.name', 'like', '%' . $request->name . '%');
        }
        $users = $query->paginate(10);
        $users->appends(request()->all());

        $sekolah = DB::table('sekolah')->orderBy('kode_sekolah')->get();
        return view('users.index', compact('users', 'jurusan', 'role', 'sekolah'));
    }


    public function store(Request $request)
    {
        $nama_user = $request->nama_user;
        $email = $request->email;
        $kode_jurusan = $request->kode_jurusan;
        $role = $request->role;
        $password = bcrypt($request->password);
        $kode_sekolah = $request->kode_sekolah;
        DB::beginTransaction();
        try {

            $user = User::create([
                'name' => $nama_user,
                'email' => $email,
                'kode_jurusan' => $kode_jurusan,
                'password' => $password,
                'kode_sekolah' => $kode_sekolah
            ]);

            $user->assignRole($role);

            DB::commit();

            return Redirect::back()->with(['success' => ' Data Berhasil Disimpan']);
        } catch (\Exception $e) {

            dd($e);
            DB::rollBack();
            return Redirect::back()->with(['warning' => ' Data Gagal Disimpan']);
        }
    }

    public function edit(Request $request)
    {
        $id_user = $request->id_user;
        $jurusan = DB::table('jurusan')->orderBy('kode_jurusan')->get();
        $role = DB::table('roles')->orderBy('id')->get();
        $sekolah = DB::table('sekolah')->orderBy('kode_sekolah')->get();
        $user = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('id', $id_user)->first();
        return view('users.edituser', compact('jurusan', 'role', 'user', 'sekolah'));
    }

    public function update(Request $request, $id_user)
    {
        $nama_user    = $request->nama_user;
        $email        = $request->email;
        $kode_jurusan = $request->kode_jurusan;
        $role         = $request->role;
        $kode_sekolah = $request->kode_sekolah;

        // Cek apakah email sudah dipakai user lain (bukan diri sendiri)
        $emailExists = DB::table('users')
            ->where('email', $email)
            ->where('id', '!=', $id_user)
            ->count();

        if ($emailExists > 0) {
            return Redirect::back()->with(['warning' => 'Email sudah digunakan oleh user lain!']);
        }

        // Hanya update password jika field terisi (bukan kosong)
        $data = [
            'name'         => $nama_user,
            'email'        => $email,
            'kode_jurusan' => $kode_jurusan,
            'kode_sekolah' => $kode_sekolah,
        ];

        if (!empty($request->password)) {
            $data['password'] = bcrypt($request->password);
        }

        DB::beginTransaction();
        try {
            // Update Data User
            DB::table('users')->where('id', $id_user)->update($data);

            // Update Data Role
            DB::table('model_has_roles')->where('model_id', $id_user)
                ->update(['role_id' => $role]);

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate: ' . $e->getMessage()]);
        }
    }



    public function delete($id_user)
    {
        try {
            DB::table('users')->where('id', $id_user)->delete();
            return Redirect::back()->with(['success' => ' Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => ' Data Gagal Dihapus']);
        }
    }
}
