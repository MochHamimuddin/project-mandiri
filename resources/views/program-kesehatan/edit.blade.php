@extends('layout.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Edit Program Kesehatan - {{ $program->jenis_program }}</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('program-kesehatan.update', $program->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi', $program->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="pengawas_id">Pengawas</label>
                    <select class="form-control @error('pengawas_id') is-invalid @enderror" id="pengawas_id" name="pengawas_id" required>
                        <option value="">Pilih Pengawas</option>
                        @foreach($pengawasList as $pengawas)
                        <option value="{{ $pengawas->id }}" {{ old('pengawas_id', $program->pengawas_id) == $pengawas->id ? 'selected' : '' }}>
                            {{ $pengawas->nama_lengkap }} ({{ $pengawas->username }})
                        </option>
                        @endforeach
                    </select>
                    @error('pengawas_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Foto Saat Ini</label>
                    <div class="mb-2">
                        <img src="{{ Storage::url($program->foto_path) }}" alt="Foto" style="max-height: 150px;" class="img-thumbnail">
                        <a href="{{ route('program-kesehatan.download', ['program' => $program->id, 'type' => 'foto']) }}" class="btn btn-sm btn-primary ml-2">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                    <label for="foto">Ganti Foto (Kosongkan jika tidak ingin mengganti)</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('foto') is-invalid @enderror" id="foto" name="foto">
                        <label class="custom-file-label" for="foto">Pilih file foto...</label>
                        <small class="form-text text-muted">Format: JPEG, PNG (Max: 2MB)</small>
                        @error('foto')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Dokumen Saat Ini</label>
                    <div class="mb-2">
                        <i class="fas fa-file-alt fa-2x text-secondary"></i>
                        <span class="ml-2">{{ basename($program->dokumen_path) }}</span>
                        <a href="{{ route('program-kesehatan.download', ['program' => $program->id, 'type' => 'dokumen']) }}" class="btn btn-sm btn-primary ml-2">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                    <label for="dokumen">Ganti Dokumen (Kosongkan jika tidak ingin mengganti)</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('dokumen') is-invalid @enderror" id="dokumen" name="dokumen">
                        <label class="custom-file-label" for="dokumen">Pilih file dokumen...</label>
                        <small class="form-text text-muted">
                            @if($program->jenis_program == 'MCU Tahunan')
                                Upload hasil D-Fit (Format: PDF, DOC, DOCX, Max: 5MB)
                            @else
                                Upload hasil kontrol karyawan (Format: PDF, DOC, DOCX, Max: 5MB)
                            @endif
                        </small>
                        @error('dokumen')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group text-right">
                    <a href="{{ route('program-kesehatan.show', $program->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
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

@push('scripts')
<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
@endpush
