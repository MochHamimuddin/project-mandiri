@extends('layout.index')

@section('content')
<div class="container">
    <h2 class="mb-4">Dashboard Program Lingkungan Hidup</h2>

    <div class="row mb-4">
        <!-- Card Krida Area -->
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Krida Area Office/Workshop</h5>
                    <h1 class="display-4 text-center">{{ $kridaCount }}</h1>
                    <p class="card-text">Total kegiatan penghijauan dan kerja bakti</p>
                    <a href="{{ route('program-lingkungan.create', 'krida') }}" class="btn btn-light">Tambah Baru</a>
                </div>
            </div>
        </div>

        <!-- Card Pengelolaan Lingkungan -->
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pengelolaan Lingkungan Workshop</h5>
                    <h1 class="display-4 text-center">{{ $pengelolaanCount }}</h1>
                    <p class="card-text">Total kegiatan pengelolaan lingkungan</p>
                    <a href="{{ route('program-lingkungan.create', 'pengelolaan') }}" class="btn btn-light">Tambah Baru</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="card">
        <div class="card-header">
            <h5>Aktivitas Terbaru</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
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
@endsection
