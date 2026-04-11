<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Rename tabel karyawan -> guru
        Schema::rename('karyawan', 'guru');

        Schema::table('guru', function (Blueprint $table) {
            $table->renameColumn('nik', 'nip');
            $table->renameColumn('jabatan', 'mata_pelajaran');
            $table->renameColumn('kode_dept', 'kode_jurusan');
            $table->renameColumn('kode_cabang', 'kode_sekolah');
            // Tambah kolom face recognition
            $table->text('face_descriptor')->nullable()->after('foto');
            $table->timestamp('face_registered_at')->nullable()->after('face_descriptor');
        });
    }

    public function down()
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->dropColumn('face_descriptor');
            $table->dropColumn('face_registered_at');
            $table->renameColumn('nip', 'nik');
            $table->renameColumn('mata_pelajaran', 'jabatan');
            $table->renameColumn('kode_jurusan', 'kode_dept');
            $table->renameColumn('kode_sekolah', 'kode_cabang');
        });

        Schema::rename('guru', 'karyawan');
    }
};
