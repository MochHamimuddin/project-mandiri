@extends('layout.index')

@section('content')<!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar Mitra</h5>

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

                        <div class="mb-3">
                            <a href="{{ route('mitra.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Tambah Mitra
                            </a>
                        </div>

                        @if($mitras->isEmpty())
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Tidak ada data mitra yang tersedia.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Perusahaan</th>
                                            <th>Alamat</th>
                                            <th>PIC</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mitras as $mitra)
                                            <tr>
                                                <td>{{ $mitra->id }}</td>
                                                <td>{{ $mitra->nama_perusahaan }}</td>
                                                <td>{{ Str::limit($mitra->alamat, 50) }}</td>
                                                <td>
                                                    @if($mitra->picUser)
                                                        <span class="badge bg-primary">
                                                            <i class="bi bi-person"></i> {{ $mitra->picUser->nama_lengkap }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="bi bi-exclamation-triangle"></i> User tidak ditemukan
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-person-plus"></i> {{ $mitra->created_by ?? 'System' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-grou  p" role="group">
                                                        <a href="{{ route('mitra.edit', $mitra->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('mitra.destroy', $mitra->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus mitra ini?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
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
                    { orderable: false, targets: [5] } // Non-aktifkan sorting untuk kolom aksi
                ]
            });
        });
    </script>
@endsection
