@extends('layout.index')

@section('content')
<!-- End Page Title -->
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Daftar Program Lingkungan Hidup</h5>
                        <div>
                            <a href="{{ route('program-lingkungan.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Dashboard
                            </a>
                            <div class="btn-group ms-2">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bi bi-plus-circle"></i> Tambah Baru
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('program-lingkungan.create', 'krida') }}">Krida Area</a>
                                    <a class="dropdown-item" href="{{ route('program-lingkungan.create', 'pengelolaan') }}">Pengelolaan Lingkungan</a>
                                </div>
                            </div>
                        </div>
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
                            <i class="bi bi-info-circle"></i> Tidak ada data ditemukan.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jenis Kegiatan</th>
                                        <th>Lokasi</th>
                                        <th>Pelaksana</th>
                                        <th>Keterangan Singkat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activities as $index => $activity)
                                    <tr>
                                        <td>{{ $activities->firstItem() + $index }}</td>
                                        <td>{{ $activity->tanggal_kegiatan->format('d/m/Y') }}</td>
                                        <td>{{ $activity->jenis_kegiatan }}</td>
                                        <td>{{ $activity->lokasi }}</td>
                                        <td>{{ $activity->pelaksana }}</td>
                                        <td>
                                            @if($activity->jenis_kegiatan == 'Krida Area Office/Workshop')
                                                {{ Str::limit($activity->deskripsi, 50) }}
                                            @else
                                                Temuan: {{ Str::limit($activity->detail_temuan, 25) }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group" style="gap:10px">
                                                <a href="{{ route('program-lingkungan.show', $activity->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                @if(auth()->user()->code_role === '001')
                                                    <a href="{{ route('program-lingkungan.edit', $activity->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('program-lingkungan.destroy', $activity->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus?')">
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
                    { orderable: false, targets: [6] } // Non-aktifkan sorting untuk kolom aksi
                ],
                paging: false, // Disable DataTables pagination since we're using Laravel pagination
                info: false,
                searching: false
            });
        });
    </script>
@endsection
