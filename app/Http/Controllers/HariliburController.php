<?php

namespace App\Http\Controllers;

use App\Models\Harilibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class HariliburController extends Controller
{
    public function index()
    {

        $query = Harilibur::query();
        $query->orderBy('kode_libur', 'desc');
        $harilibur = $query->paginate(10);
        return view('harilibur.index', compact('harilibur'));
    }

    public function create()
    {
        $sekolah = DB::table('sekolah')->orderBy('kode_sekolah')->get();
        return view('harilibur.create', compact('sekolah'));
    }

    public function store(Request $request)
    {
        //LB24001
        $tahun = date('Y', strtotime($request->tanggal_libur));
        $thn = substr($tahun, 2, 2);
        $lastlibur = DB::table('harilibur')
            ->whereRaw('YEAR(tanggal_libur)="' . $tahun . '"')
            ->orderBy('kode_libur', 'desc')
            ->first();
        $lastkodelibur = $lastlibur != null ? $lastlibur->kode_libur : "";
        $format = "LB"  . $thn;
        $kode_libur = buatkode($lastkodelibur, $format, 3);

        try {
            DB::table('harilibur')
                ->insert([
                    'kode_libur' => $kode_libur,
                    'tanggal_libur' => $request->tanggal_libur,
                    'kode_sekolah' => $request->kode_sekolah,
                    'keterangan' => $request->keterangan
                ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            //dd($e);
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }


    public function edit(Request $request)
    {
        $kode_libur = $request->kode_libur;
        $harilibur = DB::table('harilibur')->where('kode_libur', $kode_libur)->first();
        $sekolah = DB::table('sekolah')->orderBy('kode_sekolah')->get();
        return view('harilibur.edit', compact('sekolah', 'harilibur'));
    }


    public function update(Request $request, $kode_libur)
    {
        try {
            DB::table('harilibur')
                ->where('kode_libur', $kode_libur)
                ->update([
                    'tanggal_libur' => $request->tanggal_libur,
                    'kode_sekolah' => $request->kode_sekolah,
                    'keterangan' => $request->keterangan
                ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            //dd($e);
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function delete($kode_libur)
    {
        try {
            DB::table('harilibur')->where('kode_libur', $kode_libur)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }


    public function setkaryawanlibur($kode_libur)
    {
        $harilibur = DB::table('harilibur')
            ->join('sekolah', 'harilibur.kode_sekolah', '=', 'sekolah.kode_sekolah')
            ->where('kode_libur', $kode_libur)->first();
        return view('harilibur.setkaryawanlibur', compact('harilibur'));
    }


    public function setlistkaryawanlibur($kode_libur)
    {
        return view('harilibur.setlistkaryawanlibur', compact('kode_libur'));
    }


    public function getsetlistkaryawanlibur($kode_libur)
    {
        $harilibur = DB::table('harilibur')->where('kode_libur', $kode_libur)->first();
        $guru = DB::table('guru')
            ->select('guru.*', 'hariliburdetail.nip as ceknip')
            ->leftJoin(
                DB::raw("(
                SELECT nip FROM harilibur_detail
                WHERE kode_libur = '$kode_libur'
            ) hariliburdetail"),
                function ($join) {
                    $join->on('guru.nip', '=', 'hariliburdetail.nip');
                }
            )
            ->where('kode_sekolah', $harilibur->kode_sekolah)
            ->orderBy('nama_lengkap')
            ->get();
        return view('harilibur.getsetlistkaryawanlibur', compact('guru', 'kode_libur'));
    }


    public function storekaryawanlibur(Request $request)
    {
        try {
            $cek = DB::table('harilibur_detail')
                ->where('kode_libur', $request->kode_libur)
                ->where('nip', $request->nip)->count();
            if ($cek > 0) {
                return 1;
            }
            DB::table('harilibur_detail')->insert([
                'kode_libur' => $request->kode_libur,
                'nip' => $request->nip,
            ]);
            return 0;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    public function removekaryawanlibur(Request $request)
    {
        try {
            DB::table('harilibur_detail')
                ->where('kode_libur', $request->kode_libur)
                ->where('nip', $request->nip)
                ->delete();
            return 0;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    public function getkaryawanlibur($kode_libur)
    {
        $karyawanlibur = DB::table('harilibur_detail')
            ->join('guru', 'harilibur_detail.nip', '=', 'guru.nip')
            ->where('kode_libur', $kode_libur)
            ->get();

        return view('harilibur.getkaryawanlibur', compact('karyawanlibur', 'kode_libur'));
    }
}
