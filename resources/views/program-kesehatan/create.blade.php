@extends('layout.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Tambah Program Kesehatan - {{ $type == 'MCU_TAHUNAN' ? 'MCU Tahunan' : 'Penyakit Kronis' }}</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('program-kesehatan.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="jenis_program" value="{{ $type }}">

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi') }}</textarea>
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
                        <option value="{{ $pengawas->id }}" {{ old('pengawas_id') == $pengawas->id ? 'selected' : '' }}>
                            {{ $pengawas->nama_lengkap }}
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
                    <label for="foto">Upload Foto</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('foto') is-invalid @enderror" id="foto" name="foto" required>
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
                    <label for="dokumen">Upload Dokumen</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('dokumen') is-invalid @enderror" id="dokumen" name="dokumen" required>
                        <label class="custom-file-label" for="dokumen">Pilih file dokumen...</label>
                        <small class="form-text text-muted">
                            @if($type == 'MCU_TAHUNAN')
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
                    <a href="{{ route('program-kesehatan.index') }}" class="btn btn-secondary">
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

@push('scripts')
<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
@endpush
