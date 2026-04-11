<form action="/jurusan/{{ $jurusan->kode_jurusan }}/update" method="POST" id="frmJurusanEdit">
    @csrf
    <div class="mb-3">
        <label class="form-label">Kode Jurusan</label>
        <input type="text" value="{{ $jurusan->kode_jurusan }}" class="form-control" placeholder="Kode Jurusan" name="kode_jurusan" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Nama Jurusan</label>
        <input type="text" id="nama_jurusan" value="{{ $jurusan->nama_jurusan }}" class="form-control" name="nama_jurusan" placeholder="Nama Jurusan">
    </div>
    <div class="mb-3">
        <button class="btn btn-primary w-100">Simpan Perubahan</button>
    </div>
</form>
