@extends('layout.index')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Development Manpower</h2>
        <a href="{{ route('development-manpower.dashboard') }}" class="btn btn-info">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
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
                                <div class="d-flex gap-2">
    {{-- Detail button - visible to all roles --}}
    <a href="{{ route('development-manpower.show', $activity->id) }}" class="btn btn-sm btn-info" title="Detail">
        <i class="fas fa-eye"></i>
    </a>

    {{-- Edit and Delete buttons - only visible to non-002 roles --}}
    @if(auth()->user()->code_role !== '002')
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
    @endif
</div>
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
