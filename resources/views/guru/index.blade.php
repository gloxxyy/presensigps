@extends('layouts.admin.tabler')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Data Guru</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    @if (Session::get('success'))
                                        <div class="alert alert-success">{{ Session::get('success') }}</div>
                                    @endif
                                    @if (Session::get('warning'))
                                        <div class="alert alert-warning">{{ Session::get('warning') }}</div>
                                    @endif
                                </div>
                            </div>
                            @role('administrator', 'user')
                                <div class="row">
                                    <div class="col-12">
                                        <a href="#" class="btn btn-primary" id="btnTambahGuru">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus"
                                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M12 5l0 14"></path>
                                                <path d="M5 12l14 0"></path>
                                            </svg>
                                            Tambah Data Guru
                                        </a>
                                    </div>
                                </div>
                            @endrole

                            <div class="row mt-2">
                                <div class="col-12">
                                    <form action="/guru" method="GET">
                                        <div class="row">
                                            <div class="col-4">
                                                <input type="text" name="nama_guru" id="nama_guru"
                                                    class="form-control" placeholder="Nama Guru"
                                                    value="{{ Request('nama_guru') }}">
                                            </div>
                                            @role('administrator', 'user')
                                                <div class="col-3">
                                                    <select name="kode_jurusan" id="kode_jurusan" class="form-select">
                                                        <option value="">Semua Jurusan</option>
                                                        @foreach ($jurusan as $j)
                                                            <option {{ Request('kode_jurusan') == $j->kode_jurusan ? 'selected' : '' }}
                                                                value="{{ $j->kode_jurusan }}">{{ $j->nama_jurusan }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endrole
                                            <div class="col-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search"
                                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                                        <path d="M21 21l-6 -6"></path>
                                                    </svg>
                                                    Cari
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-12">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIP</th>
                                                <th>Nama Guru</th>
                                                <th>Mata Pelajaran</th>
                                                <th>No. HP</th>
                                                <th>Foto</th>
                                                <th>Jurusan</th>
                                                <th>Wajah</th>
                                                <th>Lokasi</th>
                                                <th>Jam Mengajar</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($guru as $d)
                                                @php
                                                    $path = Storage::url('uploads/guru/' . $d->foto);
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration + $guru->firstItem() - 1 }}</td>
                                                    <td>{{ $d->nip }}</td>
                                                    <td>{{ $d->nama_lengkap }}</td>
                                                    <td>{{ $d->mata_pelajaran }}</td>
                                                    <td>{{ $d->no_hp }}</td>
                                                    <td>
                                                        @if (empty($d->foto))
                                                            <img src="{{ asset('assets/img/nophoto.png') }}" class="avatar" alt="">
                                                        @else
                                                            <img src="{{ url($path) }}" class="avatar" alt="">
                                                        @endif
                                                    </td>
                                                    <td>{{ $d->nama_jurusan }}</td>

                                                    {{-- Status Wajah --}}
                                                    <td class="text-center">
                                                        @if (!empty($d->face_descriptor))
                                                            <span class="badge bg-success">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/></svg>
                                                                Terdaftar
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">Belum</span>
                                                        @endif
                                                        <a href="/guru/{{ $d->nip }}/register-face" class="btn btn-xs btn-outline-primary ms-1" title="Daftarkan Wajah">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8V6a2 2 0 0 1 2-2h2"/><path d="M4 16v2a2 2 0 0 0 2 2h2"/><path d="M16 4h2a2 2 0 0 1 2 2v2"/><path d="M16 20h2a2 2 0 0 0 2-2v-2"/><circle cx="12" cy="12" r="3"/></svg>
                                                        </a>
                                                    </td>

                                                    {{-- Status Lokasi --}}
                                                    <td class="text-center">
                                                        @if ($d->status_location == 1)
                                                            <a href="/guru/{{ $d->nip }}/lockandunlocklocation">
                                                                <span class="badge bg-danger badge-sm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 13a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2v-6z"/><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0-2 0"/><path d="M8 11v-4a4 4 0 1 1 8 0v4"/></svg>
                                                                </span>
                                                            </a>
                                                        @else
                                                            <a href="/guru/{{ $d->nip }}/lockandunlocklocation">
                                                                <span class="badge bg-success badge-sm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock-open" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 11m0 2a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2z"/><path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0-2 0"/><path d="M8 11v-5a4 4 0 0 1 8 0"/></svg>
                                                                </span>
                                                            </a>
                                                        @endif
                                                    </td>

                                                    {{-- Status Jam Mengajar --}}
                                                    <td class="text-center">
                                                        @if ($d->status_jam_kerja == 1)
                                                            <a href="/guru/{{ $d->nip }}/lockandunlockjamkerja">
                                                                <span class="badge bg-danger badge-sm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 13a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2v-6z"/><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0-2 0"/><path d="M8 11v-4a4 4 0 1 1 8 0v4"/></svg>
                                                                </span>
                                                            </a>
                                                        @else
                                                            <a href="/guru/{{ $d->nip }}/lockandunlockjamkerja">
                                                                <span class="badge bg-success badge-sm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock-open" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 11m0 2a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2z"/><path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0-2 0"/><path d="M8 11v-5a4 4 0 0 1 8 0"/></svg>
                                                                </span>
                                                            </a>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            @role('administrator', 'user')
                                                                <a href="#" class="edit btn btn-info btn-sm" nip="{{ $d->nip }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M7 7h-1a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-1"></path><path d="M20.385 6.585a2.1 2.1 0 0 0-2.97-2.97l-8.415 8.385v3h3l8.385-8.415z"></path><path d="M16 5l3 3"></path></svg>
                                                                </a>
                                                            @endrole

                                                            <a href="/konfigurasi/{{ $d->nip }}/setjamkerja" class="btn btn-success btn-sm" title="Set Jam Mengajar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37c1 .608 2.296.07 2.572-1.065z"></path><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0"></path></svg>
                                                            </a>

                                                            <a href="/guru/{{ Crypt::encrypt($d->nip) }}/resetpassword" class="btn btn-sm btn-warning" title="Reset Password">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-key-off" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.17 6.159l2.316-2.316a2.877 2.877 0 0 1 4.069 0l3.602 3.602a2.877 2.877 0 0 1 0 4.069l-2.33 2.33"/><path d="M14.931 14.948a2.863 2.863 0 0 1-1.486-.79l-.301-.302l-6.558 6.558a2 2 0 0 1-1.239.578l-.175.008h-1.172a1 1 0 0 1-.993-.883l-.007-.117v-1.172a2 2 0 0 1 .467-1.284l.119-.13l.414-.414h2v-2h2v-2l2.144-2.144l-.301-.301a2.863 2.863 0 0 1-.794-1.504"/><path d="M15 9h.01"/><path d="M3 3l18 18"/></svg>
                                                            </a>

                                                            @role('administrator', 'user')
                                                                <form action="/guru/{{ $d->nip }}/delete" method="POST">
                                                                    @csrf
                                                                    <a class="btn btn-danger btn-sm delete-confirm">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash-filled" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M20 6a1 1 0 0 1 .117 1.993l-.117.007h-.081l-.919 11a3 3 0 0 1-2.824 2.995l-.176.005h-8c-1.598 0-2.904-1.249-2.992-2.75l-.005-.167l-.923-11.083h-.08a1 1 0 0 1-.117-1.993l.117-.007h16z" stroke-width="0" fill="currentColor"></path><path d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1-1.993.117l-.007-.117h-4l-.007.117a1 1 0 0 1-1.993-.117a2 2 0 0 1 1.85-1.995l.15-.005h4z" stroke-width="0" fill="currentColor"></path></svg>
                                                                    </a>
                                                                </form>
                                                            @endrole
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $guru->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Guru --}}
    <div class="modal modal-blur fade" id="modal-inputguru" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/guru/store" method="POST" id="frmGuru" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" id="nip" class="form-control" placeholder="NIP" name="nip">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" class="form-control" name="nama_lengkap" placeholder="Nama Lengkap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mata Pelajaran</label>
                            <input type="text" id="mata_pelajaran" class="form-control" name="mata_pelajaran" placeholder="Mata Pelajaran">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" id="no_hp" class="form-control" name="no_hp" placeholder="No. HP">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jurusan</label>
                            <select name="kode_jurusan" id="kode_jurusan_modal" class="form-select">
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach ($jurusan as $j)
                                    <option value="{{ $j->kode_jurusan }}">{{ $j->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sekolah</label>
                            <select name="kode_sekolah" id="kode_sekolah_modal" class="form-select">
                                <option value="">-- Pilih Sekolah --</option>
                                @foreach ($sekolah as $s)
                                    <option value="{{ $s->kode_sekolah }}">{{ strtoupper($s->nama_sekolah) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary w-100">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Guru --}}
    <div class="modal modal-blur fade" id="modal-editguru" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="loadeditform"></div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            $("#btnTambahGuru").click(function() {
                $("#modal-inputguru").modal("show");
            });

            $(".edit").click(function() {
                var nip = $(this).attr('nip');
                $.ajax({
                    type: 'POST',
                    url: '/guru/edit',
                    cache: false,
                    data: { _token: "{{ csrf_token() }}", nip: nip },
                    success: function(respond) {
                        $("#loadeditform").html(respond);
                    }
                });
                $("#modal-editguru").modal("show");
            });

            $(".delete-confirm").click(function(e) {
                var form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus Data Guru?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) { form.submit(); }
                });
            });
        });
    </script>
@endpush
