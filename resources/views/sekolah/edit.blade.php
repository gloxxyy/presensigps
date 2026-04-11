<form action="/sekolah/update" method="POST" id="frmEditSekolah">
    @csrf
    <input type="hidden" name="kode_sekolah" value="{{ $sekolah->kode_sekolah }}">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Kode Sekolah</label>
                <input type="text" class="form-control" value="{{ $sekolah->kode_sekolah }}" readonly disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Sekolah</label>
                <input type="text" id="nama_sekolah_edit" class="form-control" name="nama_sekolah"
                    value="{{ $sekolah->nama_sekolah }}" placeholder="Nama Sekolah">
            </div>
            <div class="mb-3">
                <label class="form-label">
                    Lokasi Koordinat
                    <small class="text-muted">(klik peta untuk ubah)</small>
                </label>
                <input type="text" id="lokasi_sekolah_edit" class="form-control" name="lokasi_sekolah"
                    value="{{ $sekolah->lokasi_sekolah }}" placeholder="lat,lng" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Radius (meter)</label>
                <input type="number" id="radius_sekolah_edit" class="form-control" name="radius_sekolah"
                    value="{{ $sekolah->radius_sekolah }}" placeholder="100">
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">📍 Klik peta untuk ganti lokasi</label>
            <div id="mapEdit" style="height: 280px; border-radius: 8px; border: 1px solid #dee2e6;"></div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <button class="btn btn-primary w-100">Simpan Perubahan</button>
        </div>
    </div>
</form>
