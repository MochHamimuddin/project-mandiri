@extends('layout.index')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Development Manpower</h2>
        <a href="{{ route('development-manpower.dashboard') }}" class="btn btn-info">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('development-manpower.index') }}" method="GET" class="form-inline">
                <select name="kategori" class="form-control mr-2">
                    <option value="">Semua Kategori</option>
                    @foreach(App\Models\DevelopmentManpower::KATEGORI_AKTIVITAS as $kategori)
                        <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Tanggal</th>
                            <th>Pengawas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $activity)
                        <tr>
                            <td>{{ $loop->iteration + ($activities->currentPage() - 1) * $activities->perPage() }}</td>
                            <td>{{ $activity->kategori_aktivitas }}</td>
                            <td>{{ Str::limit($activity->deskripsi, 50) }}</td>
                            <td>{{ $activity->tanggal_aktivitas->format('d/m/Y') }}</td>
                            <td>{{ $activity->pengawas->nama_lengkap ?? '-' }}</td>
                            <td>
                                <a href="{{ route('development-manpower.show', $activity->id) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('development-manpower.edit', $activity->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('development-manpower.destroy', $activity->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $activities->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
