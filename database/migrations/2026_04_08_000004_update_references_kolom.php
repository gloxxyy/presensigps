<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Update tabel presensi: nik -> nip
        Schema::table('presensi', function (Blueprint $table) {
            $table->renameColumn('nik', 'nip');
        });

        // Update tabel konfigurasi_jamkerja: nik -> nip
        Schema::table('konfigurasi_jamkerja', function (Blueprint $table) {
            $table->renameColumn('nik', 'nip');
        });

        // Update tabel konfigurasi_jamkerja_by_date: nik -> nip
        Schema::table('konfigurasi_jamkerja_by_date', function (Blueprint $table) {
            $table->renameColumn('nik', 'nip');
        });

        // Update tabel konfigurasi_jk_dept: kode_dept -> kode_jurusan, kode_cabang -> kode_sekolah
        Schema::table('konfigurasi_jk_dept', function (Blueprint $table) {
            $table->renameColumn('kode_dept', 'kode_jurusan');
            $table->renameColumn('kode_cabang', 'kode_sekolah');
        });

        // Update tabel pengajuan_izin: nik -> nip
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            $table->renameColumn('nik', 'nip');
        });

        // Update tabel users: kode_dept -> kode_jurusan, kode_cabang -> kode_sekolah
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('kode_dept', 'kode_jurusan');
            $table->renameColumn('kode_cabang', 'kode_sekolah');
        });
    }

    public function down()
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->renameColumn('nip', 'nik');
        });

        Schema::table('konfigurasi_jamkerja', function (Blueprint $table) {
            $table->renameColumn('nip', 'nik');
        });

        Schema::table('konfigurasi_jamkerja_by_date', function (Blueprint $table) {
            $table->renameColumn('nip', 'nik');
        });

        Schema::table('konfigurasi_jk_dept', function (Blueprint $table) {
            $table->renameColumn('kode_jurusan', 'kode_dept');
            $table->renameColumn('kode_sekolah', 'kode_cabang');
        });

        Schema::table('pengajuan_izin', function (Blueprint $table) {
            $table->renameColumn('nip', 'nik');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('kode_jurusan', 'kode_dept');
            $table->renameColumn('kode_sekolah', 'kode_cabang');
        });
    }
};
