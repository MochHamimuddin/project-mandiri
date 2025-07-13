@extends('layout.index')

@section('content')
<!-- End Page Title -->
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Fire Preventive Management</h5>
                        <div>
                            <a href="{{ route('fire-preventive.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
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

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Daftar Aktivitas</h5>
                        <div>
                            <a href="{{ route('fire-preventive.create', ['type' => 'Pencucian Unit']) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Pencucian Unit
                            </a>
                            <a href="{{ route('fire-preventive.create', ['type' => 'Inspeksi APAR']) }}" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Inspeksi APAR
                            </a>
                        </div>
                    </div>

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
                                        <th>Jenis Aktivitas</th>
                                        <th>Lokasi/Deskripsi</th>
                                        <th>Pengawas</th>
                                        <th>Tanggal</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activities as $key => $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="badge {{ $item->activity_type === 'Pencucian Unit' ? 'bg-primary' : 'bg-success' }}">
                                                {{ $item->activity_type }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($item->activity_type === 'Pencucian Unit')
                                                {{ Str::limit($item->description, 50) }}
                                            @else
                                                {{ $item->inspection_location }}
                                            @endif
                                        </td>
                                        <td>{{ $item->supervisor->nama_lengkap }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $item->creator->nama_lengkap }}</td>
                                        <td>
                                            <div class="btn-group" role="group" style="gap:10px">
                                                <a href="{{ route('fire-preventive.show', $item->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                @if(auth()->user()->code_role !== '002')
                                                    <a href="{{ route('fire-preventive.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('fire-preventive.destroy', $item->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin?')">
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
                ]
            });
        });
    </script>
@endsection
