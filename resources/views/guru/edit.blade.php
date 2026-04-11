<form action="/guru/{{ Crypt::encrypt($guru->nip) }}/update" method="POST" id="frmEditGuru" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label class="form-label">NIP</label>
        <input type="text" value="{{ $guru->nip }}" id="nip" class="form-control" placeholder="NIP" name="nip_baru">
    </div>
    <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" id="nama_lengkap" value="{{ $guru->nama_lengkap }}" class="form-control" name="nama_lengkap" placeholder="Nama Lengkap">
    </div>
    <div class="mb-3">
        <label class="form-label">Mata Pelajaran</label>
        <input type="text" id="mata_pelajaran" value="{{ $guru->mata_pelajaran }}" class="form-control" name="mata_pelajaran" placeholder="Mata Pelajaran">
    </div>
    <div class="mb-3">
        <label class="form-label">No. HP</label>
        <input type="text" id="no_hp" value="{{ $guru->no_hp }}" class="form-control" name="no_hp" placeholder="No. HP">
    </div>
    <div class="mb-3">
        <label class="form-label">Foto</label>
        <input type="file" name="foto" class="form-control">
        <input type="hidden" name="old_foto" value="{{ $guru->foto }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Jurusan</label>
        <select name="kode_jurusan" id="kode_jurusan" class="form-select">
            <option value="">-- Pilih Jurusan --</option>
            @foreach ($jurusan as $j)
                <option {{ $guru->kode_jurusan == $j->kode_jurusan ? 'selected' : '' }} value="{{ $j->kode_jurusan }}">
                    {{ $j->nama_jurusan }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Sekolah</label>
        <select name="kode_sekolah" id="kode_sekolah" class="form-select">
            <option value="">-- Pilih Sekolah --</option>
            @foreach ($sekolah as $s)
                <option {{ $guru->kode_sekolah == $s->kode_sekolah ? 'selected' : '' }} value="{{ $s->kode_sekolah }}">
                    {{ strtoupper($s->nama_sekolah) }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <button class="btn btn-primary w-100">Simpan Perubahan</button>
    </div>
</form>
<script>
    $("#frmEditGuru").submit(function() {
        var nip = $("#frmEditGuru").find("#nip").val();
        var nama_lengkap = $("#frmEditGuru").find("#nama_lengkap").val();
        var mata_pelajaran = $("#frmEditGuru").find("#mata_pelajaran").val();
        var no_hp = $("#frmEditGuru").find("#no_hp").val();
        var kode_jurusan = $("#frmEditGuru").find("#kode_jurusan").val();

        if (nip == "") {
            Swal.fire({ title: 'Warning!', text: 'NIP Harus Diisi!', icon: 'warning', confirmButtonText: 'Ok' })
                .then(() => { $("#nip").focus(); });
            return false;
        } else if (nama_lengkap == "") {
            Swal.fire({ title: 'Warning!', text: 'Nama Harus Diisi!', icon: 'warning', confirmButtonText: 'Ok' })
                .then(() => { $("#nama_lengkap").focus(); });
            return false;
        } else if (mata_pelajaran == "") {
            Swal.fire({ title: 'Warning!', text: 'Mata Pelajaran Harus Diisi!', icon: 'warning', confirmButtonText: 'Ok' })
                .then(() => { $("#mata_pelajaran").focus(); });
            return false;
        } else if (no_hp == "") {
            Swal.fire({ title: 'Warning!', text: 'No. HP Harus Diisi!', icon: 'warning', confirmButtonText: 'Ok' })
                .then(() => { $("#no_hp").focus(); });
            return false;
        } else if (kode_jurusan == "") {
            Swal.fire({ title: 'Warning!', text: 'Jurusan Harus Dipilih!', icon: 'warning', confirmButtonText: 'Ok' })
                .then(() => { $("#kode_jurusan").focus(); });
            return false;
        }
    });
</script>
