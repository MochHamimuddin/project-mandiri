@extends('layout.index')

@section('content')
<!-- End Page Title -->
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Development Manpower</h5>
                        <a href="{{ route('development-manpower.dashboard') }}" class="btn btn-info">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </div>

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
                            <i class="bi bi-info-circle"></i> Tidak ada data aktivitas yang tersedia.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Tanggal</th>
                                        <th>Pengawas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activities as $activity)
                                    <tr>
                                        <td>{{ $loop->iteration + ($activities->currentPage() - 1) * $activities->perPage() }}</td>
                                        <td>{{ $activity->kategori_aktivitas }}</td>
                                        <td>{{ Str::limit($activity->deskripsi, 50) }}</td>
                                        <td>{{ $activity->tanggal_aktivitas->format('d/m/Y') }}</td>
                                        <td>
                                            @if($activity->pengawas)
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-person"></i> {{ $activity->pengawas->nama_lengkap }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group" style="gap: 10px;">
                                                <a href="{{ route('development-manpower.show', $activity->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(auth()->user()->code_role !== '002')
                                                    <a href="{{ route('development-manpower.edit', $activity->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('development-manpower.destroy', $activity->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $activities->appends(request()->query())->links() }}
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
                    { orderable: false, targets: [5] } // Non-aktifkan sorting untuk kolom aksi
                ],
                paging: false, // Disable DataTables pagination since we're using Laravel pagination
                info: false,
                searching: false
            });
        });
    </script>
@endsection
