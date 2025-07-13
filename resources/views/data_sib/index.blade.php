@extends('layout.index')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white py-3">
            <h4 class="mb-0">Daftar Data SIB</h4>
            @if(in_array(Auth::user()->code_role, ['001', '002']))
                <a href="{{ route('data-sib.create') }}" class="btn btn-light">
                    <i class="fas fa-plus me-2"></i>Buat Baru
                </a>
            @endif
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">No</th>
                            <th>Nama Lengkap</th>
                            <th>NRP</th>
                            <th>Departemen</th>
                            <th>Jenis Pekerjaan</th>
                            <th>Periode</th>
                            <th class="pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataSibs as $key => $data)
                        <tr>
                            <td class="ps-3">{{ $dataSibs->firstItem() + $key }}</td>
                            <td>{{ $data->nama_lengkap }}</td>
                            <td>{{ $data->nrp }}</td>
                            <td>{{ $data->departemen }}</td>
                            <td>{{ Str::limit($data->jenis_pekerjaan, 30) }}</td>
                            <td>
                                {{ $data->tanggal_mulai->format('d/m/Y') }} -
                                {{ $data->tanggal_akhir->format('d/m/Y') }}
                            </td>
                            <td class="pe-3">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('data-sib.show', $data->id) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    @if(Auth::user()->code_role == '001')
                                        <form action="{{ route('data-sib.destroy', $data->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-3">Tidak ada data ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $dataSibs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection