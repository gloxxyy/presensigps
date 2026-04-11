@extends('layouts.admin.tabler')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Data Jurusan</h2>
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
                            <div class="row">
                                <div class="col-12">
                                    <a href="#" class="btn btn-primary" id="btnTambahJurusan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 5l0 14"></path>
                                            <path d="M5 12l14 0"></path>
                                        </svg>
                                        Tambah Data
                                    </a>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <form action="/jurusan" method="GET">
                                        <div class="row">
                                            <div class="col-10">
                                                <input type="text" name="nama_jurusan" id="nama_jurusan"
                                                    class="form-control" placeholder="Cari Jurusan..."
                                                    value="{{ Request('nama_jurusan') }}">
                                            </div>
                                            <div class="col-2">
                                                <button type="submit" class="btn btn-primary">Cari</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode Jurusan</th>
                                                <th>Nama Jurusan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($jurusan as $d)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $d->kode_jurusan }}</td>
                                                    <td>{{ $d->nama_jurusan }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="#" class="edit btn btn-info btn-sm"
                                                                kode_jurusan="{{ $d->kode_jurusan }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit"
                                                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                    <path d="M7 7h-1a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-1"></path>
                                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0-2.97-2.97l-8.415 8.385v3h3l8.385-8.415z"></path>
                                                                    <path d="M16 5l3 3"></path>
                                                                </svg>
                                                            </a>
                                                            <form action="/jurusan/{{ $d->kode_jurusan }}/delete" method="POST" style="margin-left:5px">
                                                                @csrf
                                                                <a class="btn btn-danger btn-sm delete-confirm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash-filled"
                                                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                        <path d="M20 6a1 1 0 0 1 .117 1.993l-.117 .007h-.081l-.919 11a3 3 0 0 1-2.824 2.995l-.176 .005h-8c-1.598 0-2.904-1.249-2.992-2.75l-.005-.167l-.923-11.083h-.08a1 1 0 0 1-.117-1.993l.117-.007h16z" stroke-width="0" fill="currentColor"></path>
                                                                        <path d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1-1.993.117l-.007-.117h-4l-.007.117a1 1 0 0 1-1.993-.117a2 2 0 0 1 1.85-1.995l.15-.005h4z" stroke-width="0" fill="currentColor"></path>
                                                                    </svg>
                                                                </a>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Jurusan --}}
    <div class="modal modal-blur fade" id="modal-inputjurusan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Jurusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/jurusan/store" method="POST" id="frmJurusan">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Kode Jurusan</label>
                            <input type="text" id="kode_jurusan" class="form-control" placeholder="Kode Jurusan" name="kode_jurusan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Jurusan</label>
                            <input type="text" id="nama_jurusan_input" class="form-control" name="nama_jurusan" placeholder="Nama Jurusan">
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary w-100">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Jurusan --}}
    <div class="modal modal-blur fade" id="modal-editjurusan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Jurusan</h5>
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
            $("#btnTambahJurusan").click(function() {
                $("#modal-inputjurusan").modal("show");
            });

            $(".edit").click(function() {
                var kode_jurusan = $(this).attr('kode_jurusan');
                $.ajax({
                    type: 'POST',
                    url: '/jurusan/edit',
                    cache: false,
                    data: { _token: "{{ csrf_token() }}", kode_jurusan: kode_jurusan },
                    success: function(respond) {
                        $("#loadeditform").html(respond);
                    }
                });
                $("#modal-editjurusan").modal("show");
            });

            $(".delete-confirm").click(function(e) {
                var form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus Data Jurusan?',
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
