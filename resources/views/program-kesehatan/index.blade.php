@extends('layout.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Program Kerja Kesehatan</h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('program-kesehatan.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <div class="btn-group">
                <a href="{{ route('program-kesehatan.create', ['type' => 'MCU_TAHUNAN']) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> MCU Tahunan
                </a>
                <a href="{{ route('program-kesehatan.create', ['type' => 'PENYAKIT_KRONIS']) }}" class="btn btn-danger">
                    <i class="fas fa-plus"></i> Penyakit Kronis
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('program-kesehatan.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="type">Jenis Program</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Semua Jenis</option>
                                <option value="MCU_TAHUNAN" {{ request('type') == 'MCU_TAHUNAN' ? 'selected' : '' }}>MCU Tahunan</option>
                                <option value="PENYAKIT_KRONIS" {{ request('type') == 'PENYAKIT_KRONIS' ? 'selected' : '' }}>Penyakit Kronis</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('program-kesehatan.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Program Kesehatan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
                                <span class="badge {{ $program->jenis_program == \App\Models\ProgramKerjaKesehatan::MCU_TAHUNAN ? 'bg-primary-subtle text-primary-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                    {{ $program->jenis_program_label }}
                                </span>
                            </td>
                            <td>{{ Str::limit($program->deskripsi, 50) }}</td>
                            <td>{{ $program->pengawas->nama_lengkap }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('program-kesehatan.show', $program->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('program-kesehatan.edit', $program->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('program-kesehatan.destroy', $program->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $programs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
