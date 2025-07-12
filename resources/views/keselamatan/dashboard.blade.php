@extends('layout.index')

@section('content')
<div class="container-fluid">
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
                        <a href="{{ route($activity['report_route'], ['type' => $activity['type']]) }}"
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
