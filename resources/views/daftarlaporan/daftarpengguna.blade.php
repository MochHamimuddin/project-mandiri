@extends('layout.index')
@section('content')
<section class="section dashboard">
  <div class="container">
    <div class="row g-4">
      @php
        $programs = [
          'Fatigue Preventive',
          'Traffic Management Preventive Program',
          'Keselamatan Area Kerja',
          'Fire Prevention Management Program',
          'Development Manpower',
          'Program Kerja Kesehatan',
          'Program Kerja Lingkungan Hidup'
        ];
      @endphp

      @foreach($programs as $program)
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card info-card sales-card">
            <div class="card-body text-center">
              <h5 class="card-title">{{ $program }}</h5>
              <a href="#" class="btn btn-primary btn-sm">Laporan</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endsection
