@extends('layout.index')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Detail Inspeksi Kendaraan</h4>
        <div>
            <a href="{{ route('inspeksi.edit', $inspeksi->id) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('inspeksi.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Jenis Inspeksi</th>
                        <td>
                            @if($inspeksi->jenis_inspeksi == 'komisioning')
                                <span class="badge bg-success">{{ ucfirst($inspeksi->jenis_inspeksi) }}</span>
                            @elseif($inspeksi->jenis_inspeksi == 'perawatan')
                                <span class="badge bg-info">{{ ucfirst($inspeksi->jenis_inspeksi) }}</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ ucfirst($inspeksi->jenis_inspeksi) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Inspeksi</th>
                        <td>{{ $inspeksi->tanggal_inspeksi->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Pengawas</th>
                        <td>{{ $inspeksi->pengawas->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Mitra</th>
                        <td>{{ $inspeksi->mitra->nama_perusahaan ?? '-' }}</td>
                    </tr>

                    @if($inspeksi->jenis_inspeksi == 'komisioning')
                    <tr>
                        <th>Jenis Komisioning</th>
                        <td>{{ $inspeksi->jenis_komisioning }}</td>
                    </tr>
                    @elseif($inspeksi->jenis_inspeksi == 'perawatan')
                    <tr>
                        <th>Jadwal Perawatan</th>
                        <td>{{ $inspeksi->jadwal_perawatan->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Pelaksana Perawatan</th>
                        <td>{{ $inspeksi->pelaksana_perawatan }}</td>
                    </tr>
                    @else
                    <tr>
                        <th>Hasil Observasi Kecepatan</th>
                        <td>{{ $inspeksi->hasil_observasi_kecepatan }} {{ $inspeksi->satuan_kecepatan }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <div class="col-md-6">
                <div class="mb-4">
                    <h5>Foto Inspeksi</h5>
                    <img src="{{ $inspeksi->foto_url }}" alt="Foto Inspeksi" class="img-fluid rounded">
                </div>

                @if($inspeksi->path_dokumen)
                <div>
                    <h5>Dokumen Pendukung</h5>
                    <a href="{{ $inspeksi->dokumen_url }}" target="_blank" class="btn btn-primary">
                        <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <h5>Deskripsi Lengkap</h5>
            <p>{{ $inspeksi->deskripsi }}</p>
        </div>
    </div>
</div>
@endsection
