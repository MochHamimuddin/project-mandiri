@extends('layout.index')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Fire Preventive Management</h2>
        <div>
            <a href="{{ route('fire-preventive.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Aktivitas</h5>
            <div>
                <a href="{{ route('fire-preventive.create', ['type' => 'Pencucian Unit']) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Pencucian Unit
                </a>
                <a href="{{ route('fire-preventive.create', ['type' => 'Inspeksi APAR']) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Inspeksi APAR
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jenis Aktivitas</th>
                            <th>Lokasi/Deskripsi</th>
                            <th>Pengawas</th>
                            <th>Tanggal</th>
                            <th>Dibuat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge {{ $item->activity_type === 'Pencucian Unit' ? 'bg-primary' : 'bg-success' }}">
                                    {{ $item->activity_type }}
                                </span>
                            </td>
                            <td>
                                @if($item->activity_type === 'Pencucian Unit')
                                    {{ Str::limit($item->description, 50) }}
                                @else
                                    {{ $item->inspection_location }}
                                @endif
                            </td>
                            <td>{{ $item->supervisor->nama_lengkap }}</td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                            <td>{{ $item->creator->nama_lengkap }}</td>
                            <td>
                                <div class="d-flex gap-2">
    {{-- Tombol Detail - Tampilkan untuk semua role --}}
    <a href="{{ route('fire-preventive.show', $item->id) }}" class="btn btn-sm btn-info" title="Detail">
        <i class="fas fa-eye"></i>
    </a>

    {{-- Tombol Edit dan Hapus - Hanya untuk role selain 002 --}}
    @if(auth()->user()->code_role !== '002')
        <a href="{{ route('fire-preventive.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <form action="{{ route('fire-preventive.destroy', $item->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin?')">
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
        </div>
    </div>
</div>
@endsection
