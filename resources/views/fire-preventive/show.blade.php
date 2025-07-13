@extends('layout.index')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Detail Aktivitas</h4>
            <a href="{{ route('fire-preventive.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Jenis Aktivitas</th>
                            <td>
                                <span class="badge {{ $activity->activity_type === 'Pencucian Unit' ? 'bg-primary' : 'bg-success' }}">
                                    {{ $activity->activity_type }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Pengawas</th>
                            <td>{{ $activity->supervisor->nama_lengkap }}</td>
                        </tr>
                        @if($activity->activity_type === 'Inspeksi APAR')
                        <tr>
                            <th>Lokasi Inspeksi</th>
                            <td>{{ $activity->inspection_location }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Dibuat Oleh</th>
                            <td>{{ $activity->creator->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <h5>Foto:</h5>
                        <img src="{{ asset('storage/' . $activity->foto_path) }}" alt="Foto Aktivitas" class="img-fluid rounded">
                    </div>

                    @if($activity->activity_type === 'Pencucian Unit' && $activity->form_fpp_path)
                    <div class="mb-3">
                        <h5>Form FPP:</h5>
                        <a href="{{ asset('storage/' . $activity->form_fpp_path) }}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-download"></i> Download Form
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-3">
                <h5>Deskripsi:</h5>
                <div class="border p-3 rounded bg-light">
                    {{ $activity->description }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
