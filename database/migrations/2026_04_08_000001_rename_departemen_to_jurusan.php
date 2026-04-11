<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Rename tabel departemen -> jurusan
        Schema::rename('departemen', 'jurusan');

        Schema::table('jurusan', function (Blueprint $table) {
            $table->renameColumn('kode_dept', 'kode_jurusan');
            $table->renameColumn('nama_dept', 'nama_jurusan');
        });
    }

    public function down()
    {
        Schema::table('jurusan', function (Blueprint $table) {
            $table->renameColumn('kode_jurusan', 'kode_dept');
            $table->renameColumn('nama_jurusan', 'nama_dept');
        });

        Schema::rename('jurusan', 'departemen');
    }
};
