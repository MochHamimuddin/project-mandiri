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
            <h6 class="m-0 font-weight-bold text-primary">Aktivitas Program Kerja Kesehatan Terbaru</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($latestActivities->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tidak ada data ditemukan.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis Program</th>
                                <th>Deskripsi</th>
                                <th>Pengawas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestActivities as $key => $activity)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $activity->tanggal_upload->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $activity->jenis_program == \App\Models\ProgramKerjaKesehatan::MCU_TAHUNAN ? 'bg-primary' : 'bg-danger' }}">
                                        {{ $activity->jenis_program_label }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($activity->deskripsi, 50) }}</td>
                                <td>{{ $activity->pengawas->nama_lengkap ?? '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group" style="gap:10px">
                                        <a href="{{ route('program-kesehatan.show', $activity->id) }}" class="btn btn-sm btn-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if(auth()->user()->code_role === '001')
                                            <a href="{{ route('program-kesehatan.edit', $activity->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('program-kesehatan.destroy', $activity->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @if(session('success'))
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 3000);
        });
    </script>
    @endif

    <script>
        $(document).ready(function() {
            // Inisialisasi datatable
            $('.datatable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json'
                },
                columnDefs: [
                    { orderable: false, targets: [5] } // Non-aktifkan sorting untuk kolom aksi
                ],
                paging: false, // Disable DataTables pagination since we're using Laravel pagination
                info: false,
                searching: false
            });
        });
    </script>
@endsection
