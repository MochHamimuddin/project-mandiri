@extends('layout.index')
@section('content')
<section class="section dashboard">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Fatigue Preventive Program</h5>

            <div class="row g-4">
              <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card info-card">
                  <div class="card-body text-center">
                    <h6 class="card-title">First Time Work</h6>
                    <a href="{{ route('fatigue-activities.create-ftw') }}" class="btn btn-primary btn-sm">Create</a>
                    <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_FTW]) }}" class="btn btn-info btn-sm">Reports</a>
                  </div>
                </div>
              </div>

              <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card info-card">
                  <div class="card-body text-center">
                    <h6 class="card-title">Evaluasi D-Fit</h6>
                    <a href="{{ route('fatigue-activities.create-dfit') }}" class="btn btn-primary btn-sm">Create</a>
                    <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_DFIT]) }}" class="btn btn-info btn-sm">Reports</a>
                  </div>
                </div>
              </div>

              <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card info-card">
                  <div class="card-body text-center">
                    <h6 class="card-title">Fatigue Check</h6>
                    <a href="{{ route('fatigue-activities.create-fatigue-check') }}" class="btn btn-primary btn-sm">Create</a>
                    <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_FATIGUE_CHECK]) }}" class="btn btn-info btn-sm">Reports</a>
                  </div>
                </div>
              </div>

              <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card info-card">
                  <div class="card-body text-center">
                    <h6 class="card-title">Wake Up Call</h6>
                    <a href="{{ route('fatigue-activities.create-wakeup-call') }}" class="btn btn-primary btn-sm">Create</a>
                    <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_WAKEUP_CALL]) }}" class="btn btn-info btn-sm">Reports</a>
                  </div>
                </div>
              </div>

              <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card info-card">
                  <div class="card-body text-center">
                    <h6 class="card-title">Inspeksi SAGA</h6>
                    <a href="{{ route('fatigue-activities.create-saga') }}" class="btn btn-primary btn-sm">Create</a>
                    <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_SAGA]) }}" class="btn btn-info btn-sm">Reports</a>
                  </div>
                </div>
              </div>

              <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card info-card">
                  <div class="card-body text-center">
                    <h6 class="card-title">Sidak Napping</h6>
                    <a href="{{ route('fatigue-activities.create-sidak') }}" class="btn btn-primary btn-sm">Create</a>
                    <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_SIDAK]) }}" class="btn btn-info btn-sm">Reports</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
