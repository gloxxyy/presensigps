<?php

namespace App\Http\Controllers;

use App\Models\Setjamkerja;
use App\Models\Setjamkerjadept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KonfigurasiController extends Controller
{
    public function lokasikantor()
    {
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        return view('konfigurasi.lokasikantor', compact('lok_kantor'));
    }

    public function updatelokasikantor(Request $request)
    {
        $lokasi_kantor = $request->lokasi_kantor;
        $radius        = $request->radius;

        $update = DB::table('konfigurasi_lokasi')->where('id', 1)->update([
            'lokasi_kantor' => $lokasi_kantor,
            'radius'        => $radius,
        ]);

        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function jamkerja()
    {
        $jam_kerja = DB::table('jam_kerja')->orderBy('kode_jam_kerja')->get();
        return view('konfigurasi.jamkerja', compact('jam_kerja'));
    }

    public function storejamkerja(Request $request)
    {
        $data = [
            'kode_jam_kerja'     => $request->kode_jam_kerja,
            'nama_jam_kerja'     => $request->nama_jam_kerja,
            'awal_jam_masuk'     => $request->awal_jam_masuk,
            'jam_masuk'          => $request->jam_masuk,
            'akhir_jam_masuk'    => $request->akhir_jam_masuk,
            'status_istirahat'   => $request->status_istirahat,
            'awal_jam_istirahat' => $request->awal_jam_istirahat,
            'akhir_jam_istirahat' => $request->akhir_jam_istirahat,
            'jam_pulang'         => $request->jam_pulang,
            'total_jam'          => $request->total_jam,
            'lintashari'         => $request->lintashari,
        ];
        try {
            DB::table('jam_kerja')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function editjamkerja(Request $request)
    {
        $kode_jam_kerja = $request->kode_jam_kerja;
        $jamkerja = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->first();
        return view('konfigurasi.editjamkerja', compact('jamkerja'));
    }

    public function updatejamkerja(Request $request)
    {
        $kode_jam_kerja = $request->kode_jam_kerja;
        $data = [
            'nama_jam_kerja'     => $request->nama_jam_kerja,
            'awal_jam_masuk'     => $request->awal_jam_masuk,
            'jam_masuk'          => $request->jam_masuk,
            'akhir_jam_masuk'    => $request->akhir_jam_masuk,
            'status_istirahat'   => $request->status_istirahat,
            'awal_jam_istirahat' => $request->awal_jam_istirahat,
            'akhir_jam_istirahat' => $request->akhir_jam_istirahat,
            'jam_pulang'         => $request->jam_pulang,
            'total_jam'          => $request->total_jam,
            'lintashari'         => $request->lintashari,
        ];
        try {
            DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function deletejamkerja($kode_jam_kerja)
    {
        $hapus = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function setjamkerja($nip)
    {
        $guru     = DB::table('guru')->where('nip', $nip)->first();
        $jamkerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        $cekjamkerja = DB::table('konfigurasi_jamkerja')->where('nip', $nip)->count();
        $bulan   = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        if ($cekjamkerja > 0) {
            $setjamkerja = DB::table('konfigurasi_jamkerja')->where('nip', $nip)->get();
            return view('konfigurasi.editsetjamkerja', compact('guru', 'jamkerja', 'setjamkerja', 'bulan'));
        } else {
            return view('konfigurasi.setjamkerja', compact('guru', 'jamkerja', 'bulan'));
        }
    }

    public function storesetjamkerja(Request $request)
    {
        $nip            = $request->nip;
        $hari           = $request->hari;
        $kode_jam_kerja = $request->kode_jam_kerja;

        for ($i = 0; $i < count($hari); $i++) {
            $data[] = [
                'nip'            => $nip,
                'hari'           => $hari[$i],
                'kode_jam_kerja' => $kode_jam_kerja[$i],
            ];
        }

        try {
            Setjamkerja::insert($data);
            return redirect('/guru')->with(['success' => 'Jam Mengajar Berhasil Di-Setting']);
        } catch (\Exception $e) {
            return redirect('/guru')->with(['warning' => 'Jam Mengajar Gagal Di-Setting']);
        }
    }

    public function updatesetjamkerja(Request $request)
    {
        $nip            = $request->nip;
        $hari           = $request->hari;
        $kode_jam_kerja = $request->kode_jam_kerja;

        for ($i = 0; $i < count($hari); $i++) {
            $data[] = [
                'nip'            => $nip,
                'hari'           => $hari[$i],
                'kode_jam_kerja' => $kode_jam_kerja[$i],
            ];
        }

        DB::beginTransaction();
        try {
            DB::table('konfigurasi_jamkerja')->where('nip', $nip)->delete();
            Setjamkerja::insert($data);
            DB::commit();
            return redirect('/guru')->with(['success' => 'Jam Mengajar Berhasil Di-Setting']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/guru')->with(['warning' => 'Jam Mengajar Gagal Di-Setting']);
        }
    }

    public function jamkerjadept()
    {
        $jamkerjadept = DB::table('konfigurasi_jk_dept')
            ->join('sekolah', 'konfigurasi_jk_dept.kode_sekolah', '=', 'sekolah.kode_sekolah')
            ->join('jurusan', 'konfigurasi_jk_dept.kode_jurusan', '=', 'jurusan.kode_jurusan')
            ->get();
        return view('konfigurasi.jamkerjadept', compact('jamkerjadept'));
    }

    public function createjamkerjadept()
    {
        $jamkerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        $sekolah  = DB::table('sekolah')->get();
        $jurusan  = DB::table('jurusan')->get();
        return view('konfigurasi.createjamkerjadept', compact('jamkerja', 'sekolah', 'jurusan'));
    }

    public function storejamkerjadept(Request $request)
    {
        $kode_sekolah  = $request->kode_sekolah;
        $kode_jurusan  = $request->kode_jurusan;
        $hari           = $request->hari;
        $kode_jam_kerja = $request->kode_jam_kerja;
        $kode_jk_dept   = "J" . $kode_sekolah . $kode_jurusan;

        DB::beginTransaction();
        try {
            DB::table('konfigurasi_jk_dept')->insert([
                'kode_jk_dept'  => $kode_jk_dept,
                'kode_sekolah'  => $kode_sekolah,
                'kode_jurusan'  => $kode_jurusan,
            ]);

            for ($i = 0; $i < count($hari); $i++) {
                $data[] = [
                    'kode_jk_dept'   => $kode_jk_dept,
                    'hari'           => $hari[$i],
                    'kode_jam_kerja' => $kode_jam_kerja[$i],
                ];
            }
            Setjamkerjadept::insert($data);
            DB::commit();
            return redirect('/konfigurasi/jamkerjajurusan')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/konfigurasi/jamkerjajurusan')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function editjamkerjadept($kode_jk_dept)
    {
        $jamkerja           = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        $sekolah            = DB::table('sekolah')->get();
        $jurusan            = DB::table('jurusan')->get();
        $jamkerjadept       = DB::table('konfigurasi_jk_dept')->where('kode_jk_dept', $kode_jk_dept)->first();
        $jamkerjadept_detail = DB::table('konfigurasi_jk_dept_detail')->where('kode_jk_dept', $kode_jk_dept)->get();
        return view('konfigurasi.editjamkerjadept', compact('jamkerja', 'sekolah', 'jurusan', 'jamkerjadept', 'jamkerjadept_detail'));
    }

    public function updatejamkerjadept($kode_jk_dept, Request $request)
    {
        $hari           = $request->hari;
        $kode_jam_kerja = $request->kode_jam_kerja;

        DB::beginTransaction();
        try {
            DB::table('konfigurasi_jk_dept_detail')->where('kode_jk_dept', $kode_jk_dept)->delete();
            for ($i = 0; $i < count($hari); $i++) {
                $data[] = [
                    'kode_jk_dept'   => $kode_jk_dept,
                    'hari'           => $hari[$i],
                    'kode_jam_kerja' => $kode_jam_kerja[$i],
                ];
            }
            Setjamkerjadept::insert($data);
            DB::commit();
            return redirect('/konfigurasi/jamkerjajurusan')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/konfigurasi/jamkerjajurusan')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function showjamkerjadept($kode_jk_dept)
    {
        $jamkerja           = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        $sekolah            = DB::table('sekolah')->get();
        $jurusan            = DB::table('jurusan')->get();
        $jamkerjadept       = DB::table('konfigurasi_jk_dept')->where('kode_jk_dept', $kode_jk_dept)->first();
        $jamkerjadept_detail = DB::table('konfigurasi_jk_dept_detail')
            ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('kode_jk_dept', $kode_jk_dept)->get();
        return view('konfigurasi.showjamkerjadept', compact('jamkerja', 'sekolah', 'jurusan', 'jamkerjadept', 'jamkerjadept_detail'));
    }

    public function deletejamkerjadept($kode_jk_dept)
    {
        try {
            DB::table('konfigurasi_jk_dept')->where('kode_jk_dept', $kode_jk_dept)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function storesetjamkerjabydate(Request $request)
    {
        $nip            = $request->nip;
        $tanggal        = $request->tanggal;
        $kode_jam_kerja = $request->kode_jam_kerja;

        $data = ['nip' => $nip, 'tanggal' => $tanggal, 'kode_jam_kerja' => $kode_jam_kerja];

        try {
            DB::table('konfigurasi_jamkerja_by_date')->insert($data);
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }

    public function getjamkerjabydate($nip, $bulan, $tahun)
    {
        $konfigurasijamkerjabydate = DB::table('konfigurasi_jamkerja_by_date')
            ->join('jam_kerja', 'konfigurasi_jamkerja_by_date.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nip', $nip)
            ->whereRaw('MONTH(tanggal)="' . $bulan . '"')
            ->whereRaw('YEAR(tanggal)="' . $tahun . '"')
            ->get();
        return view('konfigurasi.getjamkerjabydate', compact('konfigurasijamkerjabydate', 'nip'));
    }

    public function deletejamkerjabydate(Request $request)
    {
        $nip     = $request->nip;
        $tanggal = $request->tanggal;

        try {
            DB::table('konfigurasi_jamkerja_by_date')->where('nip', $nip)->where('tanggal', $tanggal)->delete();
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }
}
