@extends('layout.index')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Kegiatan {{ $jenis == 'krida' ? 'Krida Area' : 'Pengelolaan Lingkungan' }}</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('program-lingkungan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="jenis_kegiatan" value="{{ $jenis == 'krida' ? 'Krida Area Office/Workshop' : 'Pengelolaan Lingkungan Workshop' }}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_kegiatan">Tanggal Kegiatan</label>
                            <input type="date" class="form-control" id="tanggal_kegiatan" name="tanggal_kegiatan" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lokasi">Lokasi</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pelaksana">Pelaksana</label>
                    <input type="text" class="form-control" id="pelaksana" name="pelaksana" required>
                </div>

                <div class="form-group">
                    <label for="upload_foto">Upload Foto</label>
                    <input type="file" class="form-control-file" id="upload_foto" name="upload_foto">
                    <small class="form-text text-muted">Format: JPEG, PNG (Max: 2MB)</small>
                </div>

                @if($jenis == 'krida')
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Kegiatan</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                    </div>
                @else
                    <div class="form-group">
                        <label for="detail_temuan">Detail Temuan</label>
                        <textarea class="form-control" id="detail_temuan" name="detail_temuan" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tindakan_perbaikan">Tindakan Perbaikan</label>
                        <textarea class="form-control" id="tindakan_perbaikan" name="tindakan_perbaikan" rows="3" required></textarea>
                    </div>
                @endif

                <div class="d-flex justify-content-between">
                    <a href="{{ route('program-lingkungan.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
