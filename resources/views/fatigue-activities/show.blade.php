@extends('layout.index')
@section('content')
<section class="section dashboard">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">{{ $activity->activity_type_label }} Details</h5>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <h6>Activity Information</h6>
                  <hr>
                  <p><strong>Date:</strong> {{ $activity->created_at->format('d M Y H:i') }}</p>
                  <p><strong>Employee:</strong> {{ $activity->user->nama_lengkap ?? '-' }}</p>
                  <p><strong>Supervisor:</strong> {{ $activity->supervisor->nama_lengkap  ?? '-'}}</p>
                  <p><strong>Mitra:</strong> {{ $activity->mitra->nama_perusahaan ?? '-' }}</p>
                  <p><strong>Shift:</strong> {{ $activity->shift->name ?? '-' }}</p>
                </div>

                @if($activity->activity_type === \App\Models\FatigueActivity::TYPE_SIDAK)
                <div class="mb-3">
                  <h6>Location</h6>
                  <hr>
                  <p>{{ $activity->location ?? '-'}}</p>
                </div>
                @endif

                @if($activity->activity_type === \App\Models\FatigueActivity::TYPE_SAGA)
                <div class="mb-3">
                  <h6>Employee Name</h6>
                  <hr>
                  <p>{{ $activity->nama_perusahaan ?? '-' }}</p>
                </div>
                @endif

                <div class="mb-3">
                  <h6>Description</h6>
                  <hr>
                  <p>{{ $activity->description ?? '-'}}</p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <h6>Photo</h6>
                  <hr>
                  <img src="{{ Storage::url($activity->photo_path) ?? '-' }}" class="img-fluid rounded" alt="Activity Photo">
                </div>

                @if($activity->result_path)
                <div class="mb-3">
                  <h6>Result File</h6>
                  <hr>
                  <a href="{{ Storage::url($activity->result_path) ?? '-' }}" target="_blank" class="btn btn-sm btn-primary">
                    View Result
                  </a>
                </div>
                @endif
              </div>
            </div>

            <div class="text-center mt-3">
              <a href="{{ route('fatigue-activities.index') }}" class="btn btn-secondary">Back</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
