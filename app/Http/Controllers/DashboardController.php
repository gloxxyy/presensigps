<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini   = date('Y-m-d');
        $bulanini  = date('m') * 1;
        $tahunini  = date('Y');
        $nip       = Auth::guard('guru')->user()->nip;
        $kode_sekolah = Auth::guard('guru')->user()->kode_sekolah;
        $kode_jurusan = Auth::guard('guru')->user()->kode_jurusan;

        $presensihariini = DB::table('presensi')->where('nip', $nip)->where('tgl_presensi', $hariini)->first();

        $historibulanini = DB::table('presensi')
            ->select('presensi.*', 'keterangan', 'jam_kerja.*', 'doc_sid', 'nama_cuti')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->leftJoin('master_cuti', 'pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
            ->where('presensi.nip', $nip)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->orderBy('tgl_presensi', 'desc')
            ->get();

        $rekappresensi = DB::table('presensi')
            ->selectRaw('
            SUM(IF(status="h",1,0)) as jmlhadir,
            SUM(IF(status="i",1,0)) as jmlizin,
            SUM(IF(status="s",1,0)) as jmlsakit,
            SUM(IF(status="c",1,0)) as jmlcuti,
            SUM(IF(jam_in > jam_masuk ,1,0)) as jmlterlambat
            ')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nip', $nip)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->first();

        $leaderboard = DB::table('presensi')
            ->join('guru', 'presensi.nip', '=', 'guru.nip')
            ->where('tgl_presensi', $hariini)
            ->orderBy('jam_in')
            ->get();

        $namabulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $sekolah  = DB::table('sekolah')->where('kode_sekolah', $kode_sekolah)->first();
        $jurusan  = DB::table('jurusan')->where('kode_jurusan', $kode_jurusan)->first();

        return view('dashboard.dashboard', compact(
            'presensihariini', 'historibulanini', 'namabulan',
            'bulanini', 'tahunini', 'rekappresensi', 'leaderboard',
            'sekolah', 'jurusan'
        ));
    }

    public function dashboardadmin()
    {
        $hariini = date('Y-m-d');

        $rekappresensi = DB::table('presensi')
            ->selectRaw('
            SUM(IF(status="h",1,0)) as jmlhadir,
            SUM(IF(status="i",1,0)) as jmlizin,
            SUM(IF(status="s",1,0)) as jmlsakit,
            SUM(IF(status="c",1,0)) as jmlcuti,
            SUM(IF(jam_in > jam_masuk ,1,0)) as jmlterlambat
            ')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('tgl_presensi', $hariini)
            ->first();

        return view('dashboard.dashboardadmin', compact('rekappresensi'));
    }
}
