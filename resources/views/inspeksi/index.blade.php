@extends('layout.index')

@section('content')
<!-- End Page Title -->
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Daftar Inspeksi Kendaraan</h5>

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

                    @if(in_array(Auth::user()->code_role, ['001', '002']))
                        <div class="mb-3">
                            <a href="{{ route('inspeksi.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Tambah Baru
                            </a>
                        </div>
                    @endif

                    @if($inspeksi->isEmpty())
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
                                        <th>Jenis Inspeksi</th>
                                        <th>Deskripsi</th>
                                        <th>Pengawas</th>
                                        <th>Mitra</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inspeksi as $key => $item)
                                    <tr>
                                        <td>{{ $inspeksi->firstItem() + $key }}</td>
                                        <td>{{ $item->tanggal_inspeksi->format('d/m/Y') }}</td>
                                        <td>
                                            @if($item->jenis_inspeksi == 'komisioning')
                                                <span class="badge bg-primary">Komisioning</span>
                                            @elseif($item->jenis_inspeksi == 'perawatan')
                                                <span class="badge bg-warning">Perawatan</span>
                                            @else
                                                <span class="badge bg-info">Evaluasi Kecepatan</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($item->deskripsi, 50) }}</td>
                                        <td>{{ $item->pengawas->nama_lengkap ?? 'N/A' }}</td>
                                        <td>{{ $item->mitra->nama_perusahaan ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group" role="group" style="gap:10px">
                                                @if(in_array(Auth::user()->code_role, ['001', '002']))
                                                    <a href="{{ route('inspeksi.show', $item->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endif

                                                @if(Auth::user()->code_role == '001')
                                                    <a href="{{ route('inspeksi.edit', $item->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('inspeksi.destroy', $item->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                            {{ $inspeksi->links() }}
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
