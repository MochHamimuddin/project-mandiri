@extends('layout.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Detail Program Kesehatan</h2>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('program-kesehatan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ $program->jenis_program }} - {{ $program->tanggal_upload->format('d F Y') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Jenis Program</th>
                                <td>
                                    <span class="badge {{ $program->jenis_program == \App\Models\ProgramKerjaKesehatan::MCU_TAHUNAN ? 'bg-primary-subtle text-primary-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                    {{ $program->jenis_program_label }}
                                </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Upload</th>
                                <td>{{ $program->tanggal_upload->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Pengawas</th>
                                <td>{{ $program->pengawas->nama_lengkap }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Oleh</th>
                                <td>{{ $program->creator->nama_lengkap ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $program->deskripsi }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">Foto</h6>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ Storage::url($program->foto_path) }}" alt="Foto" class="img-fluid mb-2" style="max-height: 200px;">
                            <a href="{{ route('program-kesehatan.download', ['program' => $program->id, 'type' => 'foto']) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i> Download Foto
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="m-0 font-weight-bold">
                                @if($program->jenis_program == 'MCU Tahunan')
                                    Hasil D-Fit
                                @else
                                    Hasil Kontrol Karyawan
                                @endif
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-4x mb-3 text-secondary"></i>
                            <p class="mb-2">{{ basename($program->dokumen_path) }}</p>
                            <a href="{{ route('program-kesehatan.download', ['program' => $program->id, 'type' => 'dokumen']) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i> Download Dokumen
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
