@extends('layout.index')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4 animate__animated animate__fadeIn">Traffic Management Preventive Program</h2>

    @if(isset($error))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Card 1: Inspeksi & Observasi Tematik -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-primary text-white rounded-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Inspeksi & Observasi Tematik</h4>
                        <button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#inspeksiCollapse">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body collapse show" id="inspeksiCollapse">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="card bg-light h-100 hover-scale">
                                <div class="card-body text-center py-4">
                                    <div class="icon-lg bg-primary bg-opacity-10 text-primary rounded-circle mb-3 mx-auto">
                                        <i class="bi bi-clipboard-check"></i>
                                    </div>
                                    <h6 class="text-muted">Komisioning</h6>
                                    <h3 class="text-primary counter" data-target="{{ $totalKomisioning }}">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light h-100 hover-scale">
                                <div class="card-body text-center py-4">
                                    <div class="icon-lg bg-success bg-opacity-10 text-success rounded-circle mb-3 mx-auto">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                    <h6 class="text-muted">Perawatan</h6>
                                    <h3 class="text-success counter" data-target="{{ $totalPerawatan }}">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Aktivitas Terakhir</h5>
                        <a href="{{ route('inspeksi.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="list-group list-group-flush scrollable-list" style="max-height: 300px;">
                        @forelse($inspeksiTerbaru as $inspeksi)
                        <a href="{{ route('inspeksi.show', $inspeksi->id) }}"
                           class="list-group-item list-group-item-action border-0 rounded-3 mb-2 hover-shadow">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="icon-sm me-3" style="background-color: {{ $inspeksi->jenis_inspeksi == 'komisioning' ? '#0d6efd' : '#198754' }};">
                                        <i class="bi {{ $inspeksi->jenis_inspeksi == 'komisioning' ? 'bi-clipboard-check' : 'bi-tools' }} text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ ucfirst($inspeksi->jenis_inspeksi) }}</h6>
                                        <small class="text-muted">{{ Str::limit($inspeksi->deskripsi, 40) }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <small class="d-block">{{ $inspeksi->tanggal_inspeksi->format('d M Y') }}</small>
                                    <small class="text-muted">{{ $inspeksi->pengawas->nama ?? '-' }}</small>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item text-muted text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                            <p class="mt-2 mb-0">Tidak ada data</p>
                        </div>
                        @endforelse
                    </div>

                    @if(count($jadwalPerawatanMendatang) > 0)
                    <div class="mt-4">
                        <h5>Jadwal Perawatan Mendatang</h5>
                        <div class="timeline">
                            @foreach($jadwalPerawatanMendatang as $item)
                            <div class="timeline-item">
                                <div class="timeline-badge bg-success"></div>
                                <div class="timeline-content p-3 rounded-3 hover-shadow">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-0">{{ $item->pelaksana_perawatan }}</h6>
                                        <span class="badge bg-primary">{{ $item->jadwal_perawatan->format('d M') }}</span>
                                    </div>
                                    <small class="text-muted">{{ $item->jadwal_perawatan->diffForHumans() }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('inspeksi.create') }}" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Inspeksi Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Card 2: Evaluasi Kecepatan -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-warning text-dark rounded-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Evaluasi Kecepatan Unit Wheel</h4>
                        <button class="btn btn-sm btn-dark" data-bs-toggle="collapse" data-bs-target="#evaluasiCollapse">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body collapse show" id="evaluasiCollapse">
                    <div class="card bg-light mb-4 hover-scale">
                        <div class="card-body text-center py-4">
                            <div class="icon-lg bg-warning bg-opacity-10 text-warning rounded-circle mb-3 mx-auto">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                            <h6 class="text-muted">Total Evaluasi</h6>
                            <h3 class="text-warning counter" data-target="{{ $totalEvaluasi }}">0</h3>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Evaluasi Terakhir</h5>
                        <a href="{{ route('inspeksi.index') }}?jenis=evaluasi_kecepatan" class="btn btn-sm btn-outline-warning">Lihat Semua</a>
                    </div>
                    <div class="list-group list-group-flush scrollable-list" style="max-height: 300px;">
                        @forelse($evaluasiTerbaru as $evaluasi)
                        <a href="{{ route('inspeksi.show', $evaluasi->id) }}"
                           class="list-group-item list-group-item-action border-0 rounded-3 mb-2 hover-shadow">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="icon-sm me-3 bg-warning">
                                        <i class="bi bi-speedometer2 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Kecepatan: {{ $evaluasi->hasil_observasi_kecepatan }} {{ $evaluasi->satuan_kecepatan }}</h6>
                                        <small class="text-muted">{{ Str::limit($evaluasi->deskripsi, 40) }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <small class="d-block">{{ $evaluasi->tanggal_inspeksi->format('d M Y') }}</small>
                                    <small class="text-muted">{{ $evaluasi->pengawas->nama ?? '-' }}</small>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item text-muted text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                            <p class="mt-2 mb-0">Tidak ada data evaluasi</p>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('inspeksi.create') }}?jenis=evaluasi_kecepatan"
                           class="btn btn-warning w-100">
                            <i class="bi bi-plus-circle me-2"></i> Tambah Evaluasi Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this to your CSS section or file -->
<style>
    .hover-scale {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-scale:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .hover-shadow:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .icon-lg {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .icon-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }
    .scrollable-list {
        overflow-y: auto;
        scrollbar-width: thin;
    }
    .timeline {
        position: relative;
        padding-left: 40px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-badge {
        position: absolute;
        left: -20px;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }
    .timeline-content {
        background-color: #f8f9fa;
    }
    .animate__animated {
        animation-duration: 1s;
    }
</style>

<!-- Add this JavaScript for interactive elements -->
<script>
    // Counter animation
    document.addEventListener('DOMContentLoaded', function() {
        // Animate counters
        const counters = document.querySelectorAll('.counter');
        const speed = 200;
        
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const increment = target / speed;
            
            if(count < target) {
                const updateCount = () => {
                    const newCount = Math.ceil(count + increment);
                    if(newCount < target) {
                        counter.innerText = newCount;
                        setTimeout(updateCount, 1);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            }
        });
        
        // Add tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection