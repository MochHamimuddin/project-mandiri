@extends('layout.index')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Tambah Inspeksi Baru</h4>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <h5>Ada kesalahan dalam input data:</h5>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('inspeksi.store') }}" method="POST" enctype="multipart/form-data" id="inspeksiForm">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="jenis_inspeksi" class="form-label">Jenis Inspeksi <span class="text-danger">*</span></label>
                    <select class="form-select @error('jenis_inspeksi') is-invalid @enderror" id="jenis_inspeksi" name="jenis_inspeksi" required>
                        <option value="">Pilih Jenis Inspeksi</option>
                        <option value="komisioning" {{ old('jenis_inspeksi') == 'komisioning' ? 'selected' : '' }}>Komisioning</option>
                        <option value="perawatan" {{ old('jenis_inspeksi') == 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                        <option value="evaluasi_kecepatan" {{ old('jenis_inspeksi') == 'evaluasi_kecepatan' ? 'selected' : '' }}>Evaluasi Kecepatan</option>
                    </select>
                    @error('jenis_inspeksi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="tanggal_inspeksi" class="form-label">Tanggal Inspeksi <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal_inspeksi') is-invalid @enderror"
                           id="tanggal_inspeksi" name="tanggal_inspeksi"
                           value="{{ old('tanggal_inspeksi', date('Y-m-d')) }}" required>
                    @error('tanggal_inspeksi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                          id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
    <label for="pengawas_id" class="form-label">Pengawas</label>
    <select class="form-select @error('pengawas_id') is-invalid @enderror" id="pengawas_id" name="pengawas_id"
        @if(auth()->user()->code_role === '002') disabled @endif>
        <option value="">Pilih Pengawas</option>

        @if(auth()->user()->code_role === '001')
            {{-- Admin melihat semua pengawas --}}
            @foreach($users as $p)
                <option value="{{ $p->id }}" {{ old('pengawas_id', isset($current_user_id) ? $current_user_id : '') == $p->id ? 'selected' : '' }}>
                    {{ $p->nama_lengkap }}
                </option>
            @endforeach
        @elseif(auth()->user()->code_role === '002')
            {{-- User biasa hanya melihat dirinya sendiri --}}
            <option value="{{ auth()->user()->id }}" selected>
                {{ auth()->user()->nama_lengkap }}
            </option>
            {{-- Tambahkan input hidden untuk memastikan nilai terkirim --}}
            <input type="hidden" name="pengawas_id" value="{{ auth()->user()->id }}">
        @endif
    </select>

    @error('pengawas_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
                <div class="col-md-6">
                    <label for="mitra_id" class="form-label">Mitra</label>
                    <select class="form-select @error('mitra_id') is-invalid @enderror" id="mitra_id" name="mitra_id">
                        <option value="">Pilih Mitra</option>
                        @foreach($mitras as $m)
                        <option value="{{ $m->id }}" {{ old('mitra_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->nama_perusahaan }}
                        </option>
                        @endforeach
                    </select>
                    @error('mitra_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Komisioning Fields -->
            <div id="komisioning_fields" class="mb-3" style="display: {{ old('jenis_inspeksi') == 'komisioning' ? 'block' : 'none' }};">
                <label for="jenis_komisioning" class="form-label">Jenis Komisioning</label>
                <input type="text" class="form-control @error('jenis_komisioning') is-invalid @enderror"
                       id="jenis_komisioning" name="jenis_komisioning"
                       value="{{ old('jenis_komisioning') }}">
                @error('jenis_komisioning')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Perawatan Fields -->
            <div id="perawatan_fields" style="display: {{ old('jenis_inspeksi') == 'perawatan' ? 'block' : 'none' }};">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="jadwal_perawatan" class="form-label">Jadwal Perawatan</label>
                        <input type="date" class="form-control @error('jadwal_perawatan') is-invalid @enderror"
                               id="jadwal_perawatan" name="jadwal_perawatan"
                               value="{{ old('jadwal_perawatan') }}">
                        @error('jadwal_perawatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="pelaksana_perawatan" class="form-label">Pelaksana Perawatan</label>
                        <input type="text" class="form-control @error('pelaksana_perawatan') is-invalid @enderror"
                               id="pelaksana_perawatan" name="pelaksana_perawatan"
                               value="{{ old('pelaksana_perawatan') }}">
                        @error('pelaksana_perawatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Evaluasi Kecepatan Fields -->
            <div id="evaluasi_fields" style="display: {{ old('jenis_inspeksi') == 'evaluasi_kecepatan' ? 'block' : 'none' }};">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="hasil_observasi_kecepatan" class="form-label">Hasil Observasi Kecepatan</label>
                        <input type="text" class="form-control @error('hasil_observasi_kecepatan') is-invalid @enderror"
                               id="hasil_observasi_kecepatan" name="hasil_observasi_kecepatan"
                               value="{{ old('hasil_observasi_kecepatan') }}">
                        @error('hasil_observasi_kecepatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="satuan_kecepatan" class="form-label">Satuan Kecepatan</label>
                        <input type="text" class="form-control @error('satuan_kecepatan') is-invalid @enderror"
                               id="satuan_kecepatan" name="satuan_kecepatan"
                               value="{{ old('satuan_kecepatan', 'km/jam') }}"
                               placeholder="km/jam">
                        @error('satuan_kecepatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="foto" class="form-label">Foto Inspeksi <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('foto') is-invalid @enderror"
                           id="foto" name="foto" required>
                    <small class="text-muted">Format: jpeg, png, jpg (max: 2MB)</small>
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="dokumen" class="form-label">Dokumen Pendukung</label>
                    <input type="file" class="form-control @error('dokumen') is-invalid @enderror"
                           id="dokumen" name="dokumen">
                    <small class="text-muted">Format: pdf (max: 5MB)</small>
                    @error('dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('inspeksi.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jenisInspeksi = document.getElementById('jenis_inspeksi');

        // Handle jenis inspeksi change
        jenisInspeksi.addEventListener('change', function() {
            const jenis = this.value;

            // Hide all fields
            document.getElementById('komisioning_fields').style.display = 'none';
            document.getElementById('perawatan_fields').style.display = 'none';
            document.getElementById('evaluasi_fields').style.display = 'none';

            // Show relevant fields
            if (jenis === 'komisioning') {
                document.getElementById('komisioning_fields').style.display = 'block';
            } else if (jenis === 'perawatan') {
                document.getElementById('perawatan_fields').style.display = 'block';
            } else if (jenis === 'evaluasi_kecepatan') {
                document.getElementById('evaluasi_fields').style.display = 'block';
            }
        });

        // Initialize fields based on old input
        if ("{{ old('jenis_inspeksi') }}") {
            jenisInspeksi.dispatchEvent(new Event('change'));
        }

        // Form submission handler
        document.getElementById('inspeksiForm').addEventListener('submit', function() {
            // Show loading indicator
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
        });
    });
</script>
@endsection
