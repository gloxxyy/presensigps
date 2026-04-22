<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\HariliburController;
use App\Http\Controllers\IzinabsenController;
use App\Http\Controllers\IzincutiController;
use App\Http\Controllers\IzinsakitController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\UserController;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ----- Auth Guru (login page) -----
Route::middleware(['guest:guru'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
});

// ----- Auth Admin (panel) -----
Route::middleware(['guest:web'])->group(function () {
    Route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');

    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
});

// ----- Routes Guru (terautentikasi) -----
Route::middleware(['auth:guru'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);

    // Presensi
    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::get('/presensi/pilihjamkerja', [PresensiController::class, 'pilihjamkerja']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);
    Route::get('/presensi/scanqr', [PresensiController::class, 'scanqr']);
    Route::post('/presensi/storeqr', [PresensiController::class, 'storeqr']);

    // Edit Profile
    Route::get('/editprofile', [PresensiController::class, 'editprofile']);
    Route::post('/presensi/{nip}/updateprofile', [PresensiController::class, 'updateprofile']);

    // Histori Presensi
    Route::get('/presensi/histori', [PresensiController::class, 'histori']);
    Route::post('/gethistori', [PresensiController::class, 'gethistori']);

    // Izin
    Route::get('/presensi/izin', [PresensiController::class, 'izin']);
    Route::get('/presensi/buatizin', [PresensiController::class, 'buatizin']);
    Route::post('/presensi/storeizin', [PresensiController::class, 'storeizin']);
    Route::post('/presensi/cekpengajuanizin', [PresensiController::class, 'cekpengajuanizin']);

    // Izin Absen
    Route::get('/izinabsen', [IzinabsenController::class, 'create']);
    Route::post('/izinabsen/store', [IzinabsenController::class, 'store']);
    Route::get('/izinabsen/{kode_izin}/edit', [IzinabsenController::class, 'edit']);
    Route::post('/izinabsen/{kode_izin}/update', [IzinabsenController::class, 'update']);

    // Izin Sakit
    Route::get('/izinsakit', [IzinsakitController::class, 'create']);
    Route::post('/izinsakit/store', [IzinsakitController::class, 'store']);
    Route::get('/izinsakit/{kode_izin}/edit', [IzinsakitController::class, 'edit']);
    Route::post('/izinsakit/{kode_izin}/update', [IzinsakitController::class, 'update']);

    // Izin Cuti
    Route::get('/izincuti', [IzincutiController::class, 'create']);
    Route::post('/izincuti/store', [IzincutiController::class, 'store']);
    Route::get('/izincuti/{kode_izin}/edit', [IzincutiController::class, 'edit']);
    Route::post('/izincuti/{kode_izin}/update', [IzincutiController::class, 'update']);
    Route::post('/izincuti/getmaxcuti', [IzincutiController::class, 'getmaxcuti']);

    Route::get('/izin/{kode_izin}/showact', [PresensiController::class, 'showact']);
    Route::get('/izin/{kode_izin}/delete', [PresensiController::class, 'deleteizin']);

    // Face Recognition (Guru side)
    Route::get('/presensi/get-face-descriptors', [GuruController::class, 'getFaceDescriptors']);
});


// ----- Routes Administrator & Admin Jurusan -----
Route::group(['middleware' => ['auth:web', 'role:administrator|admin departemen,web']], function () {
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin']);
    Route::get('/panel/dashboardadmin', [DashboardController::class, 'dashboardadmin']);

    // Data Guru
    Route::get('/guru', [GuruController::class, 'index']);
    Route::get('/guru/{nip}/resetpassword', [GuruController::class, 'resetpassword']);
    Route::get('/guru/{nip}/register-face', [GuruController::class, 'registerFace']);

    // Konfigurasi Jam Mengajar
    Route::get('/konfigurasi/{nip}/setjamkerja', [KonfigurasiController::class, 'setjamkerja']);
    Route::post('/konfigurasi/storesetjamkerja', [KonfigurasiController::class, 'storesetjamkerja']);
    Route::post('/konfigurasi/updatesetjamkerja', [KonfigurasiController::class, 'updatesetjamkerja']);
    Route::post('/konfigurasi/storesetjamkerjabydate', [KonfigurasiController::class, 'storesetjamkerjabydate']);
    Route::get('/konfigurasi/{nip}/{bulan}/{tahun}/getjamkerjabydate', [KonfigurasiController::class, 'getjamkerjabydate']);
    Route::post('/konfigurasi/deletejamkerjabydate', [KonfigurasiController::class, 'deletejamkerjabydate']);

    // Monitoring & Laporan Presensi
    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring']);
    Route::post('/getpresensi', [PresensiController::class, 'getpresensi']);
    Route::post('/tampilkanpeta', [PresensiController::class, 'tampilkanpeta']);
    Route::get('/presensi/laporan', [PresensiController::class, 'laporan']);
    Route::post('/presensi/cetaklaporan', [PresensiController::class, 'cetaklaporan']);
    Route::get('/presensi/rekap', [PresensiController::class, 'rekap']);
    Route::post('/presensi/cetakrekap', [PresensiController::class, 'cetakrekap']);

    Route::get('/presensi/izinsakit', [PresensiController::class, 'izinsakit']);

    Route::post('/koreksipresensi', [PresensiController::class, 'koreksipresensi']);
    Route::post('/storekoreksipresensi', [PresensiController::class, 'storekoreksipresensi']);

    // Face Recognition (Admin side)
    Route::post('/guru/store-face-descriptor', [GuruController::class, 'storeFaceDescriptor']);
});


// ----- Routes Khusus Administrator -----
Route::group(['middleware' => ['auth:web', 'role:administrator,web']], function () {

    // Data Guru (CRUD Penuh)
    Route::post('/guru/store', [GuruController::class, 'store']);
    Route::post('/guru/edit', [GuruController::class, 'edit']);
    Route::post('/guru/{nip}/update', [GuruController::class, 'update']);
    Route::post('/guru/{nip}/delete', [GuruController::class, 'delete']);
    Route::get('/guru/{nip}/lockandunlocklocation', [GuruController::class, 'lockandunlocklocation']);
    Route::get('/guru/{nip}/lockandunlockjamkerja', [GuruController::class, 'lockandunlockjamkerja']);

    // Data Jurusan
    Route::get('/jurusan', [JurusanController::class, 'index'])->middleware('permission:view-departemen,user');
    Route::post('/jurusan/store', [JurusanController::class, 'store']);
    Route::post('/jurusan/edit', [JurusanController::class, 'edit']);
    Route::post('/jurusan/{kode_jurusan}/update', [JurusanController::class, 'update']);
    Route::post('/jurusan/{kode_jurusan}/delete', [JurusanController::class, 'delete']);

    // Presensi Admin
    Route::post('/presensi/approveizinsakit', [PresensiController::class, 'approveizinsakit']);
    Route::get('/presensi/{kode_izin}/batalkanizinsakit', [PresensiController::class, 'batalkanizinsakit']);

    // Data Sekolah
    Route::get('/sekolah', [SekolahController::class, 'index']);
    Route::post('/sekolah/store', [SekolahController::class, 'store']);
    Route::post('/sekolah/edit', [SekolahController::class, 'edit']);
    Route::post('/sekolah/update', [SekolahController::class, 'update']);
    Route::post('/sekolah/{kode_sekolah}/delete', [SekolahController::class, 'delete']);

    // Konfigurasi
    Route::get('/konfigurasi/lokasikantor', [KonfigurasiController::class, 'lokasikantor']);
    Route::post('/konfigurasi/updatelokasikantor', [KonfigurasiController::class, 'updatelokasikantor']);
    Route::get('/konfigurasi/jamkerja', [KonfigurasiController::class, 'jamkerja']);
    Route::post('/konfigurasi/storejamkerja', [KonfigurasiController::class, 'storejamkerja']);
    Route::post('/konfigurasi/editjamkerja', [KonfigurasiController::class, 'editjamkerja']);
    Route::post('/konfigurasi/updatejamkerja', [KonfigurasiController::class, 'updatejamkerja']);
    Route::post('/konfigurasi/jamkerja/{kode_jam_kerja}/delete', [KonfigurasiController::class, 'deletejamkerja']);

    Route::get('/konfigurasi/jamkerjajurusan', [KonfigurasiController::class, 'jamkerjadept']);
    Route::get('/konfigurasi/jamkerjajurusan/create', [KonfigurasiController::class, 'createjamkerjadept']);
    Route::post('/konfigurasi/jamkerjajurusan/store', [KonfigurasiController::class, 'storejamkerjadept']);
    Route::get('/konfigurasi/jamkerjajurusan/{kode_jk_dept}/edit', [KonfigurasiController::class, 'editjamkerjadept']);
    Route::post('/konfigurasi/jamkerjajurusan/{kode_jk_dept}/update', [KonfigurasiController::class, 'updatejamkerjadept']);
    Route::get('/konfigurasi/jamkerjajurusan/{kode_jk_dept}/show', [KonfigurasiController::class, 'showjamkerjadept']);
    Route::get('/konfigurasi/jamkerjajurusan/{kode_jk_dept}/delete', [KonfigurasiController::class, 'deletejamkerjadept']);

    // Users
    Route::get('/konfigurasi/users', [UserController::class, 'index']);
    Route::post('/konfigurasi/users/store', [UserController::class, 'store']);
    Route::post('/konfigurasi/users/edit', [UserController::class, 'edit']);
    Route::post('/konfigurasi/users/{id_user}/update', [UserController::class, 'update']);
    Route::post('/konfigurasi/users/{id_user}/delete', [UserController::class, 'delete']);

    // Hari Libur
    Route::get('/konfigurasi/harilibur', [HariliburController::class, 'index']);
    Route::get('/konfigurasi/harilibur/create', [HariliburController::class, 'create']);
    Route::post('/konfigurasi/harilibur/store', [HariliburController::class, 'store']);
    Route::post('/konfigurasi/harilibur/edit', [HariliburController::class, 'edit']);
    Route::post('/konfigurasi/harilibur/{kode_libur}/update', [HariliburController::class, 'update']);
    Route::post('/konfigurasi/harilibur/{kode_libur}/delete', [HariliburController::class, 'delete']);
    Route::get('/konfigurasi/harilibur/{kode_libur}/setkaryawanlibur', [HariliburController::class, 'setkaryawanlibur']);
    Route::get('/konfigurasi/harilibur/{kode_libur}/setlistkaryawanlibur', [HariliburController::class, 'setlistkaryawanlibur']);
    Route::get('/konfigurasi/harilibur/{kode_libur}/getsetlistkaryawanlibur', [HariliburController::class, 'getsetlistkaryawanlibur']);
    Route::post('/konfigurasi/harilibur/storekaryawanlibur', [HariliburController::class, 'storekaryawanlibur']);
    Route::post('/konfigurasi/harilibur/removekaryawanlibur', [HariliburController::class, 'removekaryawanlibur']);
    Route::get('/konfigurasi/harilibur/{kode_libur}/getkaryawanlibur', [HariliburController::class, 'getkaryawanlibur']);

    // Cuti
    Route::get('/cuti', [CutiController::class, 'index']);
    Route::post('/cuti/store', [CutiController::class, 'store']);
    Route::post('/cuti/edit', [CutiController::class, 'edit']);
    Route::post('/cuti/{kode_cuti}/update', [CutiController::class, 'update']);
    Route::post('/cuti/{kode_cuti}/delete', [CutiController::class, 'delete']);
});


// ----- Utility Routes (Setup) -----
Route::get('/createrolepermission', function () {
    try {
        Role::create(['name' => 'admin departemen']);
        echo "Sukses";
    } catch (\Exception $e) {
        echo "Error";
    }
});

Route::get('/give-user-role', function () {
    try {
        $user = User::findorfail(1);
        $user->assignRole('administrator');
        echo "Sukses";
    } catch (\Exception $e) {
        echo "Error";
    }
});

Route::get('/give-role-permission', function () {
    try {
        $role = Role::findorfail(1);
        $role->givePermissionTo('view-departemen');
        echo "Sukses";
    } catch (\Exception $e) {
        echo "Error";
    }
});
