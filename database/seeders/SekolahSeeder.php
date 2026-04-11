<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sekolah')->updateOrInsert(
            ['kode_sekolah' => 'S01'],
            [
                'kode_sekolah'   => 'S01',
                'nama_sekolah'   => 'SMK Negeri 3 Pariaman',
                'lokasi_sekolah' => '-0.593966,100.107698',
                'radius_sekolah' => 100, // radius dalam meter
            ]
        );
    }
}
