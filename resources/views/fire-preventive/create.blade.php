@extends('layout.index')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Tambah Data {{ $type === 'Pencucian Unit' ? 'Pencucian Unit' : 'Inspeksi APAR' }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('fire-preventive.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="activity_type" value="{{ $type }}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supervisor_id">Pengawas</label>
                            <select name="supervisor_id" id="supervisor_id" class="form-control" required>
                                <option value="">Pilih Pengawas</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="foto">Foto</label>
                    <input type="file" name="foto" id="foto" class="form-control" required>
                    <small class="text-muted">Format: JPEG, PNG (Max: 2MB)</small>
                </div>

                @if($type === 'Pencucian Unit')
                <div class="form-group">
                    <label for="form_fpp">Form FPP</label>
                    <input type="file" name="form_fpp" id="form_fpp" class="form-control" required>
                    <small class="text-muted">Format: PDF, DOC, DOCX (Max: 5MB)</small>
                </div>
                @else
                <div class="form-group">
                    <label for="inspection_location">Lokasi Inspeksi</label>
                    <input type="text" name="inspection_location" id="inspection_location" class="form-control" required>
                </div>
                @endif

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="form-control" required></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('fire-preventive.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
