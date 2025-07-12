@extends('layout.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2>{{ $title }}</h2>
        </div>
        <div class="col-md-6 text-end">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Cetak Laporan
            </button>
            <a href="{{ route('keselamatan.type.index', ['type' => $type]) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow printable">
        <div class="card-body">
            <div class="text-center mb-4 d-print-none">
                <h3>{{ $title }}</h3>
                <p>Periode: {{ now()->format('d F Y') }}</p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pengawas</th>
                            <th>Mitra</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $item->pengawas->nama_lengkap }}</td>
                            <td>{{ $item->mitra->nama_perusahaan }}</td>
                            <td>{{ $item->deskripsi }}</td>
                            <td>
                                <span class="badge bg-{{ $item->is_approved ? 'success' : 'warning' }}">
                                    {{ $item->is_approved ? 'Disetujui' : 'Menunggu' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row mt-4 d-print-none">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('keselamatan.type.report', ['type' => $type]) }}">
                        <div class="input-group mb-3">
                            <input type="date" class="form-control" name="start_date"
                                   value="{{ request('start_date') }}">
                            <span class="input-group-text">s/d</span>
                            <input type="date" class="form-control" name="end_date"
                                   value="{{ request('end_date') }}">
                            <button class="btn btn-outline-secondary" type="submit">Filter</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('keselamatan.type.report', ['type' => $type, 'export' => 'pdf']) }}"
                       class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </a>
                    <a href="{{ route('keselamatan.type.report', ['type' => $type, 'export' => 'excel']) }}"
                       class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .printable, .printable * {
            visibility: visible;
        }
        .printable {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .table {
            font-size: 12px;
        }
        .d-print-none {
            display: none !important;
        }
    }
</style>
@endpush
