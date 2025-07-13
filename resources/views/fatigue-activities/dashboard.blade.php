@extends('layout.index')
@section('content')
<section class="section dashboard">
    <div class="container">
        <div class="row">
            <div class="col-12">
                      <!-- Gambar Header -->
                        <div class="text-center mb-4">
                            <img src="https://awsimages.detik.net.id/visual/2019/04/29/baabc598-e49c-4050-9d24-237b3152ba34_169.jpeg?w=650" 
                                 alt="Fatigue Prevention" 
                                 class="img-fluid rounded"
                                 style="max-height: 500px;">
                        </div>
                <div class="card">
                    <div class="card-body p-4">
                
                        
                        <h5 class="card-title mb-4 text-center">Fatigue Preventive Program</h5>

                        <div class="row g-4">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <h6 class="card-title mb-3">First Time Work</h6>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-ftw') }}" class="btn btn-primary btn-sm mb-2 w-100">Create</a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_FTW]) }}" class="btn btn-info btn-sm w-100">Reports</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <h6 class="card-title mb-3">Evaluasi D-Fit</h6>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-dfit') }}" class="btn btn-primary btn-sm mb-2 w-100">Create</a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_DFIT]) }}" class="btn btn-info btn-sm w-100">Reports</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <h6 class="card-title mb-3">Fatigue Check</h6>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-fatigue-check') }}" class="btn btn-primary btn-sm mb-2 w-100">Create</a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_FATIGUE_CHECK]) }}" class="btn btn-info btn-sm w-100">Reports</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <h6 class="card-title mb-3">Wake Up Call</h6>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-wakeup-call') }}" class="btn btn-primary btn-sm mb-2 w-100">Create</a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_WAKEUP_CALL]) }}" class="btn btn-info btn-sm w-100">Reports</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <h6 class="card-title mb-3">Inspeksi SAGA</h6>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-saga') }}" class="btn btn-primary btn-sm mb-2 w-100">Create</a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_SAGA]) }}" class="btn btn-info btn-sm w-100">Reports</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <h6 class="card-title mb-3">Sidak Napping</h6>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-sidak') }}" class="btn btn-primary btn-sm mb-2 w-100">Create</a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_SIDAK]) }}" class="btn btn-info btn-sm w-100">Reports</a>
                                        </div>
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