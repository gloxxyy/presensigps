<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Rename tabel cabang -> sekolah
        Schema::rename('cabang', 'sekolah');

        Schema::table('sekolah', function (Blueprint $table) {
            $table->renameColumn('kode_cabang', 'kode_sekolah');
            $table->renameColumn('nama_cabang', 'nama_sekolah');
            $table->renameColumn('lokasi_cabang', 'lokasi_sekolah');
            $table->renameColumn('radius_cabang', 'radius_sekolah');
        });
    }

    public function down()
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->renameColumn('kode_sekolah', 'kode_cabang');
            $table->renameColumn('nama_sekolah', 'nama_cabang');
            $table->renameColumn('lokasi_sekolah', 'lokasi_cabang');
            $table->renameColumn('radius_sekolah', 'radius_cabang');
        });

        Schema::rename('sekolah', 'cabang');
    }
};
