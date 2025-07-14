@extends('layout.index')
@section('content')
<section class="section dashboard">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Hero Section with Image and Text -->
                <div class="hero-section mb-4 position-relative">
                    <img src="https://awsimages.detik.net.id/visual/2019/04/29/baabc598-e49c-4050-9d24-237b3152ba34_169.jpeg?w=650" 
                         alt="Fatigue Prevention" 
                         class="img-fluid rounded hero-image"
                         style="max-height: 400px; width: 100%; object-fit: cover;">
                    <div class="hero-overlay d-flex align-items-center justify-content-center">
                        <div class="text-center text-white p-3 rounded" style="background-color: rgba(0,0,0,0.6);">
                            <h1 class="display-5 fw-bold">Fatigue Preventive Program</h1>
                            <p class="lead">Ensuring safety and well-being in the workplace</p>
                        </div>
                    </div>
                </div>

                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <!-- Program Cards with Hover Effects -->
                        <div class="row g-4">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100 program-card">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <div class="program-icon mb-3">
                                            <i class="bi bi-person-plus-fill fs-1 text-primary"></i>
                                        </div>
                                        <h5 class="card-title mb-3">First Time Work</h5>
                                        <p class="text-muted small">For new employees</p>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-ftw') }}" class="btn btn-primary btn-sm mb-2 w-100">
                                                <i class="bi bi-plus-circle me-1"></i> Create
                                            </a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_FTW]) }}" class="btn btn-outline-info btn-sm w-100">
                                                <i class="bi bi-file-text me-1"></i> Reports
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100 program-card">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <div class="program-icon mb-3">
                                            <i class="bi bi-heart-pulse-fill fs-1 text-danger"></i>
                                        </div>
                                        <h5 class="card-title mb-3">Evaluasi D-Fit</h5>
                                        <p class="text-muted small">Fitness evaluation</p>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-dfit') }}" class="btn btn-primary btn-sm mb-2 w-100">
                                                <i class="bi bi-plus-circle me-1"></i> Create
                                            </a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_DFIT]) }}" class="btn btn-outline-info btn-sm w-100">
                                                <i class="bi bi-file-text me-1"></i> Reports
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100 program-card">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <div class="program-icon mb-3">
                                            <i class="bi bi-clipboard2-pulse-fill fs-1 text-warning"></i>
                                        </div>
                                        <h5 class="card-title mb-3">Fatigue Check</h5>
                                        <p class="text-muted small">Regular fatigue assessment</p>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-fatigue-check') }}" class="btn btn-primary btn-sm mb-2 w-100">
                                                <i class="bi bi-plus-circle me-1"></i> Create
                                            </a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_FATIGUE_CHECK]) }}" class="btn btn-outline-info btn-sm w-100">
                                                <i class="bi bi-file-text me-1"></i> Reports
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100 program-card">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <div class="program-icon mb-3">
                                            <i class="bi bi-alarm-fill fs-1 text-success"></i>
                                        </div>
                                        <h5 class="card-title mb-3">Wake Up Call</h5>
                                        <p class="text-muted small">Alertness program</p>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-wakeup-call') }}" class="btn btn-primary btn-sm mb-2 w-100">
                                                <i class="bi bi-plus-circle me-1"></i> Create
                                            </a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_WAKEUP_CALL]) }}" class="btn btn-outline-info btn-sm w-100">
                                                <i class="bi bi-file-text me-1"></i> Reports
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100 program-card">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <div class="program-icon mb-3">
                                            <i class="bi bi-clipboard-check-fill fs-1 text-info"></i>
                                        </div>
                                        <h5 class="card-title mb-3">Inspeksi SAGA</h5>
                                        <p class="text-muted small">Safety inspection</p>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-saga') }}" class="btn btn-primary btn-sm mb-2 w-100">
                                                <i class="bi bi-plus-circle me-1"></i> Create
                                            </a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_SAGA]) }}" class="btn btn-outline-info btn-sm w-100">
                                                <i class="bi bi-file-text me-1"></i> Reports
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card info-card h-100 program-card">
                                    <div class="card-body p-3 text-center d-flex flex-column">
                                        <div class="program-icon mb-3">
                                            <i class="bi bi-moon-stars-fill fs-1 text-secondary"></i>
                                        </div>
                                        <h5 class="card-title mb-3">Sidak Napping</h5>
                                        <p class="text-muted small">Rest area inspection</p>
                                        <div class="mt-auto">
                                            <a href="{{ route('fatigue-activities.create-sidak') }}" class="btn btn-primary btn-sm mb-2 w-100">
                                                <i class="bi bi-plus-circle me-1"></i> Create
                                            </a>
                                            <a href="{{ route('fatigue-activities.index', ['type' => \App\Models\FatigueActivity::TYPE_SIDAK]) }}" class="btn btn-outline-info btn-sm w-100">
                                                <i class="bi bi-file-text me-1"></i> Reports
                                            </a>
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

<style>
    .hero-section {
        position: relative;
        overflow: hidden;
        border-radius: 0.5rem;
    }
    
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }
    
    .program-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .program-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    
    .program-icon {
        transition: all 0.3s ease;
    }
    
    .program-card:hover .program-icon {
        transform: scale(1.1);
    }
    
    .card-title {
        font-weight: 600;
    }
    
    .btn-primary {
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>

@endsection