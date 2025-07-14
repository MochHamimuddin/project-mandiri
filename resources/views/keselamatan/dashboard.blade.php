@extends('layout.index')

@section('content')
<div class="container-fluid">
    <!-- Hero Section with Gradient Overlay -->
    <div class="hero-section position-relative mb-5 rounded-3 overflow-hidden">
        <img src="https://www.kppmining.com/assets/images/kpp-as-the-big-company.jpg"
             alt="KPP Mining Company"
             class="img-fluid w-100 hero-image"
             style="height: 350px; object-fit: cover;">
        <div class="hero-overlay d-flex align-items-center">
            <div class="container">
                <div class="hero-content text-white px-4 py-3 rounded" style="background-color: rgba(0, 0, 0, 0.7); max-width: 600px;">
                    <h1 class="display-5 fw-bold mb-3">Keselamatan Area Kerja</h1>
                    <p class="lead mb-0">Menciptakan lingkungan kerja yang aman dan produktif untuk semua karyawan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Safety Activities Grid -->
    <div class="row g-4">
        @foreach($activities as $activity)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card activity-card h-100 border-start border-{{ $activity['color'] }} border-4 shadow-sm">
                <div class="card-body p-4 text-center d-flex flex-column">
                    <div class="activity-icon mb-3 mx-auto rounded-circle bg-{{ $activity['color'] }}-light p-3">
                        <i class="bi {{ $activity['icon'] }} fs-2 text-{{ $activity['color'] }}"></i>
                    </div>
                    <h3 class="h5 card-title fw-bold mb-2">{{ $activity['name'] }}</h3>
                   
                    <div class="mt-auto d-grid gap-2">
                        <a href="{{ route($activity['route'], ['type' => $activity['type']]) }}"
                           class="btn btn-{{ $activity['color'] }} btn-hover">
                            <i class="bi bi-eye-fill me-1"></i> Lihat Data
                        </a>
                   
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

 
</div>

<style>
    .hero-section {
        position: relative;
        border-radius: 0.5rem;
    }
    
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
    }
    
    .hero-content {
        backdrop-filter: blur(2px);
    }
    
    .activity-card {
        transition: all 0.3s ease;
        border-radius: 0.5rem;
    }
    
    .activity-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .activity-icon {
        transition: all 0.3s ease;
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .activity-card:hover .activity-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    .bg-primary-light { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
    .bg-info-light { background-color: rgba(13, 202, 240, 0.1); }
    .bg-secondary-light { background-color: rgba(108, 117, 125, 0.1); }
    
    .btn-hover {
        transition: all 0.2s ease;
    }
    
    .btn-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>

<!-- Chart.js for statistics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('safetyChart').getContext('2d');
        const safetyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['First Time Work', 'Evaluasi D-Fit', 'Fatigue Check', 'Wake Up Call', 'Inspeksi SAGA', 'Sidak Napping'],
                datasets: [{
                    label: 'Aktivitas Bulan Ini',
                    data: [120, 190, 150, 80, 70, 110],
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(13, 202, 240, 0.7)',
                        'rgba(108, 117, 125, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(13, 110, 253, 1)',
                        'rgba(25, 135, 84, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(13, 202, 240, 1)',
                        'rgba(108, 117, 125, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Aktivitas Keselamatan 30 Hari Terakhir'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection