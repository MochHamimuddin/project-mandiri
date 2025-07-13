@extends('layout.index')

@section('content')
<div class="container">
    <h2 class="mb-4">Fire Preventive Management Dashboard</h2>

    <div class="row">
        <!-- Pencucian Unit Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pencucian Unit</h5>
                </div>
                <div class="card-body">
                    <h1 class="display-4">{{ $pencucianCount }}</h1>
                    <p class="card-text">Total Pencucian Unit Terjadwal</p>
                    <a href="{{ route('fire-preventive.create', ['type' => 'Pencucian Unit']) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Baru
                    </a>
                    <a href="{{ route('fire-preventive.index') }}?type=Pencucian Unit" class="btn btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
            </div>
        </div>

        <!-- Inspeksi APAR Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Inspeksi APAR</h5>
                </div>
                <div class="card-body">
                    <h1 class="display-4">{{ $inspeksiCount }}</h1>
                    <p class="card-text">Total Inspeksi Bulanan</p>
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
@endsection
