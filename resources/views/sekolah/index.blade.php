@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Data Sekolah</h2>
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

                        <div class="row mb-2">
                            <div class="col-12">
                                <a href="#" class="btn btn-primary" id="btnTambahSekolah">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 5l0 14"></path>
                                        <path d="M5 12l14 0"></path>
                                    </svg>
                                    Tambah Data Sekolah
                                </a>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Sekolah</th>
                                            <th>Nama Sekolah</th>
                                            <th>Lokasi (Koordinat)</th>
                                            <th>Radius</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sekolah as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->kode_sekolah }}</td>
                                            <td>{{ $d->nama_sekolah }}</td>
                                            <td>
                                                <code>{{ $d->lokasi_sekolah }}</code>
                                                @if(!empty($d->lokasi_sekolah))
                                                    <a href="https://maps.google.com/?q={{ $d->lokasi_sekolah }}" target="_blank" class="ms-1 text-primary" title="Lihat di Google Maps">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ $d->radius_sekolah }} Meter</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="#" class="edit btn btn-info btn-sm"
                                                        kode_sekolah="{{ $d->kode_sekolah }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit"
                                                            width="20" height="20" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M7 7h-1a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-1"></path>
                                                            <path d="M20.385 6.585a2.1 2.1 0 0 0-2.97-2.97l-8.415 8.385v3h3l8.385-8.415z"></path>
                                                            <path d="M16 5l3 3"></path>
                                                        </svg>
                                                    </a>
                                                    <form action="/sekolah/{{ $d->kode_sekolah }}/delete" method="POST" style="margin-left:5px">
                                                        @csrf
                                                        <a class="btn btn-danger btn-sm delete-confirm">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash-filled"
                                                                width="20" height="20" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M20 6a1 1 0 0 1 .117 1.993l-.117.007h-.081l-.919 11a3 3 0 0 1-2.824 2.995l-.176.005h-8c-1.598 0-2.904-1.249-2.992-2.75l-.005-.167l-.923-11.083h-.08a1 1 0 0 1-.117-1.993l.117-.007h16z" stroke-width="0" fill="currentColor"></path>
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

{{-- Modal Tambah Sekolah --}}
<div class="modal modal-blur fade" id="modal-inputsekolah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Sekolah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/sekolah/store" method="POST" id="frmSekolah">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode Sekolah</label>
                                <input type="text" id="kode_sekolah" class="form-control" placeholder="Contoh: SKL01" name="kode_sekolah">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Sekolah</label>
                                <input type="text" id="nama_sekolah" class="form-control" name="nama_sekolah" placeholder="Contoh: SMAN 1 Kota">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Lokasi Koordinat
                                    <small class="text-muted">(klik peta untuk mengisi otomatis)</small>
                                </label>
                                <input type="text" id="lokasi_sekolah" class="form-control" name="lokasi_sekolah"
                                    placeholder="Klik peta atau isi manual: -7.123,110.456" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Radius (meter)</label>
                                <input type="number" id="radius_sekolah" class="form-control" name="radius_sekolah"
                                    placeholder="Contoh: 100" value="100">
                                <small class="text-muted">Guru hanya bisa absen dalam radius ini dari sekolah</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                📍 Klik peta untuk pilih lokasi sekolah
                            </label>
                            <div id="mapTambah" style="height: 280px; border-radius: 8px; border: 1px solid #dee2e6;"></div>
                            <small class="text-muted mt-1 d-block">Atau cari lokasi: <a href="https://maps.google.com" target="_blank">Google Maps</a> → klik kanan → "What's here?" → salin koordinat</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M10 14l11-11"></path>
                                    <path d="M21 3l-6.5 18a.55.55 0 0 1-1 0l-3.5-7l-7-3.5a.55.55 0 0 1 0-1l18-6.5"></path>
                                </svg>
                                Simpan Data Sekolah
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Sekolah --}}
<div class="modal modal-blur fade" id="modal-editsekolah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Sekolah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="loadeditform"></div>
        </div>
    </div>
</div>

@endsection

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
@endpush

@push('myscript')
<script>
    let mapTambah = null;
    let markerTambah = null;

    // Inisialisasi peta di modal Tambah
    function initMapTambah() {
        if (mapTambah) return; // sudah diinit

        // Default: Indonesia tengah
        mapTambah = L.map('mapTambah').setView([-2.5, 117.5], 5);

        L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0','mt1','mt2','mt3']
        }).addTo(mapTambah);

        mapTambah.on('click', function(e) {
            var lat = e.latlng.lat.toFixed(7);
            var lng = e.latlng.lng.toFixed(7);

            // Set nilai ke input
            document.getElementById('lokasi_sekolah').value = lat + ',' + lng;

            // Update marker
            if (markerTambah) {
                markerTambah.setLatLng(e.latlng);
            } else {
                markerTambah = L.marker(e.latlng).addTo(mapTambah);
            }
            markerTambah.bindPopup('📍 Lokasi Sekolah dipilih').openPopup();
        });

        // Invalidate size setelah modal terbuka (fix peta tidak tampil penuh)
        setTimeout(() => { mapTambah.invalidateSize(); }, 300);
    }

    $(function() {
        $("#btnTambahSekolah").click(function() {
            $("#modal-inputsekolah").modal("show");
        });

        // Inisialisasi peta setelah modal terbuka
        $('#modal-inputsekolah').on('shown.bs.modal', function() {
            initMapTambah();
        });

        $(".edit").click(function() {
            var kode_sekolah = $(this).attr('kode_sekolah');
            $.ajax({
                type: 'POST',
                url: '/sekolah/edit',
                cache: false,
                data: { _token: "{{ csrf_token() }}", kode_sekolah: kode_sekolah },
                success: function(respond) {
                    $("#loadeditform").html(respond);
                    // Inisialisasi peta edit setelah content dimuat
                    setTimeout(initMapEdit, 400);
                }
            });
            $("#modal-editsekolah").modal("show");
        });

        $(".delete-confirm").click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Data Sekolah?',
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

        // Validasi form
        $("#frmSekolah").submit(function() {
            if ($("#kode_sekolah").val() == "") {
                Swal.fire({ title:'Warning!', text:'Kode Sekolah harus diisi!', icon:'warning', confirmButtonText:'Ok' })
                    .then(() => { $("#kode_sekolah").focus(); });
                return false;
            }
            if ($("#nama_sekolah").val() == "") {
                Swal.fire({ title:'Warning!', text:'Nama Sekolah harus diisi!', icon:'warning', confirmButtonText:'Ok' })
                    .then(() => { $("#nama_sekolah").focus(); });
                return false;
            }
            if ($("#lokasi_sekolah").val() == "") {
                Swal.fire({ title:'Warning!', text:'Lokasi Sekolah harus diisi! Klik peta untuk memilih lokasi.', icon:'warning', confirmButtonText:'Ok' });
                return false;
            }
            if ($("#radius_sekolah").val() == "") {
                Swal.fire({ title:'Warning!', text:'Radius harus diisi!', icon:'warning', confirmButtonText:'Ok' })
                    .then(() => { $("#radius_sekolah").focus(); });
                return false;
            }
        });
    });

    // Fungsi init peta untuk modal Edit (dipanggil setelah AJAX load)
    function initMapEdit() {
        var lokasiVal = document.getElementById('lokasi_sekolah_edit') ?
                        document.getElementById('lokasi_sekolah_edit').value : '';
        var defaultLat = -2.5, defaultLng = 117.5, defaultZoom = 5;

        if (lokasiVal) {
            var lok = lokasiVal.split(',');
            if (lok.length == 2) {
                defaultLat = parseFloat(lok[0]);
                defaultLng = parseFloat(lok[1]);
                defaultZoom = 16;
            }
        }

        var mapEdit = L.map('mapEdit').setView([defaultLat, defaultLng], defaultZoom);
        L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0','mt1','mt2','mt3']
        }).addTo(mapEdit);

        var markerEdit = null;
        if (lokasiVal) {
            markerEdit = L.marker([defaultLat, defaultLng]).addTo(mapEdit)
                .bindPopup('📍 Lokasi Sekolah Saat Ini').openPopup();
        }

        mapEdit.on('click', function(e) {
            var lat = e.latlng.lat.toFixed(7);
            var lng = e.latlng.lng.toFixed(7);
            document.getElementById('lokasi_sekolah_edit').value = lat + ',' + lng;
            if (markerEdit) { markerEdit.setLatLng(e.latlng); }
            else { markerEdit = L.marker(e.latlng).addTo(mapEdit); }
            markerEdit.bindPopup('📍 Lokasi baru dipilih').openPopup();
        });

        setTimeout(() => { mapEdit.invalidateSize(); }, 300);
    }
</script>
@endpush
