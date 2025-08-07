@extends('layout.index')

<!-- Debugging -->
@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>Detail Data listDokumen - #{{ $listDokumen->id }}
            </h4>
        </div>

        <div class="card-body">
            <!-- Status Badge -->
            <div class="mb-4">
                <span class="badge bg-info ms-2">
                    <i class="far fa-calendar me-1"></i>
                    {{ $listDokumen->start_date->format('d M Y') }} - {{ $listDokumen->end_date->format('d M Y') }}
                </span>
            </div>

            <!-- Main Information Sections -->
            <div class="row mb-4">
                <!-- Applicant Information -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-user-tie me-2"></i>Informasi User
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted small">Nama Lengkap</label>
                                    <p class="fs-5">{{ App\Models\User::find($listDokumen->id_users)->nama_lengkap ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Information -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-briefcase me-2"></i>Informasi Pekerjaan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small">Jenis Pekerjaan</label>
                                <p class="fs-5">{{ \App\Models\ListDokumen::KATEGORI_AKTIVITAS[$listDokumen->jenis_pekerjaan] ?? $listDokumen->jenis_pekerjaan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline and Submission Info -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-day me-2"></i>Periode Pekerjaan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-point"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Mulai Pekerjaan</h6>
                                        <p class="text-primary">{{ $listDokumen->start_date->format('d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-point"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Selesai Pekerjaan</h6>
                                        <p class="text-primary">{{ $listDokumen->end_date->format('d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-point"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Durasi</h6>
                                        <p>{{ $listDokumen->start_date->diffInDays($listDokumen->end_date) + 1 }} Hari</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-folder-open me-2"></i>List dokumen
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Klik pada tombol dokumen untuk melihat atau mengunduh file.
                    </div>

                    <div class="row">
                        <!-- Required Documents -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-circle-check me-2"></i>Dokumen
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @foreach([
                                        'Foto Kelengkapan APD' => $listDokumen->path_apd,
                                        'Pelaksanaan P5M dan Sosialisasi JSA' => $listDokumen->path_p5m_jsa,
                                        'Pelaksanaan P2H pada unit' => $listDokumen->path_p2h,
                                        'Pelaksanaan Inspeksi FPP' => $listDokumen->path_fpp,
                                        'Form FPP' => $listDokumen->path_form_fpp,
                                        'Foto kegiatan berlangsung' => $listDokumen->path_kegiatan,
                                        'Absensi P5M' => $listDokumen->path_absensi_p5m,
                                        'Form Inspeksi & Observasi berjenjang' => $listDokumen->path_inspeksi
                                    ] as $label => $path)
                                        <div class="document-item mb-3">
                                            <label class="form-label fw-bold">{{ $label }}</label>
                                            @if($path)
                                                <a href="{{ Storage::url($path) }}" target="_blank"
                                                   class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                                    <i class="far fa-file-pdf me-2"></i>
                                                    Lihat Dokumen
                                                    <span class="badge bg-secondary ms-2">
                                                        {{ pathinfo($path, PATHINFO_EXTENSION) }}
                                                    </span>
                                                </a>
                                            @else
                                                <span class="badge bg-light text-dark">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('list-dokumen.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                </a>

                @if(auth()->user()->code_role == '001')
                <div>
                    <form action="{{ route('list-dokumen.destroy', $listDokumen->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus listDokumen ini?')">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 1.5rem;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .timeline-point {
        position: absolute;
        left: -1.5rem;
        top: 0.25rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background-color: #0d6efd;
    }
    .timeline-content {
        padding-left: 1rem;
    }
    .document-item {
        padding: 0.75rem;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
    }
    .badge {
        font-weight: 500;
    }
</style>
@endpush
@endsection
