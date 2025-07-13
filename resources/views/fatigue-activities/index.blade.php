@extends('layout.index')

@section('content')
<!-- End Page Title -->
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Fatigue Activities Report</h5>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($activities->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Tidak ada data ditemukan.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
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
                                    @foreach($activities as $key => $activity)
                                        @if(auth()->user()->code_role == '001' || auth()->user()->id == $activity->user_id)
                                            <tr>
                                                <td>{{ $activities->firstItem() + $key }}</td>
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
                                                    <div class="btn-group" role="group" style="gap:10px">
                                                        <a href="{{ route('fatigue-activities.show', $activity->id) }}" class="btn btn-sm btn-info" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @if(auth()->user()->code_role == '001')
                                                            <a href="{{ route('fatigue-activities.edit', $activity->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $activities->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
    @if(session('success'))
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 3000);
        });
    </script>
    @endif

    <script>
        $(document).ready(function() {
            // Inisialisasi datatable
            $('.datatable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json'
                },
                columnDefs: [
                    { orderable: false, targets: [8] } // Non-aktifkan sorting untuk kolom aksi
                ],
                paging: false, // Disable DataTables pagination since we're using Laravel pagination
                info: false,
                searching: false
            });

            document.getElementById('activity-type-filter')?.addEventListener('change', function() {
                this.form.submit();
            });
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
                    // Show success message using template's alert style
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        Status updated to ${data.status_text}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    `;
                    document.querySelector('.card-body').prepend(alertDiv);

                    // Auto dismiss after 3 seconds
                    setTimeout(() => {
                        $(alertDiv).alert('close');
                    }, 3000);
                } else {
                    throw new Error(data.message || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.innerHTML = `
                    Failed to update status: ${error.message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                `;
                document.querySelector('.card-body').prepend(alertDiv);

                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    $(alertDiv).alert('close');
                }, 5000);
            });
        }
    </script>
@endsection
