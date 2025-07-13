@extends('layout.index')

@section('content')
<!-- End Page Title -->
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Program Kerja Kesehatan</h5>
                        <div>
                            <a href="{{ route('program-kesehatan.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                            </a>
                            <div class="btn-group ms-2">
                                <a href="{{ route('program-kesehatan.create', ['type' => 'MCU_TAHUNAN']) }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> MCU Tahunan
                                </a>
                                <a href="{{ route('program-kesehatan.create', ['type' => 'PENYAKIT_KRONIS']) }}" class="btn btn-danger">
                                    <i class="bi bi-plus-circle"></i> Penyakit Kronis
                                </a>
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

                    @if($programs->isEmpty())
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
                                        <th>Jenis Program</th>
                                        <th>Deskripsi</th>
                                        <th>Pengawas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($programs as $key => $program)
                                    <tr>
                                        <td>{{ $programs->firstItem() + $key }}</td>
                                        <td>{{ $program->tanggal_upload->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge {{ $program->jenis_program == \App\Models\ProgramKerjaKesehatan::MCU_TAHUNAN ? 'bg-primary' : 'bg-danger' }}">
                                                {{ $program->jenis_program_label }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($program->deskripsi, 50) }}</td>
                                        <td>{{ $program->pengawas->nama_lengkap }}</td>
                                        <td>
                                            <div class="btn-group" role="group" style="gap:10px">
                                                <a href="{{ route('program-kesehatan.show', $program->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                @if(auth()->user()->code_role === '001')
                                                    <a href="{{ route('program-kesehatan.edit', $program->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('program-kesehatan.destroy', $program->id) }}" method="POST" class="d-inline">
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

                        <div class="d-flex justify-content-center mt-4">
                            {{ $programs->links() }}
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
