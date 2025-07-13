@extends('layout.index')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>Detail Data SIB - #{{ $sib->id }}
            </h4>
            <span class="badge bg-light text-dark">
                @if($sib->perihal == 'Pengajuan SIB Baru')
                    <i class="fas fa-file-circle-plus me-1"></i> Baru
                @else
                    <i class="fas fa-file-pen me-1"></i> Perpanjangan
                @endif
            </span>
        </div>

        <div class="card-body">
            <!-- Status Badge -->
            <div class="mb-4">
                <span class="badge bg-info ms-2">
                    <i class="far fa-calendar me-1"></i>
                    {{ $sib->tanggal_mulai->format('d M Y') }} - {{ $sib->tanggal_akhir->format('d M Y') }}
                </span>
            </div>

            <!-- Main Information Sections -->
            <div class="row mb-4">
                <!-- Applicant Information -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-user-tie me-2"></i>Informasi Pemohon
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted small">Nama Lengkap</label>
                                    <p class="fs-5">{{ $sib->nama_lengkap }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted small">NRP</label>
                                    <p class="fs-5">{{ $sib->nrp }}</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small">Departemen</label>
                                <p class="fs-5">{{ \App\Models\DataSib::DEPARTEMEN[$sib->departemen] ?? $sib->departemen }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small">Diajukan Pada</label>
                                <p class="fs-5">{{ $sib->created_at->format('d F Y H:i') }}</p>
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
                                <p class="fs-5">{{ \App\Models\DataSib::JENIS_PEKERJAAN[$sib->jenis_pekerjaan] ?? $sib->jenis_pekerjaan }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small">Lokasi</label>
                                <p class="fs-5">{{ \App\Models\DataSib::LOKASI[$sib->lokasi] ?? $sib->lokasi }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small">Deskripsi Pekerjaan</label>
                                <p class="fs-5">{{ $sib->deskripsi_pekerjaan ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline and Submission Info -->
            <div class="row mb-4">
                <div class="col-md-6">
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
                                        <p class="text-primary">{{ $sib->tanggal_mulai->format('d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-point"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Selesai Pekerjaan</h6>
                                        <p class="text-primary">{{ $sib->tanggal_akhir->format('d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-point"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Durasi</h6>
                                        <p>{{ $sib->tanggal_mulai->diffInDays($sib->tanggal_akhir) + 1 }} Hari</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>Ketentuan Pengajuan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-{{ $sib->pengajuan_baru_h7 == 'Ya' ? 'success' : 'secondary' }}">
                                <i class="fas {{ $sib->pengajuan_baru_h7 == 'Ya' ? 'fa-check-circle' : 'fa-times-circle' }} me-2"></i>
                                Pengajuan Baru (H-7): <strong>{{ $sib->pengajuan_baru_h7 }}</strong>
                            </div>
                            <div class="alert alert-{{ $sib->perpanjangan_h2 == 'Ya' ? 'success' : 'secondary' }}">
                                <i class="fas {{ $sib->perpanjangan_h2 == 'Ya' ? 'fa-check-circle' : 'fa-times-circle' }} me-2"></i>
                                Perpanjangan (H-2): <strong>{{ $sib->perpanjangan_h2 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-folder-open me-2"></i>Dokumen Pendukung
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
                                        <i class="fas fa-file-circle-check me-2"></i>Dokumen Wajib
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @foreach([
                                        'Work Permit' => $sib->work_permit_path,
                                        'Emergency Preparedness' => $sib->emergency_preparedness_path,
                                        'Emergency Escape Plan' => $sib->emergency_escape_plan_path,
                                        'History Training' => $sib->history_training_path,
                                        'Form FPP' => $sib->form_fpp_path,
                                        'Form Observasi Berjenjang' => $sib->form_observasi_berjenjang_path
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

                        <!-- Additional Documents -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-circle-plus me-2"></i>Dokumen Tambahan
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- JSA Files -->
                                    <div class="document-item mb-3">
                                        <label class="form-label fw-bold">JSA (Job Safety Analysis)</label>
                                        @if($sib->jsa_path1 || $sib->jsa_path2 || $sib->jsa_path3 || $sib->jsa_path4 || $sib->jsa_path5)
                                            <div class="d-flex flex-wrap gap-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($sib->{"jsa_path$i"})
                                                        <a href="{{ Storage::url($sib->{"jsa_path$i"}) }}" target="_blank"
                                                           class="btn btn-sm btn-outline-primary">
                                                            JSA {{ $i }}
                                                        </a>
                                                    @endif
                                                @endfor
                                            </div>
                                        @else
                                            <span class="badge bg-light text-dark">Tidak tersedia</span>
                                        @endif
                                    </div>

                                    <!-- IBPR Files -->
                                    <div class="document-item mb-3">
                                        <label class="form-label fw-bold">IBPR</label>
                                        @if($sib->ibpr_path1 || $sib->ibpr_path2 || $sib->ibpr_path3 || $sib->ibpr_path4 || $sib->ibpr_path5)
                                            <div class="d-flex flex-wrap gap-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($sib->{"ibpr_path$i"})
                                                        <a href="{{ Storage::url($sib->{"ibpr_path$i"}) }}" target="_blank"
                                                           class="btn btn-sm btn-outline-primary">
                                                            IBPR {{ $i }}
                                                        </a>
                                                    @endif
                                                @endfor
                                            </div>
                                        @else
                                            <span class="badge bg-light text-dark">Tidak tersedia</span>
                                        @endif
                                    </div>

                                    <!-- Other Optional Documents -->
                                    @foreach([
                                        'Staggling Plan' => $sib->staggling_plan_path,
                                        'Kajian Geotek' => $sib->kajian_geotek_path,
                                        'Form P2H Unit Lifting' => $sib->form_p2h_unit_lifting_path,
                                        'Form Inspeksi Tools' => $sib->form_inspeksi_tools_path
                                    ] as $label => $path)
                                        <div class="document-item mb-3">
                                            <label class="form-label fw-bold">{{ $label }}</label>
                                            @if($path)
                                                <a href="{{ Storage::url($path) }}" target="_blank"
                                                   class="btn btn-sm btn-outline-primary">
                                                    Lihat Dokumen
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
                <a href="{{ route('data-sib.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                </a>

                @if(auth()->user()->code_role == '001')
                <div>
                    <form action="{{ route('data-sib.destroy', $sib->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus SIB ini?')">
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
