<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Pengajuanizin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{

    public function gethari($hari)
    {
        switch ($hari) {
            case 'Sun': $hari_ini = "Minggu"; break;
            case 'Mon': $hari_ini = "Senin";  break;
            case 'Tue': $hari_ini = "Selasa"; break;
            case 'Wed': $hari_ini = "Rabu";   break;
            case 'Thu': $hari_ini = "Kamis";  break;
            case 'Fri': $hari_ini = "Jumat";  break;
            case 'Sat': $hari_ini = "Sabtu";  break;
            default:    $hari_ini = "Tidak Diketahui"; break;
        }
        return $hari_ini;
    }


    public function create()
    {
        $nip      = Auth::guard('guru')->user()->nip;
        $hariini  = date("Y-m-d");
        $kode_sekolah = Auth::guard('guru')->user()->kode_sekolah;
        $lok_sekolah  = DB::table('sekolah')->where('kode_sekolah', $kode_sekolah)->first();

        $presensi     = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nip', $nip);
        $cek          = $presensi->count();
        $datapresensi = $presensi->first();

        return view('presensi.create', compact('cek', 'lok_sekolah', 'hariini', 'datapresensi'));
    }

    public function store(Request $request)
    {
        // === Ambil data dasar ===
        $nip             = Auth::guard('guru')->user()->nip;
        $status_location = Auth::guard('guru')->user()->status_location;
        $hariini         = date("Y-m-d");
        $jam             = date("H:i:s");
        $jam_hms         = date("H:i");

        // === Window presensi: 07:00 - 17:00 ===
        $JAM_BUKA = "07:00";
        $JAM_TUTUP = "17:00";
        if ($jam_hms < $JAM_BUKA) {
            echo "error|Presensi belum dibuka. Mulai pukul {$JAM_BUKA}|in";
            return;
        }
        if ($jam_hms > $JAM_TUTUP) {
            echo "error|Presensi sudah ditutup. Waktu presensi berakhir pukul {$JAM_TUTUP}|in";
            return;
        }

        // === Cek radius geolokasi ===
        $kode_sekolah     = Auth::guard('guru')->user()->kode_sekolah;
        $lok_sekolah      = DB::table('sekolah')->where('kode_sekolah', $kode_sekolah)->first();
        $lok              = explode(",", $lok_sekolah->lokasi_sekolah);
        $latitudesekolah  = $lok[0];
        $longitudesekolah = $lok[1];
        $lokasi           = $request->lokasi;
        $lokasiuser       = explode(",", $lokasi);
        $latitudeuser     = $lokasiuser[0];
        $longitudeuser    = $lokasiuser[1];

        $jarak  = $this->distance($latitudesekolah, $longitudesekolah, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        if ($status_location == 1 && $radius > $lok_sekolah->radius_sekolah) {
            echo "error|Maaf Anda Berada Diluar Radius, Jarak Anda " . $radius . " meter dari Sekolah|radius";
            return;
        }

        // === Cek presensi hari ini ===
        $presensi     = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nip', $nip);
        $cek          = $presensi->count();
        $datapresensi = $presensi->first();

        // === Proses foto ===
        $ket         = $cek > 0 ? "out" : "in";
        $image       = $request->image;
        $folderPath  = "public/uploads/absensi/";
        $formatName  = $nip . "-" . $hariini . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName    = $formatName . ".png";
        $file        = $folderPath . $fileName;

        // === Absen Masuk ===
        if ($cek == 0) {
            $data = [
                'nip'            => $nip,
                'tgl_presensi'   => $hariini,
                'jam_in'         => $jam,
                'foto_in'        => $fileName,
                'lokasi_in'      => $lokasi,
                'kode_jam_kerja' => null,
                'status'         => 'h'
            ];
            $simpan = DB::table('presensi')->insert($data);
            if ($simpan) {
                Storage::put($file, $image_base64);
                echo "success|Terimakasih, Selamat Mengajar 🎉|in";
            } else {
                echo "error|Maaf Gagal Absen, Hubungi Admin|in";
            }

        // === Absen Pulang ===
        } else {
            if (!empty($datapresensi->jam_out)) {
                echo "error|Anda Sudah Melakukan Absen Pulang Sebelumnya!|out";
                return;
            }
            $data_pulang = [
                'jam_out'    => $jam,
                'foto_out'   => $fileName,
                'lokasi_out' => $lokasi
            ];
            $update = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nip', $nip)->update($data_pulang);
            if ($update) {
                Storage::put($file, $image_base64);
                echo "success|Terimakasih, Hati-Hati Di Jalan 👋|out";
            } else {
                echo "error|Maaf Gagal Absen, Hubungi Admin|out";
            }
        }
    }




    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta     = $lon1 - $lon2;
        $miles     = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles     = acos($miles);
        $miles     = rad2deg($miles);
        $miles     = $miles * 60 * 1.1515;
        $feet      = $miles * 5280;
        $yards     = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters    = $kilometers * 1000;
        return compact('meters');
    }

    public function editprofile()
    {
        $nip  = Auth::guard('guru')->user()->nip;
        $guru = DB::table('guru')->where('nip', $nip)->first();
        return view('presensi.editprofile', compact('guru'));
    }

    public function updateprofile(Request $request)
    {
        $nip          = Auth::guard('guru')->user()->nip;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp        = $request->no_hp;
        $password     = Hash::make($request->password);
        $guru         = DB::table('guru')->where('nip', $nip)->first();

        $request->validate(['foto' => 'image|mimes:png,jpg|max:1024']);

        if ($request->hasFile('foto')) {
            $foto = $nip . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $guru->foto;
        }

        if (empty($request->password)) {
            $data = ['nama_lengkap' => $nama_lengkap, 'no_hp' => $no_hp, 'foto' => $foto];
        } else {
            $data = ['nama_lengkap' => $nama_lengkap, 'no_hp' => $no_hp, 'password' => $password, 'foto' => $foto];
        }

        $update = DB::table('guru')->where('nip', $nip)->update($data);
        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/guru/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Profil Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['error' => 'Profil Gagal Diupdate']);
        }
    }

    public function histori()
    {
        $namabulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nip   = Auth::guard('guru')->user()->nip;

        $histori = DB::table('presensi')
            ->select('presensi.*', 'keterangan', 'jam_kerja.*', 'doc_sid', 'nama_cuti')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->leftJoin('master_cuti', 'pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
            ->where('presensi.nip', $nip)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();

        return view('presensi.gethistori', compact('histori'));
    }

    public function izin(Request $request)
    {
        $nip = Auth::guard('guru')->user()->nip;

        if (!empty($request->bulan) && !empty($request->tahun)) {
            $dataizin = DB::table('pengajuan_izin')
                ->leftJoin('master_cuti', 'pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
                ->orderBy('tgl_izin_dari', 'desc')
                ->where('nip', $nip)
                ->whereRaw('MONTH(tgl_izin_dari)="' . $request->bulan . '"')
                ->whereRaw('YEAR(tgl_izin_dari)="' . $request->tahun . '"')
                ->get();
        } else {
            $dataizin = DB::table('pengajuan_izin')
                ->leftJoin('master_cuti', 'pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
                ->orderBy('tgl_izin_dari', 'desc')
                ->where('nip', $nip)->limit(5)->orderBy('tgl_izin_dari', 'desc')
                ->get();
        }

        $namabulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return view('presensi.izin', compact('dataizin', 'namabulan'));
    }

    public function buatizin()
    {
        return view('presensi.buatizin');
    }

    public function storeizin(Request $request)
    {
        $nip        = Auth::guard('guru')->user()->nip;
        $tgl_izin   = $request->tgl_izin;
        $status     = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'nip'        => $nip,
            'tgl_izin'   => $tgl_izin,
            'status'     => $status,
            'keterangan' => $keterangan,
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);
        if ($simpan) {
            return redirect('/presensi/izin')->with(['success' => 'Data Izin Berhasil Disimpan']);
        } else {
            return redirect('/presensi/izin')->with(['error' => 'Data Izin Gagal Disimpan']);
        }
    }

    public function monitoring()
    {
        $sekolah  = DB::table('sekolah')->orderBy('kode_sekolah')->get();
        $jurusan  = DB::table('jurusan')->orderBy('kode_jurusan')->get();
        return view('presensi.monitoring', compact('sekolah', 'jurusan'));
    }

    public function getpresensi(Request $request)
    {
        $kode_jurusan = Auth::guard('user')->user()->kode_jurusan;
        $kode_sekolah = Auth::guard('user')->user()->kode_sekolah;
        $user         = User::find(Auth::guard('user')->user()->id);

        $tanggal = $request->tanggal;

        $query = Guru::query();
        $query->selectRaw(
            'guru.nip, nama_lengkap, guru.kode_jurusan, guru.kode_sekolah,
            datapresensi.id,jam_in,jam_out,foto_in,foto_out,lokasi_in,lokasi_out,
            datapresensi.status,jam_masuk, nama_jam_kerja, jam_pulang, keterangan'
        );
        $query->leftJoin(
            DB::raw("(
                SELECT
                presensi.nip,presensi.id,jam_in,jam_out,foto_in,foto_out,lokasi_in,lokasi_out,presensi.status,jam_masuk, nama_jam_kerja, jam_pulang, keterangan
                FROM presensi
                LEFT JOIN  jam_kerja ON presensi.kode_jam_kerja = jam_kerja.kode_jam_kerja
                LEFT JOIN pengajuan_izin ON presensi.kode_izin = pengajuan_izin.kode_izin
                WHERE tgl_presensi = '$tanggal'
            ) datapresensi"),
            function ($join) {
                $join->on('guru.nip', '=', 'datapresensi.nip');
            }
        );

        if (!empty($request->kode_sekolah)) {
            $query->where('guru.kode_sekolah', $request->kode_sekolah);
        }

        if (!empty($request->kode_jurusan)) {
            $query->where('guru.kode_jurusan', $request->kode_jurusan);
        }

        if ($user->hasRole('admin departemen')) {
            $query->where('guru.kode_sekolah', $kode_sekolah);
            $query->where('guru.kode_jurusan', $kode_jurusan);
        }

        $query->orderBy('nama_lengkap');
        $presensi = $query->get();

        return view('presensi.getpresensi', compact('presensi', 'tanggal'));
    }

    public function tampilkanpeta(Request $request)
    {
        $id       = $request->id;
        $presensi = DB::table('presensi')->where('id', $id)
            ->join('guru', 'presensi.nip', '=', 'guru.nip')
            ->first();
        return view('presensi.showmap', compact('presensi'));
    }

    public function laporan()
    {
        $kode_jurusan = Auth::guard('user')->user()->kode_jurusan;
        $kode_sekolah = Auth::guard('user')->user()->kode_sekolah;
        $user         = User::find(Auth::guard('user')->user()->id);

        $namabulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        if ($user->hasRole('admin departemen')) {
            $guru = DB::table('guru')
                ->where('kode_jurusan', $kode_jurusan)
                ->where('kode_sekolah', $kode_sekolah)
                ->orderBy('nama_lengkap')->get();
        } else if ($user->hasRole('administrator')) {
            $guru = DB::table('guru')->orderBy('nama_lengkap')->get();
        }

        return view('presensi.laporan', compact('namabulan', 'guru'));
    }

    public function cetaklaporan(Request $request)
    {
        $nip       = $request->nip;
        $bulan     = $request->bulan;
        $tahun     = $request->tahun;
        $namabulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $guru = DB::table('guru')->where('nip', $nip)
            ->join('jurusan', 'guru.kode_jurusan', '=', 'jurusan.kode_jurusan')
            ->first();

        $presensi = DB::table('presensi')
            ->select('presensi.*', 'keterangan', 'jam_kerja.*')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->where('presensi.nip', $nip)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();

        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y H:i:s");
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan Presensi Guru $time.xls");
            return view('presensi.cetaklaporanexcel', compact('bulan', 'tahun', 'namabulan', 'guru', 'presensi'));
        }
        return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'guru', 'presensi'));
    }

    public function rekap()
    {
        $namabulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $jurusan  = DB::table('jurusan')->get();
        $sekolah  = DB::table('sekolah')->orderBy('kode_sekolah')->get();
        return view('presensi.rekap', compact('namabulan', 'jurusan', 'sekolah'));
    }

    public function cetakrekap(Request $request)
    {
        $bulan        = $request->bulan;
        $tahun        = $request->tahun;
        $kode_jurusan = $request->kode_jurusan;
        $kode_sekolah = $request->kode_sekolah;
        $dari         = $tahun . "-" . $bulan . "-01";
        $sampai       = date("Y-m-t", strtotime($dari));

        $namabulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $datalibur = getkaryawanlibur($dari, $sampai);
        $harilibur = DB::table('harilibur')->whereBetween('tanggal_libur', [$dari, $sampai])->get();

        $select_date = "";
        $field_date  = "";
        $i = 1;
        while (strtotime($dari) <= strtotime($sampai)) {
            $rangetanggal[] = $dari;
            $select_date .= "MAX(IF(tgl_presensi = '$dari',
            CONCAT(
            IFNULL(jam_in,'NA'),'|',
            IFNULL(jam_out,'NA'),'|',
            IFNULL(presensi.status,'NA'),'|',
            IFNULL(nama_jam_kerja,'NA'),'|',
            IFNULL(jam_masuk,'NA'),'|',
            IFNULL(jam_pulang,'NA'),'|',
            IFNULL(presensi.kode_izin,'NA'),'|',
            IFNULL(keterangan,'NA'),'|',
            IFNULL(total_jam,'NA'),'|',
            IFNULL(lintashari,'NA'),'|',
            IFNULL(awal_jam_istirahat,'NA'),'|',
            IFNULL(akhir_jam_istirahat,'NA'),'|'
            ),NULL)) as tgl_" . $i . ",";
            $field_date .= "tgl_" . $i . ",";
            $i++;
            $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
        }

        $jmlhari   = count($rangetanggal);
        $lastrange = $jmlhari - 1;
        $sampai    = $rangetanggal[$lastrange];
        if ($jmlhari == 30)      array_push($rangetanggal, NULL);
        else if ($jmlhari == 29) array_push($rangetanggal, NULL, NULL);
        else if ($jmlhari == 28) array_push($rangetanggal, NULL, NULL, NULL);

        $query = Guru::query();
        $query->selectRaw("$field_date guru.nip, nama_lengkap, mata_pelajaran");
        $query->leftJoin(
            DB::raw("(
                SELECT
                $select_date
                presensi.nip
                FROM presensi
                LEFT JOIN  jam_kerja ON presensi.kode_jam_kerja = jam_kerja.kode_jam_kerja
                LEFT JOIN pengajuan_izin ON presensi.kode_izin = pengajuan_izin.kode_izin
                WHERE tgl_presensi BETWEEN '$rangetanggal[0]' AND '$sampai'
                GROUP BY nip
            ) presensi"),
            function ($join) {
                $join->on('guru.nip', '=', 'presensi.nip');
            }
        );

        if (!empty($kode_jurusan)) {
            $query->where('kode_jurusan', $kode_jurusan);
        }
        if (!empty($kode_sekolah)) {
            $query->where('kode_sekolah', $kode_sekolah);
        }

        $query->orderBy('nama_lengkap');
        $rekap = $query->get();

        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y H:i:s");
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Rekap Presensi Guru $time.xls");
        }

        $data = ['bulan', 'tahun', 'namabulan', 'rekap', 'rangetanggal', 'jmlhari', 'datalibur', 'harilibur'];

        if ($request->jenis_laporan == 1) {
            return view('presensi.cetakrekap', compact($data));
        } else if ($request->jenis_laporan == 2) {
            return view('presensi.cetakrekap_detail', compact($data));
        }
    }

    public function izinsakit(Request $request)
    {
        $kode_jurusan = Auth::guard('user')->user()->kode_jurusan;
        $kode_sekolah = Auth::guard('user')->user()->kode_sekolah;
        $user         = User::find(Auth::guard('user')->user()->id);

        $query = Pengajuanizin::query();
        $query->select(
            'kode_izin', 'tgl_izin_dari', 'tgl_izin_sampai',
            'pengajuan_izin.nip', 'nama_lengkap', 'mata_pelajaran',
            'status', 'status_approved', 'keterangan',
            'guru.kode_sekolah', 'guru.kode_jurusan', 'doc_sid'
        );
        $query->join('guru', 'pengajuan_izin.nip', '=', 'guru.nip');

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_izin_dari', [$request->dari, $request->sampai]);
        }
        if (!empty($request->nip)) {
            $query->where('pengajuan_izin.nip', $request->nip);
        }
        if (!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
        }
        if ($request->status_approved === '0' || $request->status_approved === '1' || $request->status_approved === '2') {
            $query->where('status_approved', $request->status_approved);
        }
        if ($user->hasRole('admin departemen')) {
            $query->where('guru.kode_jurusan', $kode_jurusan);
            $query->where('guru.kode_sekolah', $kode_sekolah);
        }
        if (!empty($request->kode_sekolah)) {
            $query->where('guru.kode_sekolah', $request->kode_sekolah);
        }
        if (!empty($request->kode_jurusan)) {
            $query->where('guru.kode_jurusan', $request->kode_jurusan);
        }

        $query->orderBy('tgl_izin_dari', 'desc');
        $izinsakit = $query->paginate(10);
        $izinsakit->appends($request->all());

        $sekolah = DB::table('sekolah')->orderBy('kode_sekolah')->get();
        $jurusan = DB::table('jurusan')->orderBy('kode_jurusan')->get();
        return view('presensi.izinsakit', compact('izinsakit', 'sekolah', 'jurusan'));
    }

    public function approveizinsakit(Request $request)
    {
        $status_approved = $request->status_approved;
        $kode_izin       = $request->kode_izin_form;
        $dataizin        = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $nip             = $dataizin->nip;
        $tgl_dari        = $dataizin->tgl_izin_dari;
        $tgl_sampai      = $dataizin->tgl_izin_sampai;
        $status          = $dataizin->status;

        DB::beginTransaction();
        try {
            if ($status_approved == 1) {
                while (strtotime($tgl_dari) <= strtotime($tgl_sampai)) {
                    DB::table('presensi')->insert([
                        'nip'        => $nip,
                        'tgl_presensi' => $tgl_dari,
                        'status'     => $status,
                        'kode_izin'  => $kode_izin
                    ]);
                    $tgl_dari = date("Y-m-d", strtotime("+1 days", strtotime($tgl_dari)));
                }
            }

            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                'status_approved' => $status_approved
            ]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diproses']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Diproses']);
        }
    }

    public function batalkanizinsakit($kode_izin)
    {
        DB::beginTransaction();
        try {
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update(['status_approved' => 0]);
            DB::table('presensi')->where('kode_izin', $kode_izin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dibatalkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan']);
        }
    }

    public function cekpengajuanizin(Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $nip      = Auth::guard('guru')->user()->nip;
        $cek      = DB::table('pengajuan_izin')->where('nip', $nip)->where('tgl_izin', $tgl_izin)->count();
        return $cek;
    }

    public function showact($kode_izin)
    {
        $dataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        return view('presensi.showact', compact('dataizin'));
    }

    public function deleteizin($kode_izin)
    {
        $cekdataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $doc_sid     = $cekdataizin->doc_sid;

        try {
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->delete();
            if ($doc_sid != null) {
                Storage::delete('/public/uploads/sid/' . $doc_sid);
            }
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Dihapus']);
        }
    }

    public function koreksipresensi(Request $request)
    {
        $nip      = $request->nip;
        $guru     = DB::table('guru')->where('nip', $nip)->first();
        $tanggal  = $request->tanggal;
        $presensi = DB::table('presensi')->where('nip', $nip)->where('tgl_presensi', $tanggal)->first();
        $jamkerja = DB::table('jam_kerja')->orderBy('kode_jam_kerja')->get();
        return view('presensi.koreksipresensi', compact('guru', 'tanggal', 'jamkerja', 'presensi'));
    }

    public function storekoreksipresensi(Request $request)
    {
        $status         = $request->status;
        $nip            = $request->nip;
        $tanggal        = $request->tanggal;
        $jam_in         = $status == "a" ? NULL : $request->jam_in;
        $jam_out        = $status == "a" ? NULL : $request->jam_out;
        $kode_jam_kerja = $status == "a" ? NULL : $request->kode_jam_kerja;

        try {
            $cekpresensi = DB::table('presensi')->where('nip', $nip)->where('tgl_presensi', $tanggal)->count();
            if ($cekpresensi > 0) {
                DB::table('presensi')
                    ->where('nip', $nip)
                    ->where('tgl_presensi', $tanggal)
                    ->update([
                        'jam_in'         => $jam_in,
                        'jam_out'        => $jam_out,
                        'kode_jam_kerja' => $kode_jam_kerja,
                        'status'         => $status
                    ]);
            } else {
                DB::table('presensi')->insert([
                    'nip'            => $nip,
                    'tgl_presensi'   => $tanggal,
                    'jam_in'         => $jam_in,
                    'jam_out'        => $jam_out,
                    'kode_jam_kerja' => $kode_jam_kerja,
                    'status'         => $status
                ]);
            }
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function pilihjamkerja()
    {
        $nip      = Auth::guard('guru')->user()->nip;
        $hariini  = date('Y-m-d');
        $cekpresensi = DB::table('presensi')->where('nip', $nip)->where('tgl_presensi', $hariini)->first();
        if (!empty($cekpresensi)) {
            $kode_jam_kerja = Crypt::encrypt($cekpresensi->kode_jam_kerja);
            return redirect('/presensi/' . $kode_jam_kerja . '/create');
        }
        $jamkerja = DB::table('jam_kerja')->get();
        return view('presensi.pilihjamkerja', compact('jamkerja'));
    }
}
