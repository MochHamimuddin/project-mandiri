@extends('layout.index')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail {{ $development_manpower->kategori_aktivitas }}</h2>
        <div>
            <a href="{{ route('development-manpower.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Informasi Umum</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Kategori Aktivitas</dt>
                        <dd class="col-sm-8">{{ $development_manpower->kategori_aktivitas }}</dd>

                        <dt class="col-sm-4">Tanggal</dt>
                        <dd class="col-sm-8">{{ $development_manpower->tanggal_aktivitas->format('d F Y') }}</dd>

                        <dt class="col-sm-4">Pengawas</dt>
                        <dd class="col-sm-8">{{ $development_manpower->pengawas->nama_lengkap ?? '-' }}</dd>

                        @if($development_manpower->posisi)
                        <dt class="col-sm-4">Posisi</dt>
                        <dd class="col-sm-8">{{ $development_manpower->posisi }}</dd>
                        @endif
                    </dl>
                </div>
                <div class="col-md-6">
                    <dt>Deskripsi</dt>
                    <dd>{{ $development_manpower->deskripsi }}</dd>
                </div>
            </div>
        </div>
    </div>

    @if($development_manpower->kategori_aktivitas === 'Pembinaan Pelanggaran')
    <div class="card mb-4">
        <div class="card-header">Detail Pelanggaran</div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Pelaku/Korban</dt>
                <dd class="col-sm-9">{{ $development_manpower->pelakuKorban->nama_lengkap ?? '-' }}</dd>

                <dt class="col-sm-3">Saksi Langsung</dt>
                <dd class="col-sm-9">{{ $development_manpower->saksi->nama_lengkap ?? '-' }}</dd>

                <dt class="col-sm-3">Kronologi</dt>
                <dd class="col-sm-9">{{ $development_manpower->kronologi }}</dd>
            </dl>
        </div>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Dokumen Pendukung</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>Foto Aktivitas</h5>
                    @if($development_manpower->foto_aktivitas)
                        <img src="{{ asset('storage/' . $development_manpower->foto_aktivitas) }}" class="img-fluid" alt="Foto Aktivitas">
                        <a href="{{ asset('storage/' . $development_manpower->foto_aktivitas) }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-download"></i> Download
                        </a>
                    @else
                        <p class="text-muted">Tidak ada foto</p>
                    @endif
                </div>

                <div class="col-md-4 mb-3">
                    <h5>Dokumen 1</h5>
                    @if($development_manpower->dokumen_1)
                        @if(str_contains($development_manpower->dokumen_1, '.pdf'))
                        <embed src="{{ asset('storage/' . $development_manpower->dokumen_1) }}" width="100%" height="200" type="application/pdf">
                        @endif
                        <a href="{{ asset('storage/' . $development_manpower->dokumen_1) }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-download"></i> Download
                        </a>
                    @else
                        <p class="text-muted">Tidak ada dokumen</p>
                    @endif
                </div>

                @if($development_manpower->dokumen_2)
                <div class="col-md-4 mb-3">
                    <h5>Dokumen 2</h5>
                    @if(str_contains($development_manpower->dokumen_2, '.pdf'))
                    <embed src="{{ asset('storage/' . $development_manpower->dokumen_2) }}" width="100%" height="200" type="application/pdf">
                    @endif
                    <a href="{{ asset('storage/' . $development_manpower->dokumen_2) }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
