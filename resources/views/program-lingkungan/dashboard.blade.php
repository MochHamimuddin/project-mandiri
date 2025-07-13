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
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Aktivitas Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis Kegiatan</th>
                                <th>Lokasi</th>
                                <th>Pelaksana</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestActivities as $activity)
                            <tr>
                                <td>{{ $activity->tanggal_kegiatan->format('d/m/Y') }}</td>
                                <td>{{ $activity->jenis_kegiatan }}</td>
                                <td>{{ $activity->lokasi }}</td>
                                <td>{{ $activity->pelaksana }}</td>
                                <td>
                                    <a href="{{ route('program-lingkungan.show', $activity->id) }}" class="btn btn-sm btn-info">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

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