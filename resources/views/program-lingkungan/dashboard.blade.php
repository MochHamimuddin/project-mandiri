@extends('layout.index')

@section('content')
<div class="container-fluid p-0">
    <!-- Background Image Section -->
    <div class="position-relative">
        <div class="bg-image" style="
            background-image: url('https://www.kppmining.com/api/assets/gallery-1670437567996-4.jpeg?folder=gallery');
            height: 400px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(0.7);
        "></div>
        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
            <h1 class="text-white display-4">Dashboard Program Lingkungan Hidup</h1>
        </div>
    </div>

    <div class="container py-4">
        <div class="row mb-4">
            <!-- Card Krida Area -->
            <div class="col-md-6">
                <div class="card text-white bg-success mb-3 shadow-lg">
                    <div class="card-body">
                        <h5 class="card-title">Krida Area Office/Workshop</h5>
                        <h1 class="display-4 text-center">{{ $kridaCount }}</h1>
                        <p class="card-text">Total kegiatan penghijauan dan kerja bakti</p>
                        <a href="{{ route('program-lingkungan.create', 'krida') }}" class="btn btn-light">Tambah Baru</a>
                        <a href="{{ route('program-lingkungan.index', 'krida') }}" class="btn btn-light">Lihat Data</a>
                    </div>
                </div>
            </div>

            <!-- Card Pengelolaan Lingkungan -->
            <div class="col-md-6">
                <div class="card text-white bg-primary mb-3 shadow-lg">
                    <div class="card-body">
                        <h5 class="card-title">Pengelolaan Lingkungan Workshop</h5>
                        <h1 class="display-4 text-center">{{ $pengelolaanCount }}</h1>
                        <p class="card-text">Total kegiatan pengelolaan lingkungan</p>
                        <a href="{{ route('program-lingkungan.create', 'pengelolaan') }}" class="btn btn-light">Tambah Baru</a>
                        <a href="{{ route('program-lingkungan.index', 'pengelolaan') }}" class="btn btn-light">Lihat Data</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="card shadow">
            <div class="card-header ">
                <h5 class="mb-0">Aktivitas Program Lingkungan Hidup</h5>
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

                @if($errors->any()))
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

                @if($latestActivities->isEmpty()))
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
                                    <th>Jenis Kegiatan</th>
                                    <th>Lokasi</th>
                                    <th>Pelaksana</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestActivities as $key => $activity)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $activity->tanggal_kegiatan->format('d/m/Y') }}</td>
                                    <td>{{ $activity->jenis_kegiatan }}</td>
                                    <td>{{ $activity->lokasi }}</td>
                                    <td>{{ $activity->pelaksana }}</td>
                                    <td>
                                        <div class="btn-group" role="group" style="gap:10px">
                                            <a href="{{ route('program-lingkungan.show', $activity->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if(auth()->user()->code_role === '001')
                                                <a href="{{ route('program-lingkungan.edit', $activity->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('program-lingkungan.destroy', $activity->id) }}" method="POST" class="d-inline">
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
</div>

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

<style>
    .bg-image {
        position: relative;
        z-index: 1;
    }
    .position-absolute {
        z-index: 2;
    }
    .card {
        border: none;
        transition: transform 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection
