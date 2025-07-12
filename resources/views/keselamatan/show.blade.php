@extends('layout.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2>Detail Aktivitas</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('keselamatan.type.index', ['type' => $activity->activity_type]) }}"
               class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Jenis Aktivitas</th>
                            <td>{{ $activityTypes[$activity->activity_type] ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <th>Pengawas</th>
                            <td>{{ $activity->pengawas->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <th>Mitra</th>
                            <td>{{ $activity->mitra->nama_perusahaan }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Oleh</th>
                            <td>{{ $activity->creator->nama_lengkap ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Diperbarui Oleh</th>
                            <td>{{ $activity->updater->nama_lengkap ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-{{ $activity->is_approved ? 'success' : 'warning' }}">
                                    {{ $activity->is_approved ? 'Disetujui' : 'Menunggu' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <div class="border p-2 rounded bg-light">
                            {!! nl2br(e($activity->deskripsi)) !!}
                        </div>
                    </div>

                    @if($activity->path_foto)
                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <div>
                            <img src="{{ Storage::url($activity->path_foto) }}" alt="Foto Aktivitas"
                                 class="img-fluid rounded" style="max-height: 200px;">
                            <a href="{{ Storage::url($activity->path_foto) }}" target="_blank"
                               class="btn btn-sm btn-info ms-2">
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($activity->path_file)
                    <div class="mb-3">
                        <label class="form-label">Dokumen Pendukung</label>
                        <div>
                            @if($activity->path_file)
                            <a href="{{ asset('storage/'.$activity->path_file) }}" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-file-earmark"></i> Lihat Dokumen
                            </a>
                            @endif

                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-end gap-2">
                <a href="{{ route('keselamatan.edit', ['activity' => $activity->id]) }}" class="btn btn-warning">
                <form action="{{ route('keselamatan.destroy', ['activity' => $activity->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </form>
                <button class="btn btn-{{ $activity->is_approved ? 'warning' : 'success' }}"
                        onclick="toggleApproval({{ $activity->id }})">
                    <i class="bi bi-{{ $activity->is_approved ? 'x-circle' : 'check-circle' }}"></i>
                    {{ $activity->is_approved ? 'Batalkan Persetujuan' : 'Setujui' }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleApproval(id) {
    fetch(`{{ url('keselamatan/activity') }}/${id}/toggle-approval`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal mengubah status persetujuan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}
</script>
@endpush
