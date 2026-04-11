<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SekolahController extends Controller
{
    public function index()
    {
        $sekolah = DB::table('sekolah')->orderBy('kode_sekolah')->get();
        return view('sekolah.index', compact('sekolah'));
    }

    public function store(Request $request)
    {
        $kode_sekolah  = $request->kode_sekolah;
        $nama_sekolah  = $request->nama_sekolah;
        $lokasi_sekolah = $request->lokasi_sekolah;
        $radius_sekolah = $request->radius_sekolah;

        try {
            $data = [
                'kode_sekolah'  => $kode_sekolah,
                'nama_sekolah'  => $nama_sekolah,
                'lokasi_sekolah' => $lokasi_sekolah,
                'radius_sekolah' => $radius_sekolah,
            ];
            DB::table('sekolah')->insert($data);
            return Redirect::back()->with(['success' => 'Data Sekolah Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Sekolah Gagal Disimpan']);
        }
    }

    public function edit(Request $request)
    {
        $kode_sekolah = $request->kode_sekolah;
        $sekolah = DB::table('sekolah')->where('kode_sekolah', $kode_sekolah)->first();
        return view('sekolah.edit', compact('sekolah'));
    }

    public function update(Request $request)
    {
        $kode_sekolah  = $request->kode_sekolah;
        $nama_sekolah  = $request->nama_sekolah;
        $lokasi_sekolah = $request->lokasi_sekolah;
        $radius_sekolah = $request->radius_sekolah;

        try {
            $data = [
                'nama_sekolah'  => $nama_sekolah,
                'lokasi_sekolah' => $lokasi_sekolah,
                'radius_sekolah' => $radius_sekolah,
            ];
            DB::table('sekolah')
                ->where('kode_sekolah', $kode_sekolah)
                ->update($data);
            return Redirect::back()->with(['success' => 'Data Sekolah Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Sekolah Gagal Diupdate']);
        }
    }

    public function delete($kode_sekolah)
    {
        $hapus = DB::table('sekolah')->where('kode_sekolah', $kode_sekolah)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Sekolah Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Sekolah Gagal Dihapus']);
        }
    }
}
