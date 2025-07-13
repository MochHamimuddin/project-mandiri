@extends('layout.index')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Edit Data {{ $developmentManpower->kategori_aktivitas }}</h4>
            <a href="{{ route('development-manpower.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('development-manpower.update', $developmentManpower->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body">
                <!-- Basic Information Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_aktivitas">Tanggal Aktivitas <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_aktivitas') is-invalid @enderror"
                                   id="tanggal_aktivitas" name="tanggal_aktivitas"
                                   value="{{ old('tanggal_aktivitas', $developmentManpower->tanggal_aktivitas->format('Y-m-d')) }}" required>
                            @error('tanggal_aktivitas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pengawas_id">Pengawas</label>
                            <select class="form-control @error('pengawas_id') is-invalid @enderror" id="pengawas_id" name="pengawas_id">
                                <option value="">-- Pilih Pengawas --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('pengawas_id', $developmentManpower->pengawas_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pengawas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group mb-4">
                    <label for="deskripsi">Deskripsi <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi"
                              rows="3" required>{{ old('deskripsi', $developmentManpower->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Position (if applicable) -->
                @if(in_array($developmentManpower->kategori_aktivitas, ['SKKP/POP For GL Mitra', 'Training HRCP Mitra', 'Training Additional Plant']))
                <div class="form-group mb-4">
                    <label for="posisi">Posisi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('posisi') is-invalid @enderror" id="posisi" name="posisi"
                           value="{{ old('posisi', $developmentManpower->posisi) }}" required>
                    @error('posisi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                <!-- File Upload Section -->
                <div class="card mb-4">
                    <div class="card-header">Dokumen Pendukung</div>
                    <div class="card-body">
                        <!-- Activity Photo -->
                        <div class="form-group mb-4">
                            <label for="foto_aktivitas">Foto Aktivitas</label>
                            @if($developmentManpower->foto_aktivitas)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/'.$developmentManpower->foto_aktivitas) }}" class="img-thumbnail" style="max-height: 200px;">
                                    <a href="{{ asset('storage/'.$developmentManpower->foto_aktivitas) }}" target="_blank"
                                       class="btn btn-sm btn-info ml-2">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control-file @error('foto_aktivitas') is-invalid @enderror"
                                   id="foto_aktivitas" name="foto_aktivitas">
                            <small class="form-text text-muted">Format: JPEG, PNG, JPG, GIF (Max: 2MB)</small>
                            @error('foto_aktivitas')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Document 1 -->
                        <div class="form-group mb-4">
                            <label for="dokumen_1">
                                @if($developmentManpower->kategori_aktivitas === 'Review SMKP For Mitra Kerja')
                                    MOM (Minutes of Meeting)
                                @elseif($developmentManpower->kategori_aktivitas === 'Pembinaan Pelanggaran')
                                    LPI (Laporan Pelanggaran Internal)
                                @elseif(in_array($developmentManpower->kategori_aktivitas, ['SKKP/POP For GL Mitra', 'Training HRCP Mitra']))
                                    Plan Training
                                @else
                                    Dokumen Pendukung
                                @endif
                            </label>
                            @if($developmentManpower->dokumen_1)
                                <div class="mb-3">
                                    <a href="{{ asset('storage/'.$developmentManpower->dokumen_1) }}" target="_blank"
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat Dokumen
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control-file @error('dokumen_1') is-invalid @enderror"
                                   id="dokumen_1" name="dokumen_1">
                            <small class="form-text text-muted">
                                Format: PDF, DOC, DOCX, XLS, XLSX (Max: 5MB)
                            </small>
                            @error('dokumen_1')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Document 2 (if applicable) -->
                        @if($developmentManpower->kategori_aktivitas === 'Review SMKP For Mitra Kerja')
                        <div class="form-group">
                            <label for="dokumen_2">Absensi</label>
                            @if($developmentManpower->dokumen_2)
                                <div class="mb-3">
                                    <a href="{{ asset('storage/'.$developmentManpower->dokumen_2) }}" target="_blank"
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat Dokumen
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control-file @error('dokumen_2') is-invalid @enderror"
                                   id="dokumen_2" name="dokumen_2">
                            <small class="form-text text-muted">
                                Format: PDF, XLS, XLSX (Max: 5MB)
                            </small>
                            @error('dokumen_2')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Violation Details (for Pembinaan Pelanggaran) -->
                @if($developmentManpower->kategori_aktivitas === 'Pembinaan Pelanggaran')
                <div class="card mb-4">
                    <div class="card-header">Detail Pelanggaran</div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label for="pelaku_korban_id">Pelaku/Korban <span class="text-danger">*</span></label>
                            <select class="form-control @error('pelaku_korban_id') is-invalid @enderror"
                                    id="pelaku_korban_id" name="pelaku_korban_id" required>
                                <option value="">-- Pilih Pelaku/Korban --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('pelaku_korban_id', $developmentManpower->pelaku_korban_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pelaku_korban_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="saksi_id">Saksi Langsung</label>
                            <select class="form-control @error('saksi_id') is-invalid @enderror"
                                    id="saksi_id" name="saksi_id">
                                <option value="">-- Pilih Saksi --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('saksi_id', $developmentManpower->saksi_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            @error('saksi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="kronologi">Kronologi Singkat <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('kronologi') is-invalid @enderror"
                                      id="kronologi" name="kronologi" rows="3" required>{{ old('kronologi', $developmentManpower->kronologi) }}</textarea>
                            @error('kronologi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
