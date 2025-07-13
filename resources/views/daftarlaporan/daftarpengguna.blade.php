@extends('layout.index')
@section('content')
<section class="section dashboard">
  <div class="container">
       <div class="row mb-4">
      <div class="col-12">
        <img src="https://www.kppmining.com/assets/images/kpp-home-banner.png" alt="KPP Banner" class="img-fluid rounded-3 shadow-sm w-100" style="max-height: 300px; object-fit: cover;">
      </div>
    </div>
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
            'icon' => 'bi-check-circle'
          ],
          [
            'name' => 'Keselamatan Area Kerja',
            'route' => 'keselamatan.dashboard',
            'color' => 'warning',
            'icon' => 'bi-shield-check'
          ],
          [
            'name' => 'Fire Prevention Management Program',
            'route' => 'fire-preventive.dashboard',
            'color' => 'danger',
            'icon' => 'bi-fire'
          ],
          [
            'name' => 'Development Manpower',
            'route' => 'development-manpower.dashboard',
            'color' => 'info',
            'icon' => 'bi-people-fill'
          ],
          [
            'name' => 'Program Kerja Kesehatan',
            'route' => 'program-kesehatan.dashboard',
            'color' => 'secondary',
            'icon' => 'bi-heart-pulse'
          ],
          [
            'name' => 'Program Kerja Lingkungan Hidup',
            'route' => 'program-lingkungan.dashboard',
            'color' => 'dark',
            'icon' => 'bi-tree-fill'
          ],
          [
            'name' => 'Update SIB HRA',
            'route' => 'dashboard',
            'color' => 'success',
            'icon' => 'bi-arrow-repeat'
          ]
        ];
      @endphp

      @foreach($programs as $program)
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card info-card {{ $program['color'] }}-card shadow-sm rounded-3">
            <div class="card-body text-center p-4">
              <div class="card-icon mb-3 text-{{ $program['color'] }}">
                <i class="bi {{ $program['icon'] }}"></i>
              </div>
              <h5 class="card-title mb-3">{{ $program['name'] }}</h5>
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
  .card {
    transition: transform 0.3s;
    height: 100%;
    margin-bottom: 1rem;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }

  .card-body {
    padding: 1.5rem !important;
  }

  .card-icon {
    font-size: 2rem;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .card-title {
    font-size: 1rem;
    font-weight: 600;
  }

  .gap-2 {
    gap: 0.75rem !important;
  }
</style>
@endsection
