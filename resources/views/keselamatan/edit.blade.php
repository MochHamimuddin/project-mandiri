@extends('layout.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Edit Aktivitas</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('keselamatan.update', ['activity' => $activity->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="pengawas_id" class="form-label">Pengawas</label>
                            <select class="form-select @error('pengawas_id') is-invalid @enderror" id="pengawas_id" name="pengawas_id" required>
                                <option value="">Pilih Pengawas</option>
                                @foreach($pengawas as $p)
                                <option value="{{ $p->id }}" {{ old('pengawas_id', $activity->pengawas_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_lengkap }}
                                </option>
                                @endforeach
                            </select>
                            @error('pengawas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="mitra_id" class="form-label">Mitra</label>
                            <select class="form-select @error('mitra_id') is-invalid @enderror" id="mitra_id" name="mitra_id" required>
                                <option value="">Pilih Mitra</option>
                                @foreach($mitras as $mitra)
                                <option value="{{ $mitra->id }}" {{ old('mitra_id', $activity->mitra_id) == $mitra->id ? 'selected' : '' }}>
                                    {{ $mitra->nama_perusahaan }}
                                </option>
                                @endforeach
                            </select>
                            @error('mitra_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="path_foto" class="form-label">Foto</label>
                            <input type="file" class="form-control @error('path_foto') is-invalid @enderror"
                                   id="path_foto" name="path_foto" accept="image/*">
                            @error('path_foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($activity->path_foto)
                            <div class="mt-2">
                                <small>Foto saat ini:</small>
                                <a href="{{ Storage::url($activity->path_foto) }}" target="_blank" class="d-block">
                                    <img src="{{ Storage::url($activity->path_foto) }}" alt="Current Photo"
                                         style="max-height: 100px;">
                                </a>
                            </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="path_file" class="form-label">Dokumen Pendukung</label>
                            <input type="file" class="form-control @error('path_file') is-invalid @enderror"
                                   id="path_file" name="path_file" accept=".pdf,.doc,.docx">
                            @error('path_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($activity->path_file)
                            <div class="mt-2">
                                <small>Dokumen saat ini:</small>
                                <a href="{{ Storage::url($activity->path_file) }}" target="_blank" class="d-block">
                                    {{ basename($activity->path_file) }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi"
                              name="deskripsi" rows="3" required>{{ old('deskripsi', $activity->deskripsi) }}</textarea>
                    @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @can('approve', $activity)
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_approved" name="is_approved"
                           value="1" {{ old('is_approved', $activity->is_approved) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_approved">Disetujui</label>
                </div>
                @endcan

                <div class="d-flex justify-content-between">
                    <a href="{{ route('keselamatan.show', ['activity' => $activity->id]) }}">Detail</a>
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
