@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->

                <h2 class="page-title">
                    Edit Set Jam Kerja Departemen
                </h2>
            </div>

        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <form action="/konfigurasi/jamkerjadept/{{ $jamkerjadept->kode_jk_dept }}/update" method="POST">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <select name="kode_sekolah" id="kode_sekolah" class="form-select" disabled>
                                    <option value="">Pilih Sekolah</option>
                                    @foreach ($sekolah as $d)
                                    <option {{ $jamkerjadept->kode_sekolah == $d->kode_sekolah ? 'selected' :'' }} value="{{ $d->kode_sekolah }}">{{ strtoupper($d->nama_sekolah) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <select name="kode_jurusan" id="kode_jurusan" class="form-select" disabled>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach ($jurusan as $d)
                                    <option {{ $jamkerjadept->kode_jurusan == $d->kode_jurusan ? 'selected' :'' }} value="{{ $d->kode_jurusan }}">{{ strtoupper($d->nama_jurusan) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">


                    <table class="table">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam Kerja</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jamkerjadept_detail as $s)
                            <tr>
                                <td>
                                    {{ $s->hari }}
                                    <input type="hidden" name="hari[]" value="{{ $s->hari }}">
                                </td>
                                <td>
                                    <select name="kode_jam_kerja[]" id="kode_jam_kerja" class="form-select">
                                        <option value="">Pilih Jam Kerja</option>
                                        @foreach ($jamkerja as $d)
                                        <option {{ $d->kode_jam_kerja==$s->kode_jam_kerja ? 'selected' : '' }} value="{{ $d->kode_jam_kerja }}">{{ $d->nama_jam_kerja }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button class="btn btn-primary w-100" type="submit">Simpan</button>


                </div>
                <div class="col-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="6">Master Jam Kerja</th>
                            </tr>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Awal Masuk</th>
                                <th>Jam Masuk</th>
                                <th>Akhir Masuk</th>
                                <th>Jam Pulang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jamkerja as $d)
                            <tr>
                                <td>{{ $d->kode_jam_kerja }}</td>
                                <td>{{ $d->nama_jam_kerja }}</td>
                                <td>{{ $d->awal_jam_masuk }}</td>
                                <td>{{ $d->jam_masuk }}</td>
                                <td>{{ $d->akhir_jam_masuk }}</td>
                                <td>{{ $d->jam_pulang }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
