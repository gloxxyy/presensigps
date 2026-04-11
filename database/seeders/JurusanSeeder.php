<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $jurusan = [
            [
                'kode_jurusan' => 'APT',
                'nama_jurusan' => 'Agribisnis Perikanan Air Tawar',
            ],
            [
                'kode_jurusan' => 'NKI',
                'nama_jurusan' => 'Nautika Kapal Penangkap Ikan',
            ],
            [
                'kode_jurusan' => 'RPL',
                'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            ],
            [
                'kode_jurusan' => 'TKJ',
                'nama_jurusan' => 'Teknik Komputer dan Jaringan',
            ],
            [
                'kode_jurusan' => 'TPU',
                'nama_jurusan' => 'Teknik Pendinginan dan Tata Udara',
            ],
            [
                'kode_jurusan' => 'TKI',
                'nama_jurusan' => 'Teknika Kapal Penangkap Ikan',
            ],
        ];

        foreach ($jurusan as $item) {
            DB::table('jurusan')->updateOrInsert(
                ['kode_jurusan' => $item['kode_jurusan']],
                $item
            );
        }
    }
}
