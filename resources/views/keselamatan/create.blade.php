@extends('layout.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>{{ $title }}</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
           <form action="{{ route('keselamatan.store', ['type' => $type]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
    <label for="pengawas_id" class="form-label">Pengawas</label>
    <select class="form-select @error('pengawas_id') is-invalid @enderror" id="pengawas_id" name="pengawas_id"
        required
        @if(auth()->user()->code_role === '002') disabled @endif>

        <option value="">Pilih Pengawas</option>

        @if(auth()->user()->code_role === '001')
            {{-- Admin melihat semua pengawas --}}
            @foreach($pengawas as $p)
                <option value="{{ $p->id }}" {{ old('pengawas_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->nama_lengkap }} ({{ $p->username }})
                </option>
            @endforeach
        @elseif(auth()->user()->code_role === '002')
            {{-- User biasa hanya melihat dirinya sendiri --}}
            <option value="{{ auth()->user()->id }}" selected>
                {{ auth()->user()->nama_lengkap }} ({{ auth()->user()->username }})
            </option>
            {{-- Input hidden untuk memastikan nilai terkirim --}}
            <input type="hidden" name="pengawas_id" value="{{ auth()->user()->id }}">
        @endif
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
                                <option value="{{ $mitra->id }}" {{ old('mitra_id') == $mitra->id ? 'selected' : '' }}>
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
                        </div>

                        <div class="mb-3">
                            <label for="path_file" class="form-label">Dokumen Pendukung</label>
                            <input type="file" class="form-control @error('path_file') is-invalid @enderror"
                                   id="path_file" name="path_file" accept=".pdf,.doc,.docx">
                            @error('path_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi"
                              name="deskripsi" rows="3" required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('keselamatan.type.index', ['type' => $type]) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
