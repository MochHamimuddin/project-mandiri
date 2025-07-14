@extends('layout.index')
@section('content')
<section class="section dashboard">
  <div class="container">
    <div class="row mb-4">
      <div class="col-12 position-relative">
        <img src="https://www.kppmining.com/assets/images/kpp-home-banner.png" alt="KPP Banner" class="img-fluid rounded-3 shadow-sm w-100" style="max-height: 300px; object-fit: cover;">
        <div class="banner-overlay p-4">
          <h2 class="text-white mb-2">Safety & Health Programs</h2>
          <p class="text-white-50 mb-0">Access all available safety and health programs</p>
        </div>
      </div>
    </div>
    
    <div class="row g-4">
      @php
        $programs = [
          [
            'name' => 'Fatigue Preventive',
            'route' => 'fatigue-preventive.dashboard',
            'color' => 'primary',
            'icon' => 'bi-activity',
            'desc' => 'Monitor and prevent worker fatigue',
            'completion' => 85,
            'activities' => 12
          ],
          [
            'name' => 'Traffic Management',
            'route' => 'inspeksi.dashboard',
            'color' => 'success',
            'icon' => 'bi-check-circle',
            'desc' => 'Vehicle and traffic safety management',
            'completion' => 92,
            'activities' => 18
          ],
          [
            'name' => 'Workplace Safety',
            'route' => 'keselamatan.dashboard',
            'color' => 'warning',
            'icon' => 'bi-shield-check',
            'desc' => 'Work area safety programs',
            'completion' => 78,
            'activities' => 15
          ],
          [
            'name' => 'Fire Prevention',
            'route' => 'fire-preventive.dashboard',
            'color' => 'danger',
            'icon' => 'bi-fire',
            'desc' => 'Fire prevention and management',
            'completion' => 95,
            'activities' => 9
          ],
          [
            'name' => 'Manpower Development',
            'route' => 'development-manpower.dashboard',
            'color' => 'info',
            'icon' => 'bi-people-fill',
            'desc' => 'Employee training and development',
            'completion' => 80,
            'activities' => 22
          ],
          [
            'name' => 'Health Program',
            'route' => 'program-kesehatan.dashboard',
            'color' => 'secondary',
            'icon' => 'bi-heart-pulse',
            'desc' => 'Employee health initiatives',
            'completion' => 88,
            'activities' => 14
          ],
          [
            'name' => 'Environment Program',
            'route' => 'program-lingkungan.dashboard',
            'color' => 'dark',
            'icon' => 'bi-tree-fill',
            'desc' => 'Environmental management',
            'completion' => 75,
            'activities' => 11
          ],
        ];
        
        // Define explicit colors for the chart
        $chartColors = [
          'primary' => '#4e73df',
          'success' => '#1cc88a',
          'warning' => '#f6c23e',
          'danger' => '#e74a3b',
          'info' => '#36b9cc',
          'secondary' => '#858796',
          'dark' => '#5a5c69'
        ];
      @endphp

      @foreach($programs as $program)
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card program-card shadow-sm rounded-3 border-0 h-100">
            <div class="card-body text-center p-4 d-flex flex-column">
              <div class="card-icon mb-3 text-{{ $program['color'] }}">
                <i class="bi {{ $program['icon'] }}"></i>
              </div>
              <h5 class="card-title mb-2">{{ $program['name'] }}</h5>
              <p class="card-text text-muted small mb-3">{{ $program['desc'] }}</p>
              <div class="mt-auto">
                <a href="{{ route($program['route']) }}" class="btn btn-{{ $program['color'] }} btn-sm w-100">
                  <i class="bi bi-box-arrow-in-right me-1"></i> Access Program
                </a>
              </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
              <small class="text-muted">Completion: {{ $program['completion'] }}%</small>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    
    <!-- Statistics Section -->
    <div class="row mt-5">
      <!-- Pie Chart -->
      <div class="col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-4">Program Completion Status</h5>
            <div class="chart-container" style="position: relative; height: 300px;">
              <canvas id="completionChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Program Activities Table -->
      <div class="col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-4">Program Activities Summary</h5>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Program</th>
                    <th class="text-end">Activities</th>
                    <th class="text-end">Completion</th>
                    <th class="text-end">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($programs as $program)
                  <tr>
                    <td>
                      <i class="bi {{ $program['icon'] }} text-{{ $program['color'] }} me-2"></i>
                      {{ $program['name'] }}
                    </td>
                    <td class="text-end">{{ $program['activities'] }}</td>
                    <td class="text-end">
                      <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-{{ $program['color'] }}" 
                             role="progressbar" 
                             style="width: {{ $program['completion'] }}%" 
                             aria-valuenow="{{ $program['completion'] }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                          {{ $program['completion'] }}%
                        </div>
                      </div>
                    </td>
                    <td class="text-end">
                      @if($program['completion'] >= 90)
                        <span class="badge bg-success">Excellent</span>
                      @elseif($program['completion'] >= 75)
                        <span class="badge bg-primary">Good</span>
                      @elseif($program['completion'] >= 50)
                        <span class="badge bg-warning">Fair</span>
                      @else
                        <span class="badge bg-danger">Needs Improvement</span>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr class="table-light">
                    <th>Total</th>
                    <th class="text-end">{{ array_sum(array_column($programs, 'activities')) }}</th>
                    <th class="text-end">{{ round(array_sum(array_column($programs, 'completion'))/count($programs), 1) }}%</th>
                    <th class="text-end">Average</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Quick Stats Section -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-4">Program Statistics Overview</h5>
            <div class="row text-center">
              <div class="col-md-3 mb-3 mb-md-0">
                <div class="stat-card bg-primary bg-opacity-10 p-3 rounded">
                  <h3 class="text-primary mb-1">{{ count($programs) }}</h3>
                  <p class="mb-0 text-muted">Total Programs</p>
                </div>
              </div>
              <div class="col-md-3 mb-3 mb-md-0">
                <div class="stat-card bg-success bg-opacity-10 p-3 rounded">
                  <h3 class="text-success mb-1">{{ array_sum(array_column($programs, 'activities')) }}</h3>
                  <p class="mb-0 text-muted">Total Activities</p>
                </div>
              </div>
              <div class="col-md-3 mb-3 mb-md-0">
                <div class="stat-card bg-warning bg-opacity-10 p-3 rounded">
                  <h3 class="text-warning mb-1">5</h3>
                  <p class="mb-0 text-muted">Pending Actions</p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="stat-card bg-info bg-opacity-10 p-3 rounded">
                  <h3 class="text-info mb-1">{{ round(array_sum(array_column($programs, 'completion'))/count($programs), 1) }}%</h3>
                  <p class="mb-0 text-muted">Avg Completion</p>
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
  .banner-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
  }
  
  .program-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
    overflow: hidden;
  }
  
  .program-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    border-color: rgba(var(--bs-primary-rgb), 0.2);
  }
  
  .program-card .card-icon {
    font-size: 2.5rem;
    margin-bottom: 1.25rem;
    transition: transform 0.3s;
  }
  
  .program-card:hover .card-icon {
    transform: scale(1.1);
  }
  
  .program-card .card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--bs-dark);
    transition: color 0.3s;
  }
  
  .program-card:hover .card-title {
    color: var(--bs-primary);
  }
  
  .program-card .btn {
    transition: all 0.3s;
    font-weight: 500;
    letter-spacing: 0.5px;
  }
  
  .stat-card {
    transition: all 0.3s;
  }
  
  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
  }
  
  .stat-card h3 {
    font-weight: 700;
  }
  
  .table-hover tbody tr {
    transition: all 0.2s;
  }
  
  .table-hover tbody tr:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
    transform: translateX(2px);
  }
  
  .progress {
    border-radius: 10px;
    background-color: #f0f0f0;
  }
  
  .progress-bar {
    border-radius: 10px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  @media (max-width: 768px) {
    .banner-overlay {
      padding: 1rem !important;
    }
    
    .banner-overlay h2 {
      font-size: 1.5rem;
    }
    
    .program-card .card-icon {
      font-size: 2rem;
    }
  }
</style>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add animation to cards when they come into view
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate__animated', 'animate__fadeInUp');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.program-card').forEach(card => {
      observer.observe(card);
    });
    
    // Pie Chart
    const ctx = document.getElementById('completionChart').getContext('2d');
    const completionChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: [
          @foreach($programs as $program)
            '{{ $program['name'] }}',
          @endforeach
        ],
        datasets: [{
          data: [
            @foreach($programs as $program)
              {{ $program['completion'] }},
            @endforeach
          ],
          backgroundColor: [
            @foreach($programs as $program)
              '{{ $chartColors[$program['color']] }}',
            @endforeach
          ],
          borderColor: '#fff',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
            labels: {
              boxWidth: 12,
              padding: 20,
              font: {
                size: 12
              },
              usePointStyle: true,
              pointStyle: 'circle'
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return `${context.label}: ${context.raw}% completion`;
              }
            }
          }
        },
        animation: {
          animateScale: true,
          animateRotate: true
        }
      }
    });
  });
</script>
@endsection