@extends('layout.index')

@section('content')
<div class="container-fluid">
    <!-- KPP Mining Header Image -->
    <div class="row mb-4">
        <div class="col-md-12">
            <img src="https://www.kppmining.com/assets/images/kpp-as-the-big-company.jpg"
                 alt="KPP Mining Company"
                 class="img-fluid rounded shadow"
                 style="max-height: 300px; width: 100%; object-fit: cover;">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <h2 class="text-center">Keselamatan Area Kerja</h2>
        </div>
    </div>

    <div class="row">
        @foreach($activities as $activity)
        <div class="col-md-4 mb-4">
            <div class="card border-{{ $activity['color'] }} shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi {{ $activity['icon'] }} fs-1 text-{{ $activity['color'] }}"></i>
                    </div>
                    <h5 class="card-title">{{ $activity['name'] }}</h5>
                    <p class="card-text">Total: {{ $activity['count'] }}</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route($activity['route'], ['type' => $activity['type']]) }}"
                           class="btn btn-outline-{{ $activity['color'] }}">
                            <i class="bi bi-list"></i> Lihat
                        </a>
                        <a href="{{ route($activity['route'], ['type' => $activity['type']]) }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-file-earmark-text"></i> Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
