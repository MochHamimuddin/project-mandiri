@extends('layout.index')
@section('content')
<section class="section dashboard">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Fatigue Activities Report</h5>

            @if(auth()->user()->code_role == '001')
              <div class="p-3 border-bottom">
                <form id="filter-form" method="GET" class="row g-2">
                  <div class="col-md-3">
                    <select class="form-select form-select-sm" name="type" id="activity-type-filter">
                      <option value="">All Activity Types</option>
                      @foreach($activityTypes as $type => $label)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                          {{ $label }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </form>
              </div>
            @endif

            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Activity Type</th>
                    <th>Employee</th>
                    <th>Supervisor</th>
                    <th>Mitra</th>
                    <th>Shift</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($activities as $activity)
                    @if(auth()->user()->code_role == '001' || auth()->user()->id == $activity->user_id)
                      <tr>
                        <td>{{ $activity->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $activity->activity_type_label }}</td>
                        <td>{{ $activity->user?->nama_lengkap ?? '-' }}</td>
                        <td>{{ $activity->supervisor?->nama_lengkap ?? '-' }}</td>
                        <td>{{ $activity->mitra?->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $activity->shift?->name ?? '-' }}</td>
                        <td>
                          @if(auth()->user()->code_role == '001')
                            <button class="btn btn-sm toggle-approval
                              {{ $activity->is_approved ? 'btn-success' : 'btn-warning' }}"
                              data-id="{{ $activity->id }}"
                              onclick="toggleApproval(this)">
                              {{ $activity->is_approved ? 'Closed' : 'Open' }}
                            </button>
                          @else
                            <span class="badge bg-{{ $activity->is_approved ? 'success' : 'warning' }}">
                              {{ $activity->is_approved ? 'Closed' : 'Open' }}
                            </span>
                          @endif
                        </td>
                        <td>
                          <a href="{{ route('fatigue-activities.show', $activity->id) }}" class="btn btn-sm btn-info">View</a>
                          @if(auth()->user()->code_role == '001')
                            <a href="{{ route('fatigue-activities.edit', $activity->id) }}" class="btn btn-sm btn-warning">Edit</a>
                          @endif
                        </td>
                      </tr>
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
  document.getElementById('activity-type-filter')?.addEventListener('change', function() {
    this.form.submit();
  });

  function toggleApproval(button) {
    const activityId = button.dataset.id;
    const url = '{{ route("fatigue-activities.toggle-approval", ["id" => ":id"]) }}'.replace(':id', activityId);

    fetch(url, {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })
    .then(response => {
      if (!response.ok) throw new Error('Network response was not ok');
      return response.json();
    })
    .then(data => {
      if (data.success) {
        button.classList.toggle('btn-success');
        button.classList.toggle('btn-warning');
        button.innerText = data.status_text;
        alert('Status updated! New status: ' + data.status_text);
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to update status. Check console for details.');
    });
  }
</script>
@endsection
