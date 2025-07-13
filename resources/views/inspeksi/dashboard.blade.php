@extends('layout.index')

@section('content')
<div class="container">
    <h2 class="mb-4">Traffic Management Preventive Program</h2>

    @if(isset($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    <div class="row">
        <!-- Card 1: Inspeksi & Observasi Tematik -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Inspeksi & Observasi Tematik</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>Komisioning</h6>
                                    <h3 class="text-primary">{{ $totalKomisioning }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>Perawatan</h6>
                                    <h3 class="text-success">{{ $totalPerawatan }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4">Aktivitas Terakhir</h5>
                    <div class="list-group">
                        @forelse($inspeksiTerbaru as $inspeksi)
                        <a href="{{ route('inspeksi.show', $inspeksi->id) }}"
                           class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <strong>{{ ucfirst($inspeksi->jenis_inspeksi) }}</strong>
                                    <small class="d-block text-muted">{{ Str::limit($inspeksi->deskripsi, 40) }}</small>
                                </span>
                                <span class="text-end">
                                    <small>{{ $inspeksi->tanggal_inspeksi->format('d M Y') }}</small><br>
                                    <small class="text-muted">{{ $inspeksi->pengawas->nama ?? '-' }}</small>
                                </span>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item text-muted">Tidak ada data</div>
                        @endforelse
                    </div>

                    @if(count($jadwalPerawatanMendatang) > 0)
                    <h5 class="mt-4">Jadwal Perawatan Mendatang</h5>
                    <ul class="list-group">
                        @foreach($jadwalPerawatanMendatang as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $item->pelaksana_perawatan }}
                            <span class="badge bg-primary rounded-pill">
                                {{ $item->jadwal_perawatan->format('d M') }}
                            </span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 2: Evaluasi Kecepatan -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Evaluasi Kecepatan Unit Wheel</h4>
                </div>
                <div class="card-body">
                    <div class="card bg-light mb-3">
                        <div class="card-body text-center">
                            <h6>Total Evaluasi</h6>
                            <h3 class="text-warning">{{ $totalEvaluasi }}</h3>
                        </div>
                    </div>

                    <h5 class="mt-4">Evaluasi Terakhir</h5>
                    <div class="list-group">
                        @forelse($evaluasiTerbaru as $evaluasi)
                        <a href="{{ route('inspeksi.show', $evaluasi->id) }}"
                           class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <strong>Kecepatan: {{ $evaluasi->hasil_observasi_kecepatan }} {{ $evaluasi->satuan_kecepatan }}</strong>
                                    <small class="d-block text-muted">{{ Str::limit($evaluasi->deskripsi, 40) }}</small>
                                </span>
                                <span class="text-end">
                                    <small>{{ $evaluasi->tanggal_inspeksi->format('d M Y') }}</small><br>
                                    <small class="text-muted">{{ $evaluasi->pengawas->nama ?? '-' }}</small>
                                </span>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item text-muted">Tidak ada data evaluasi</div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('inspeksi.create') }}?jenis=evaluasi_kecepatan"
                           class="btn btn-warning">
                            <i class="bi bi-plus-circle"></i> Tambah Evaluasi Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
