<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign key dulu jika ada, lalu set null, lalu tambahkan constraint lagi jika perlu
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE presensi DROP FOREIGN KEY fk_presensi_jk;');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE presensi MODIFY kode_jam_kerja VARCHAR(20) NULL;');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE presensi ADD CONSTRAINT fk_presensi_jk FOREIGN KEY (kode_jam_kerja) REFERENCES jam_kerja(kode_jam_kerja) ON DELETE SET NULL ON UPDATE CASCADE;');
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->string('kode_jam_kerja', 20)->nullable(false)->change();
        });
    }
};
