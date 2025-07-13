@extends('layout.index')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h4 class="mb-0">Daftar Data SIB</h4>
            @if(in_array(Auth::user()->code_role, ['001', '002']))
                <a href="{{ route('data-sib.create') }}" class="btn btn-light">
                    <i class="fas fa-plus me-2"></i>Buat Baru
                </a>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NRP</th>
                            <th>Departemen</th>
                            <th>Jenis Pekerjaan</th>
                            <th>Periode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataSibs as $key => $data)
                        <tr>
                            <td>{{ $dataSibs->firstItem() + $key }}</td>
                            <td>{{ $data->nama_lengkap }}</td>
                            <td>{{ $data->nrp }}</td>
                            <td>{{ $data->departemen }}</td>
                            <td>{{ Str::limit($data->jenis_pekerjaan, 30) }}</td>
                            <td>
                                {{ $data->tanggal_mulai->format('d/m/Y') }} -
                                {{ $data->tanggal_akhir->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('data-sib.show', $data->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Auth::user()->code_role == '001')
                                        <form action="{{ route('data-sib.destroy', $data->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data ditemukan</td>
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
