@extends('layout.index')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Edit Inspeksi Kendaraan</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('inspeksi.update', $inspeksi->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="jenis_inspeksi" class="form-label">Jenis Inspeksi</label>
                    <select class="form-select" id="jenis_inspeksi" name="jenis_inspeksi" required>
                        <option value="komisioning" {{ $inspeksi->jenis_inspeksi == 'komisioning' ? 'selected' : '' }}>Komisioning</option>
                        <option value="perawatan" {{ $inspeksi->jenis_inspeksi == 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                        <option value="evaluasi_kecepatan" {{ $inspeksi->jenis_inspeksi == 'evaluasi_kecepatan' ? 'selected' : '' }}>Evaluasi Kecepatan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="tanggal_inspeksi" class="form-label">Tanggal Inspeksi</label>
                    <input type="date" class="form-control" id="tanggal_inspeksi" name="tanggal_inspeksi"
                           value="{{ $inspeksi->tanggal_inspeksi->format('Y-m-d') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ $inspeksi->deskripsi }}</textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="pengawas_id" class="form-label">Pengawas</label>
                    <select class="form-select" id="pengawas_id" name="pengawas_id">
                        <option value="">Pilih Pengawas</option>
                        @foreach($users as $p)
                        <option value="{{ $p->id }}" {{ $inspeksi->pengawas_id == $p->id ? 'selected' : '' }}>{{ $p->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="mitra_id" class="form-label">Mitra</label>
                    <select class="form-select" id="mitra_id" name="mitra_id">
                        <option value="">Pilih Mitra</option>
                        @foreach($mitras as $m)
                        <option value="{{ $m->id }}" {{ $inspeksi->mitra_id == $m->id ? 'selected' : '' }}>{{ $m->nama_perusahaan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Field untuk Komisioning -->
            <div id="komisioning_fields" class="mb-3" style="{{ $inspeksi->jenis_inspeksi != 'komisioning' ? 'display: none;' : '' }}">
                <label for="jenis_komisioning" class="form-label">Jenis Komisioning</label>
                <input type="text" class="form-control" id="jenis_komisioning" name="jenis_komisioning"
                       value="{{ $inspeksi->jenis_komisioning }}">
            </div>

            <!-- Field untuk Perawatan -->
            <div id="perawatan_fields" style="{{ $inspeksi->jenis_inspeksi != 'perawatan' ? 'display: none;' : '' }}">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="jadwal_perawatan" class="form-label">Jadwal Perawatan</label>
                        <input type="date" class="form-control" id="jadwal_perawatan" name="jadwal_perawatan"
                               value="{{ $inspeksi->jadwal_perawatan ? $inspeksi->jadwal_perawatan->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-6">
                        <label for="pelaksana_perawatan" class="form-label">Pelaksana Perawatan</label>
                        <input type="text" class="form-control" id="pelaksana_perawatan" name="pelaksana_perawatan"
                               value="{{ $inspeksi->pelaksana_perawatan }}">
                    </div>
                </div>
            </div>

            <!-- Field untuk Evaluasi Kecepatan -->
            <div id="evaluasi_fields" style="{{ $inspeksi->jenis_inspeksi != 'evaluasi_kecepatan' ? 'display: none;' : '' }}">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="hasil_observasi_kecepatan" class="form-label">Hasil Observasi Kecepatan</label>
                        <input type="text" class="form-control" id="hasil_observasi_kecepatan" name="hasil_observasi_kecepatan"
                               value="{{ $inspeksi->hasil_observasi_kecepatan }}">
                    </div>
                    <div class="col-md-6">
                        <label for="satuan_kecepatan" class="form-label">Satuan Kecepatan</label>
                        <input type="text" class="form-control" id="satuan_kecepatan" name="satuan_kecepatan"
                               value="{{ $inspeksi->satuan_kecepatan }}" placeholder="km/jam">
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="foto" class="form-label">Foto Inspeksi</label>
                    <input type="file" class="form-control" id="foto" name="foto">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto</small>
                    <div class="mt-2">
                        <img src="{{ $inspeksi->foto_url }}" alt="Foto Saat Ini" style="max-height: 100px;">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="dokumen" class="form-label">Dokumen Pendukung (PDF)</label>
                    <input type="file" class="form-control" id="dokumen" name="dokumen">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah dokumen</small>
                    @if($inspeksi->path_dokumen)
                    <div class="mt-2">
                        <a href="{{ $inspeksi->dokumen_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            Lihat Dokumen Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('inspeksi.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('jenis_inspeksi').addEventListener('change', function() {
        const jenis = this.value;

        // Sembunyikan semua field tambahan
        document.getElementById('komisioning_fields').style.display = 'none';
        document.getElementById('perawatan_fields').style.display = 'none';
        document.getElementById('evaluasi_fields').style.display = 'none';

        // Tampilkan field yang sesuai
        if (jenis === 'komisioning') {
            document.getElementById('komisioning_fields').style.display = 'block';
        } else if (jenis === 'perawatan') {
            document.getElementById('perawatan_fields').style.display = 'block';
        } else if (jenis === 'evaluasi_kecepatan') {
            document.getElementById('evaluasi_fields').style.display = 'block';
        }
    });
</script>
@endsection
