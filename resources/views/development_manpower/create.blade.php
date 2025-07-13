@extends('layout.index')

@section('content')
<div class="container">
    <h2>Tambah Data {{ $kategori }}</h2>

    <form action="{{ route('development-manpower.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="kategori_aktivitas" value="{{ $kategori }}">

        <div class="card mb-3">
            <div class="card-header">Informasi Umum</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="tanggal_aktivitas">Tanggal Aktivitas</label>
                    <input type="date" class="form-control" id="tanggal_aktivitas" name="tanggal_aktivitas" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                </div>

                <div class="form-group">
    <label for="pengawas_id">Pengawas</label>
    <select class="form-control" id="pengawas_id" name="pengawas_id"
        @if(auth()->user()->code_role === '002') disabled @endif>
        <option value="">-- Pilih Pengawas --</option>

        @if(auth()->user()->code_role === '001')
            {{-- Admin can see all supervisors --}}
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('pengawas_id', $selectedPengawasId ?? '') == $user->id ? 'selected' : '' }}>
                    {{ $user->nama_lengkap }}
                </option>
            @endforeach
        @elseif(auth()->user()->code_role === '002')
            {{-- Regular user can only see themselves --}}
            <option value="{{ auth()->user()->id }}" selected>
                {{ auth()->user()->nama_lengkap }}
            </option>
            {{-- Hidden input to ensure value gets submitted --}}
            <input type="hidden" name="pengawas_id" value="{{ auth()->user()->id }}">
        @endif
    </select>
</div>

                @if(in_array($kategori, ['SKKP/POP For GL Mitra', 'Training HRCP Mitra', 'Training Additional Plant']))
                <div class="form-group">
                    <label for="posisi">Posisi</label>
                    <input type="text" class="form-control" id="posisi" name="posisi" required>
                </div>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Upload Dokumen</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="foto_aktivitas">Foto Aktivitas</label>
                    <input type="file" class="form-control-file" id="foto_aktivitas" name="foto_aktivitas" required>
                </div>

                @if($kategori === 'Review SMKP For Mitra Kerja')
                <div class="form-group">
                    <label for="dokumen_1">MOM (Minutes of Meeting)</label>
                    <input type="file" class="form-control-file" id="dokumen_1" name="dokumen_1" required>
                </div>
                <div class="form-group">
                    <label for="dokumen_2">Absensi</label>
                    <input type="file" class="form-control-file" id="dokumen_2" name="dokumen_2" required>
                </div>
                @elseif($kategori === 'Pembinaan Pelanggaran')
                <div class="form-group">
                    <label for="dokumen_1">LPI (Laporan Pelanggaran Internal)</label>
                    <input type="file" class="form-control-file" id="dokumen_1" name="dokumen_1" required>
                </div>
                @else
                <div class="form-group">
                    <label for="dokumen_1">
                        @if(in_array($kategori, ['SKKP/POP For GL Mitra', 'Training HRCP Mitra']))
                            Plan Training
                        @else
                            Absensi
                        @endif
                    </label>
                    <input type="file" class="form-control-file" id="dokumen_1" name="dokumen_1" required>
                </div>
                @endif
            </div>
        </div>

        @if($kategori === 'Pembinaan Pelanggaran')
        <div class="card mb-3">
            <div class="card-header">Detail Pelanggaran</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="pelaku_korban_id">Pelaku/Korban</label>
                    <select class="form-control" id="pelaku_korban_id" name="pelaku_korban_id" required>
                        <option value="">-- Pilih Pelaku/Korban --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="saksi_id">Saksi Langsung</label>
                    <select class="form-control" id="saksi_id" name="saksi_id">
                        <option value="">-- Pilih Saksi --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="kronologi">Kronologi Singkat</label>
                    <textarea class="form-control" id="kronologi" name="kronologi" rows="3" required></textarea>
                </div>
            </div>
        </div>
        @endif

        <div class="d-flex justify-content-between">
            <a href="{{ route('development-manpower.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection
