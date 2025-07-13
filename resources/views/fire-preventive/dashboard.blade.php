@extends('layout.index')

@section('content')
<div class="container-fluid p-0">
    <!-- Background Image Section -->
    <div class="position-relative">
        <img src="https://www.kppmining.com/assets/images/why-us.png" 
             alt="KPP Mining Why Us Background" 
             class="img-fluid w-100"
             style="height: 300px; object-fit: cover;">
        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: rgba(0,0,0,0.5);">
            <h1 class="text-white display-4">Fire Preventive Management Dashboard</h1>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <!-- Pencucian Unit Card -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Pencucian Unit</h5>
                    </div>
                    <div class="card-body">
                        <h1 class="display-4">{{ $pencucianCount }}</h1>
                        <p class="card-text">Total Pencucian Unit Terjadwal</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('fire-preventive.create', ['type' => 'Pencucian Unit']) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Baru
                            </a>
                            <a href="{{ route('fire-preventive.index') }}?type=Pencucian Unit" class="btn btn-outline-primary">
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inspeksi APAR Card -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Inspeksi APAR</h5>
                    </div>
                    <div class="card-body">
                        <h1 class="display-4">{{ $inspeksiCount }}</h1>
                        <p class="card-text">Total Inspeksi Bulanan</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('fire-preventive.create', ['type' => 'Inspeksi APAR']) }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Tambah Baru
                            </a>
                            <a href="{{ route('fire-preventive.index') }}?type=Inspeksi APAR" class="btn btn-outline-success">
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection