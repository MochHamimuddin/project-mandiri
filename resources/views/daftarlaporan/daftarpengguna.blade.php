@extends('layout.index')
@section('content')
<section class="section dashboard">
  <div class="container">
    <div class="row g-4">
      @php
        $programs = [
          [
            'name' => 'Fatigue Preventive',
            'route' => 'fatigue-preventive.dashboard',
            'color' => 'primary',
            'icon' => 'bi-activity'
          ],
          [
            'name' => 'Traffic Management Preventive Program',
            'route' => 'inspeksi.dashboard',
            'color' => 'success',
            'icon' => 'bi-traffic-light'
          ],
          [
            'name' => 'Keselamatan Area Kerja',
            'route' => 'keselamatan.dashboard',
            'color' => 'warning',
            'icon' => 'bi-shield-check'
          ],
          [
            'name' => 'Fire Prevention Management Program',
            'route' => 'dashboard',
            'color' => 'danger',
            'icon' => 'bi-fire'
          ],
          [
            'name' => 'Development Manpower',
            'route' => 'dashboard',
            'color' => 'info',
            'icon' => 'bi-people-fill'
          ],
          [
            'name' => 'Program Kerja Kesehatan',
            'route' => 'keselamatan.dashboard',
            'color' => 'secondary',
            'icon' => 'bi-heart-pulse'
          ],
          [
            'name' => 'Program Kerja Lingkungan Hidup',
            'route' => 'dashboard',
            'color' => 'dark',
            'icon' => 'bi-tree-fill'
          ]
        ];
      @endphp

      @foreach($programs as $program)
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card info-card {{ $program['color'] }}-card">
            <div class="card-body text-center">
              <div class="card-icon">
                <i class="bi {{ $program['icon'] }}"></i>
              </div>
              <h5 class="card-title">{{ $program['name'] }}</h5>
              <div class="d-flex justify-content-center gap-2">
                <a href="{{ route($program['route']) }}" class="btn btn-{{ $program['color'] }} btn-sm">
                  <i class="bi bi-box-arrow-in-right"></i> Masuk
                </a>
                <a href="{{ route($program['route']) }}" class="btn btn-{{ $program['color'] }} btn-sm">
                  <i class="bi bi-box-arrow-in-right"></i> Laporan
                </a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<style>
  .card-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--bs-{{ $program['color'] }});
  }
  .card {
    transition: transform 0.3s;
    height: 100%;
  }
  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  }
</style>
@endsection
