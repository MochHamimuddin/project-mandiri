@extends('layout.index')
@section('content')
<section class="section dashboard">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Edit {{ $activity->activity_type_label }} Activity</h5>

            <form action="{{ route('fatigue-activities.update', $activity->id) }}" method="POST">
              @csrf
              @method('PUT')

              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="user_id" class="form-label">Employee</label>
                  <select class="form-select" id="user_id" name="user_id" required>
                    <option value="">Select Employee</option>
                    @foreach($users as $user)
                      <option value="{{ $user->id }}" {{ $activity->user_id == $user->id ? 'selected' : '' }}>
                        {{ $user->nama_lengkap }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="supervisor_id" class="form-label">Supervisor</label>
                  <select class="form-select" id="supervisor_id" name="supervisor_id" required>
                    <option value="">Select Supervisor</option>
                    @foreach($supervisors as $supervisor)
                      <option value="{{ $supervisor->id }}" {{ $activity->supervisor_id == $supervisor->id ? 'selected' : '' }}>
                        {{ $supervisor->nama_lengkap }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="shift_id" class="form-label">Shift</label>
                  <select class="form-select" id="shift_id" name="shift_id">
                    <option value="">Select Shift</option>
                    @foreach($shifts as $shift)
                      <option value="{{ $shift->id }}" {{ $activity->shift_id == $shift->id ? 'selected' : '' }}>
                        {{ $shift->name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="mitra_id" class="form-label">Mitra</label>
                  <select class="form-select" id="mitra_id" name="mitra_id">
                    <option value="">Select Mitra</option>
                    @foreach($mitras as $mitra)
                      <option value="{{ $mitra->id }}" {{ $activity->mitra_id == $mitra->id ? 'selected' : '' }}>
                        {{ $mitra->nama_perusahaan }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              @if($activity->activity_type === \App\Models\FatigueActivity::TYPE_SIDAK)
              <div class="row mb-3">
                <div class="col-md-12">
                  <label for="location" class="form-label">Location</label>
                  <input type="text" class="form-control" id="location" name="location"
                         value="{{ $activity->location }}" required>
                </div>
              </div>
              @endif

              @if($activity->activity_type === \App\Models\FatigueActivity::TYPE_SAGA)
              <div class="row mb-3">
                <div class="col-md-12">
                  <label for="employee_name" class="form-label">Employee Name</label>
                  <input type="text" class="form-control" id="employee_name" name="employee_name"
                         value="{{ $activity->employee_name }}" required>
                </div>
              </div>
              @endif

              <div class="row mb-3">
                <div class="col-md-12">
                  <label for="description" class="form-label">Description</label>
                  <textarea class="form-control" id="description" name="description" rows="3" required>
                    {{ $activity->description }}
                  </textarea>
                </div>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('fatigue-activities.show', $activity->id) }}" class="btn btn-secondary">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
