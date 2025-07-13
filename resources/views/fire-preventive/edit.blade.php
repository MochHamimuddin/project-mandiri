@extends('layout.index')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Edit Data {{ $activity->activity_type }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('fire-preventive.update', $activity->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supervisor_id">Pengawas</label>
                            <select name="supervisor_id" id="supervisor_id" class="form-control" required>
                                <option value="">Pilih Pengawas</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}"
                                    {{ old('supervisor_id', $activity->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->nama_lengkap }}
                                </option>
                                @endforeach
                            </select>
                            @error('supervisor_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="foto">Foto</label>
                    <input type="file" name="foto" id="foto" class="form-control">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah (foto saat ini akan dipertahankan)</small>
                    @if($activity->foto_path)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $activity->foto_path) }}" alt="Current Foto" style="max-height: 100px;">
                        <input type="hidden" name="current_foto" value="{{ $activity->foto_path }}">
                    </div>
                    @endif
                    @error('foto')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                @if($activity->activity_type === 'Pencucian Unit')
                <div class="form-group">
                    <label for="form_fpp">Form FPP</label>
                    <input type="file" name="form_fpp" id="form_fpp" class="form-control">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah (form saat ini akan dipertahankan)</small>
                    @if($activity->form_fpp_path)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $activity->form_fpp_path) }}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Lihat Form Saat Ini
                        </a>
                        <input type="hidden" name="current_form_fpp" value="{{ $activity->form_fpp_path }}">
                    </div>
                    @endif
                    @error('form_fpp')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                @else
                <div class="form-group">
                    <label for="inspection_location">Lokasi Inspeksi</label>
                    <input type="text" name="inspection_location" id="inspection_location"
                           class="form-control" value="{{ old('inspection_location', $activity->inspection_location) }}" required>
                    @error('inspection_location')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                @endif

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="form-control" required>{{ old('description', $activity->description) }}</textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('fire-preventive.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
