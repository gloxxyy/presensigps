<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Fix tabel harilibur: kode_cabang -> kode_sekolah
        if (Schema::hasColumn('harilibur', 'kode_cabang')) {
            Schema::table('harilibur', function (Blueprint $table) {
                $table->renameColumn('kode_cabang', 'kode_sekolah');
            });
        }

        // 2. Fix tabel harilibur_detail: nik -> nip
        if (Schema::hasColumn('harilibur_detail', 'nik')) {
            Schema::table('harilibur_detail', function (Blueprint $table) {
                $table->renameColumn('nik', 'nip');
            });
        }

        // 3. Drop VIEW lama dan buat ulang yang mereferensikan tabel guru + kolom nip
        DB::statement('DROP VIEW IF EXISTS `q_rekappresensi`');

        DB::statement("
            CREATE VIEW `q_rekappresensi` AS
            SELECT
                `guru`.`nip`,
                `guru`.`nama_lengkap`,
                `guru`.`mata_pelajaran`,
                `guru`.`kode_jurusan`,
                `guru`.`kode_sekolah`,
                COALESCE(`rek`.`jmlhadir`, 0)    AS jmlhadir,
                COALESCE(`rek`.`jmlizin`, 0)     AS jmlizin,
                COALESCE(`rek`.`jmlsakit`, 0)    AS jmlsakit,
                COALESCE(`rek`.`jmlcuti`, 0)     AS jmlcuti,
                COALESCE(`rek`.`jmlterlambat`, 0) AS jmlterlambat
            FROM `guru`
            LEFT JOIN (
                SELECT
                    `presensi`.`nip`,
                    SUM(IF(`status`='h',1,0))            AS jmlhadir,
                    SUM(IF(`status`='i',1,0))            AS jmlizin,
                    SUM(IF(`status`='s',1,0))            AS jmlsakit,
                    SUM(IF(`status`='c',1,0))            AS jmlcuti,
                    SUM(IF(`jam_in` > `jam_masuk`,1,0))  AS jmlterlambat
                FROM `presensi`
                LEFT JOIN `jam_kerja` ON `presensi`.`kode_jam_kerja` = `jam_kerja`.`kode_jam_kerja`
                GROUP BY `presensi`.`nip`
            ) AS `rek` ON `guru`.`nip` = `rek`.`nip`
        ");
    }

    public function down()
    {
        // Rollback: kembalikan nama kolom lama
        if (Schema::hasColumn('harilibur', 'kode_sekolah')) {
            Schema::table('harilibur', function (Blueprint $table) {
                $table->renameColumn('kode_sekolah', 'kode_cabang');
            });
        }

        if (Schema::hasColumn('harilibur_detail', 'nip')) {
            Schema::table('harilibur_detail', function (Blueprint $table) {
                $table->renameColumn('nip', 'nik');
            });
        }

        DB::statement('DROP VIEW IF EXISTS `q_rekappresensi`');
    }
};
