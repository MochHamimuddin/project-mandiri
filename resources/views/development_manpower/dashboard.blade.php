@extends('layout.index')

@section('content')
<div class="container">
    <!-- Header Image Section -->
    <div class="row mb-4">
        <div class="col-12">
            <img src="https://www.kppmining.com/api/assets/gallery-1670436826134-4.jpeg?folder=gallery" 
                 alt="KPP Mining Development" 
                 class="img-fluid rounded shadow"
                 style="max-height: 300px; width: 100%; object-fit: cover;">
        </div>
    </div>

    <h2 class="mb-4 text-center">Development Manpower Dashboard</h2>

    <div class="row">
        @foreach($categories as $category)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $category }}</h5>
                    <h2 class="display-4">{{ $counts[$category] }}</h2>
                </div>
                <div class="card-footer bg-transparent text-center">
                    <a href="{{ route('development-manpower.create', ['kategori' => $category]) }}" class="btn btn-sm btn-primary mr-2">
                        <i class="fas fa-plus"></i> Tambah
                    </a>
                    <a href="{{ route('development-manpower.index') }}?kategori={{ urlencode($category) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-list"></i> Lihat Semua
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection