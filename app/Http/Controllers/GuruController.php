<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $kode_jurusan = Auth::guard('user')->user()->kode_jurusan;
        $kode_sekolah = Auth::guard('user')->user()->kode_sekolah;
        $user = User::find(Auth::guard('user')->user()->id);

        $query = Guru::query();
        $query->select('guru.*', 'nama_jurusan');
        $query->join('jurusan', 'guru.kode_jurusan', '=', 'jurusan.kode_jurusan');
        $query->orderBy('nama_lengkap');

        if (!empty($request->nama_guru)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_guru . '%');
        }

        if (!empty($request->kode_jurusan)) {
            $query->where('guru.kode_jurusan', $request->kode_jurusan);
        }

        if (!empty($request->kode_sekolah)) {
            $query->where('guru.kode_sekolah', $request->kode_sekolah);
        }

        if ($user->hasRole('admin departemen')) {
            $query->where('guru.kode_jurusan', $kode_jurusan);
            $query->where('guru.kode_sekolah', $kode_sekolah);
        }

        $guru = $query->paginate(10);
        $jurusan = DB::table('jurusan')->get();
        $sekolah = DB::table('sekolah')->orderBy('kode_sekolah')->get();

        return view('guru.index', compact('guru', 'jurusan', 'sekolah'));
    }

    public function store(Request $request)
    {
        $nip = $request->nip;
        $nama_lengkap = $request->nama_lengkap;
        $mata_pelajaran = $request->mata_pelajaran;
        $no_hp = $request->no_hp;
        $kode_jurusan = $request->kode_jurusan;
        $password = Hash::make('12345');
        $kode_sekolah = $request->kode_sekolah;

        if ($request->hasFile('foto')) {
            $foto = $nip . '.' . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = null;
        }

        try {
            $data = [
                'nip'            => $nip,
                'nama_lengkap'   => $nama_lengkap,
                'mata_pelajaran' => $mata_pelajaran,
                'no_hp'          => $no_hp,
                'kode_jurusan'   => $kode_jurusan,
                'foto'           => $foto,
                'password'       => $password,
                'kode_sekolah'   => $kode_sekolah,
            ];

            $simpan = DB::table('guru')->insert($data);
            if ($simpan) {
                if ($request->hasFile('foto')) {
                    $folderPath = 'public/uploads/guru/';
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Guru Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            $message = $e->getCode() == 23000
                ? 'Data dengan NIP ' . $nip . ' Sudah Ada'
                : 'Hubungi IT';
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan: ' . $message]);
        }
    }

    public function edit(Request $request)
    {
        $nip = $request->nip;
        $jurusan = DB::table('jurusan')->get();
        $sekolah = DB::table('sekolah')->orderBy('kode_sekolah')->get();
        $guru = DB::table('guru')->where('nip', $nip)->first();
        return view('guru.edit', compact('jurusan', 'guru', 'sekolah'));
    }

    public function update($nip, Request $request)
    {
        $nip = Crypt::decrypt($nip);
        $nip_baru = $request->nip_baru;
        $nama_lengkap = $request->nama_lengkap;
        $mata_pelajaran = $request->mata_pelajaran;
        $no_hp = $request->no_hp;
        $kode_jurusan = $request->kode_jurusan;
        $kode_sekolah = $request->kode_sekolah;
        $old_foto = $request->old_foto;

        if ($request->hasFile('foto')) {
            $foto = $nip . '.' . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $old_foto;
        }

        $cekNip = DB::table('guru')
            ->where('nip', $nip_baru)
            ->where('nip', '!=', $nip)
            ->count();

        if ($cekNip > 0) {
            return Redirect::back()->with(['warning' => 'NIP Sudah Digunakan']);
        }

        try {
            $data = [
                'nip'            => $nip_baru,
                'nama_lengkap'   => $nama_lengkap,
                'mata_pelajaran' => $mata_pelajaran,
                'no_hp'          => $no_hp,
                'kode_jurusan'   => $kode_jurusan,
                'foto'           => $foto,
                'kode_sekolah'   => $kode_sekolah,
            ];

            $update = DB::table('guru')->where('nip', $nip)->update($data);
            if ($update) {
                if ($request->hasFile('foto')) {
                    $folderPath = 'public/uploads/guru/';
                    $folderPathOld = 'public/uploads/guru/' . $old_foto;
                    Storage::delete($folderPathOld);
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Guru Berhasil Diupdate']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function delete($nip)
    {
        $delete = DB::table('guru')->where('nip', $nip)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Guru Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function resetpassword($nip)
    {
        $nip = Crypt::decrypt($nip);
        $password = Hash::make('12345');
        $reset = DB::table('guru')->where('nip', $nip)->update([
            'password' => $password
        ]);

        if ($reset) {
            return Redirect::back()->with(['success' => 'Password Guru Berhasil Direset']);
        } else {
            return Redirect::back()->with(['warning' => 'Password Gagal Direset']);
        }
    }

    public function lockandunlocklocation($nip)
    {
        try {
            $guru = DB::table('guru')->where('nip', $nip)->first();
            $status_location = $guru->status_location;
            DB::table('guru')->where('nip', $nip)->update([
                'status_location' => $status_location == '1' ? '0' : '1'
            ]);
            return Redirect::back()->with(['success' => 'Status Lokasi Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Status Lokasi Gagal Diupdate']);
        }
    }

    public function lockandunlockjamkerja($nip)
    {
        try {
            $guru = DB::table('guru')->where('nip', $nip)->first();
            $status_jam_kerja = $guru->status_jam_kerja;
            DB::table('guru')->where('nip', $nip)->update([
                'status_jam_kerja' => $status_jam_kerja == '1' ? '0' : '1'
            ]);
            return Redirect::back()->with(['success' => 'Status Jam Mengajar Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Status Jam Mengajar Gagal Diupdate']);
        }
    }

    // =========================================================
    // FACE RECOGNITION METHODS
    // =========================================================

    /**
     * Tampilkan halaman registrasi wajah guru
     */
    public function registerFace($nip)
    {
        $guru = DB::table('guru')->where('nip', $nip)->first();
        if (!$guru) {
            return Redirect::back()->with(['warning' => 'Data Guru Tidak Ditemukan']);
        }
        return view('guru.register-face', compact('guru'));
    }

    /**
     * Simpan face descriptor ke database
     */
    public function storeFaceDescriptor(Request $request)
    {
        $nip = $request->nip;
        $face_descriptor = $request->face_descriptor; // JSON string dari face-api.js

        if (empty($face_descriptor)) {
            return response()->json(['status' => 'error', 'message' => 'Face descriptor kosong']);
        }

        try {
            DB::table('guru')->where('nip', $nip)->update([
                'face_descriptor'    => $face_descriptor,
                'face_registered_at' => now(),
            ]);
            return response()->json(['status' => 'success', 'message' => 'Wajah berhasil didaftarkan']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan wajah']);
        }
    }

    /**
     * Ambil semua face descriptor untuk keperluan face recognition saat presensi
     */
    public function getFaceDescriptors()
    {
        $gurus = DB::table('guru')
            ->whereNotNull('face_descriptor')
            ->select('nip', 'nama_lengkap', 'face_descriptor')
            ->get();

        return response()->json($gurus);
    }
}
