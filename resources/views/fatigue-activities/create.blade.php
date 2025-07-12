@extends('layout.index')
@section('content')
<section class="section dashboard">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Create {{ $title ?? ucfirst(str_replace('_', ' ', $type)) }} Activity</h5>

            @if($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            @endif

            <form action="{{ route('fatigue-activities.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="activity_type" value="{{ $type }}">

              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="user_id" class="form-label">Employee</label>
                  <select class="form-select" id="user_id" name="user_id" required>
                    <option value="">Select Employee</option>
                    @foreach($users as $user)
                      <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->nama_lengkap }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="supervisor_id" class="form-label">Supervisor</label>
                  <select class="form-select" id="supervisor_id" name="supervisor_id" required>
                    <option value="">Select Supervisor</option>
                    @foreach($supervisors as $supervisor)
                      <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>{{ $supervisor->nama_lengkap }}</option>
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
                      <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="mitra_id" class="form-label">Mitra</label>
                  <select class="form-select" id="mitra_id" name="mitra_id">
                    <option value="">Select Mitra</option>
                    @foreach($mitras as $mitra)
                      <option value="{{ $mitra->id }}" {{ old('mitra_id') == $mitra->id ? 'selected' : '' }}>{{ $mitra->nama_perusahaan}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-12">
                  <label for="photo" class="form-label">Photo</label>
                  <input type="file" class="form-control" id="photo" name="photo" accept="image/jpeg,image/png" required>
                </div>
              </div>

              @if($type === \App\Models\FatigueActivity::TYPE_SIDAK)
              <div class="row mb-3">
                <div class="col-md-12">
                  <label for="location" class="form-label">Location</label>
                  <input type="text" class="form-control" id="location" name="location" value="{{ old('location') }}" required>
                </div>
              </div>
              @endif

              @if(in_array($type, [
                  \App\Models\FatigueActivity::TYPE_FTW,
                  \App\Models\FatigueActivity::TYPE_DFIT,
                  \App\Models\FatigueActivity::TYPE_FATIGUE_CHECK,
                  \App\Models\FatigueActivity::TYPE_WAKEUP_CALL
              ]))
              <div class="row mb-3">
                <div class="col-md-12">
                  <label for="result_file" class="form-label">Result File</label>
                  <input type="file" class="form-control" id="result_file" name="result_file" accept=".pdf,.jpeg,.jpg,.png" required>
                </div>
              </div>
              @endif

              <div class="row mb-3">
                <div class="col-md-12">
                  <label for="description" class="form-label">Description</label>
                  <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                </div>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('fatigue-preventive.dashboard') }}" class="btn btn-secondary">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
document.querySelector('form').addEventListener('submit', function(e) {
  console.log('Form submitted');
  console.log('Form data:', new FormData(this));
});
</script>
@endsection
