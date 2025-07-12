@extends('layout.index')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">{{ $title }}</h2>
        <div>
            <a href="{{ route('keselamatan.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('keselamatan.create', ['type' => $type]) }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Tambah Baru
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Tanggal</th>
                            <th>Pengawas</th>
                            <th>Mitra</th>
                            <th width="12%">Status</th>
                            <th width="18%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td>{{ ($activities->currentPage() - 1) * $activities->perPage() + $loop->iteration }}</td>
                            <td>{{ $activity->created_at->translatedFormat('d M Y') }}</td>
                            <td>{{ $activity->pengawas->nama_lengkap ?? '-' }}</td>
                            <td>{{ $activity->mitra->nama_perusahaan ?? '-' }}</td>
                            <td>
                                @if($activity->is_approved)
                                <span class="badge bg-success">Disetujui</span>
                                @else
                                <span class="badge bg-warning text-dark">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('keselamatan.show', $activity->id) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('keselamatan.edit', $activity->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('keselamatan.destroy', $activity->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada data ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($activities->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $activities->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
