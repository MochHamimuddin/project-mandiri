@extends('layout.index')

@section('content')
<!-- End Page Title -->
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Daftar Data SIB</h5>

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
                            <a href="{{ route('data-sib.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Buat Baru
                            </a>
                        </div>
                    @endif

                    @if($dataSibs->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Tidak ada data ditemukan.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lengkap</th>
                                        <th>NRP</th>
                                        <th>Departemen</th>
                                        <th>Jenis Pekerjaan</th>
                                        <th>Periode</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataSibs as $key => $data)
                                    <tr>
                                        <td>{{ $dataSibs->firstItem() + $key }}</td>
                                        <td>{{ $data->nama_lengkap }}</td>
                                        <td>{{ $data->nrp }}</td>
                                        <td>{{ $data->departemen }}</td>
                                        <td>{{ Str::limit($data->jenis_pekerjaan, 30) }}</td>
                                        <td>
                                            {{ $data->tanggal_mulai->format('d/m/Y') }} -
                                            {{ $data->tanggal_akhir->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group" style="gap:10px">
                                                <a href="{{ route('data-sib.show', $data->id) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(Auth::user()->code_role == '001')
                                                    <form action="{{ route('data-sib.destroy', $data->id) }}" method="POST" class="d-inline">
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
                            {{ $dataSibs->links() }}
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
