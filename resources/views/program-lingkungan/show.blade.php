@extends('layout.index')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Kegiatan Lingkungan Hidup</h2>
        <div>
            <a href="{{ route('program-lingkungan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">{{ $programLingkunganHidup->jenis_kegiatan }}</h4>
        </div>

        <div class="card-body">
            <div class="row">
                <!-- Kolom Informasi Utama -->
                <div class="col-md-8">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tanggal Kegiatan:</div>
                        <div class="col-md-8">
                            {{ $programLingkunganHidup->tanggal_kegiatan->translatedFormat('l, d F Y') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Lokasi:</div>
                        <div class="col-md-8">
                            {{ $programLingkunganHidup->lokasi }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Pelaksana:</div>
                        <div class="col-md-8">
                            {{ $programLingkunganHidup->pelaksana }}
                        </div>
                    </div>

                    <!-- Detail Temuan -->
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Detail Temuan:</div>
                        <div class="col-md-8">
                            @if($programLingkunganHidup->detail_temuan)
                                <div class="card bg-light p-3">
                                    {!! nl2br(e($programLingkunganHidup->detail_temuan)) !!}
                                </div>
                            @else
                                <span class="text-muted fst-italic">Tidak ada data temuan</span>
                            @endif
                        </div>
                    </div>

                    <!-- Tindakan Perbaikan -->
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tindakan Perbaikan:</div>
                        <div class="col-md-8">
                            @if($programLingkunganHidup->tindakan_perbaikan)
                                <div class="card bg-light p-3">
                                    {!! nl2br(e($programLingkunganHidup->tindakan_perbaikan)) !!}
                                </div>
                            @else
                                <span class="text-muted fst-italic">Belum ada tindakan perbaikan</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Kolom Foto -->
                <div class="col-md-4">
                    @if($programLingkunganHidup->upload_foto)
                        <div class="text-center mb-3">
                            <img src="{{ asset('storage/' . $programLingkunganHidup->upload_foto) }}"
                                 alt="Dokumentasi Kegiatan"
                                 class="img-fluid rounded border"
                                 style="max-height: 300px; object-fit: contain;">
                        </div>
                        <div class="text-center">
                            <a href="{{ asset('storage/' . $programLingkunganHidup->upload_foto) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                               <i class="fas fa-expand"></i> Lihat Full Size
                            </a>
                        </div>
                    @else
                        <div class="alert alert-secondary text-center">
                            <i class="fas fa-image me-2"></i>Tidak ada dokumentasi foto
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Terakhir diupdate: {{ $programLingkunganHidup->updated_at->diffForHumans() }}
                </small>
                <div>
    @if(auth()->user()->code_role === '001')
        <form action="{{ route('program-lingkungan.destroy', $programLingkunganHidup->id) }}"
              method="POST"
              class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                    <i class="fas fa-trash"></i> Hapus
            </button>
        </form>
    @endif
</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card.bg-light {
        background-color: #f8f9fa !important;
        border-left: 4px solid #0d6efd;
    }
    .fw-bold {
        color: #495057;
    }
</style>
@endsection
