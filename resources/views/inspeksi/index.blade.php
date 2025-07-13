@extends('layout.index')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Daftar Inspeksi Kendaraan</h4>
        <div class="card-header-action">
            <a href="{{ route('inspeksi.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Baru
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('inspeksi.index') }}" method="GET" class="form-inline">
                <div class="form-group mr-2">
                    <select class="form-control" name="jenis">
                        <option value="">Semua Jenis</option>
                        <option value="komisioning" {{ request('jenis') == 'komisioning' ? 'selected' : '' }}>Komisioning</option>
                        <option value="perawatan" {{ request('jenis') == 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                        <option value="evaluasi_kecepatan" {{ request('jenis') == 'evaluasi_kecepatan' ? 'selected' : '' }}>Evaluasi Kecepatan</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                <a href="{{ route('inspeksi.index') }}" class="btn btn-secondary">Reset</a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Jenis Inspeksi</th>
                        <th>Deskripsi</th>
                        <th>Pengawas</th>
                        <th>Mitra</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inspeksi as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($inspeksi->currentPage() - 1) * $inspeksi->perPage() }}</td>
                        <td>{{ $item->tanggal_inspeksi->format('d/m/Y') }}</td>
                        <td>
                            @if($item->jenis_inspeksi == 'komisioning')
                                <span class="badge badge-primary">Komisioning</span>
                            @elseif($item->jenis_inspeksi == 'perawatan')
                                <span class="badge badge-warning">Perawatan</span>
                            @else
                                <span class="badge badge-info">Evaluasi Kecepatan</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($item->deskripsi, 50) }}</td>
                        <td>{{ $item->pengawas->nama_lengkap ?? 'N/A' }}</td>
                        <td>{{ $item->mitra->nama_perusahaan ?? 'N/A' }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('inspeksi.show', $item->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inspeksi.edit', $item->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inspeksi.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $inspeksi->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
