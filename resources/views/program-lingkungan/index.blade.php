@extends('layout.index')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Program Lingkungan Hidup</h2>
        <div>
            <a href="{{ route('program-lingkungan.dashboard') }}" class="btn btn-secondary mr-2">Dashboard</a>
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Tambah Baru
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('program-lingkungan.create', 'krida') }}">Krida Area</a>
                    <a class="dropdown-item" href="{{ route('program-lingkungan.create', 'pengelolaan') }}">Pengelolaan Lingkungan</a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis Kegiatan</th>
                            <th>Lokasi</th>
                            <th>Pelaksana</th>
                            <th>Keterangan Singkat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $index => $activity)
                        <tr>
                            <td>{{ $activities->firstItem() + $index }}</td>
                            <td>{{ $activity->tanggal_kegiatan->format('d/m/Y') }}</td>
                            <td>{{ $activity->jenis_kegiatan }}</td>
                            <td>{{ $activity->lokasi }}</td>
                            <td>{{ $activity->pelaksana }}</td>
                            <td>
                                @if($activity->jenis_kegiatan == 'Krida Area Office/Workshop')
                                    {{ Str::limit($activity->deskripsi, 50) }}
                                @else
                                    Temuan: {{ Str::limit($activity->detail_temuan, 25) }}
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('program-lingkungan.show', $activity->id) }}" class="btn btn-sm btn-info">Detail</a>
                                <a href="{{ route('program-lingkungan.edit', $activity->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('program-lingkungan.destroy', $activity->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
