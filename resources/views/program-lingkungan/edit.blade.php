@extends('layout.index')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Kegiatan {{ $jenis == 'krida' ? 'Krida Area' : 'Pengelolaan Lingkungan' }}</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('program-lingkungan.update', $activity->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_kegiatan">Tanggal Kegiatan</label>
                            <input type="date" class="form-control" id="tanggal_kegiatan" name="tanggal_kegiatan"
                                   value="{{ old('tanggal_kegiatan', $activity->tanggal_kegiatan->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lokasi">Lokasi</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi"
                                   value="{{ old('lokasi', $activity->lokasi) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pelaksana">Pelaksana</label>
                    <input type="text" class="form-control" id="pelaksana" name="pelaksana"
                           value="{{ old('pelaksana', $activity->pelaksana) }}">
                </div>

                <div class="form-group">
                    <label for="upload_foto">Upload Foto</label>
                    @if($activity->upload_foto)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $activity->upload_foto) }}" alt="Foto Saat Ini" style="max-height: 100px;" class="img-thumbnail">
                            <a href="{{ asset('storage/' . $activity->upload_foto) }}" target="_blank" class="btn btn-sm btn-link">Lihat</a>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="hapus_foto" name="hapus_foto" value="1">
                                <label class="form-check-label" for="hapus_foto">Hapus foto saat ini</label>
                            </div>
                        </div>
                    @endif
                    <input type="file" class="form-control-file" id="upload_foto" name="upload_foto">
                    <small class="form-text text-muted">Format: JPEG, PNG (Max: 2MB)</small>
                </div>

                @if($jenis == 'krida')
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Kegiatan</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5">{{ old('deskripsi', $activity->deskripsi) }}</textarea>
                    </div>
                @else
                    <div class="form-group">
                        <label for="detail_temuan">Detail Temuan</label>
                        <textarea class="form-control" id="detail_temuan" name="detail_temuan" rows="5">{{ old('detail_temuan', $activity->detail_temuan) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="tindakan_perbaikan">Tindakan Perbaikan</label>
                        <textarea class="form-control" id="tindakan_perbaikan" name="tindakan_perbaikan" rows="5">{{ old('tindakan_perbaikan', $activity->tindakan_perbaikan) }}</textarea>
                    </div>
                @endif

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('program-lingkungan.show', $activity->id) }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
