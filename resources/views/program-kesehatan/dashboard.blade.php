@extends('layout.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Dashboard Program Kerja Kesehatan</h2>
        </div>
    </div>

    <!-- Cards Section -->
    <div class="row">
        <!-- MCU Tahunan Card -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                MCU Tahunan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mcuCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('program-kesehatan.index', ['type' => 'MCU_TAHUNAN']) }}" class="btn btn-sm btn-primary">Lihat Data</a>
                        <a href="{{ route('program-kesehatan.create', ['type' => 'MCU_TAHUNAN']) }}" class="btn btn-sm btn-success">Tambah Baru</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Penyakit Kronis Card -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Penyakit Kronis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kronisCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heartbeat fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('program-kesehatan.index', ['type' => 'PENYAKIT_KRONIS']) }}" class="btn btn-sm btn-danger">Lihat Data</a>
                        <a href="{{ route('program-kesehatan.create', ['type' => 'PENYAKIT_KRONIS']) }}" class="btn btn-sm btn-success">Tambah Baru</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Program</th>
                            <th>Deskripsi</th>
                            <th>Pengawas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestActivities as $activity)
                        <tr>
                            <td>{{ $activity->tanggal_upload->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge {{ $activity->jenis_program == \App\Models\ProgramKerjaKesehatan::MCU_TAHUNAN ? 'bg-primary-subtle text-primary-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                    {{ $activity->jenis_program_label }}
                                </span>
                            </td>
                            <td>{{ Str::limit($activity->deskripsi, 50) }}</td>
                            <td>{{ $activity->pengawas->nama_lengkap ?? '-' }}</td>
                            <td>
                                <a href="{{ route('program-kesehatan.show', $activity->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
