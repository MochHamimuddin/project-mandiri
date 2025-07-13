@extends('layout.index')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Formulir Edit SIB</h4>
            <small class="d-block mt-1">ID SIB: {{ $sib->id }}</small>
        </div>
        <div class="card-body">
            <form action="{{ route('data-sib.update', $sib->id) }}" method="POST" enctype="multipart/form-data" id="sibForm">
                @csrf
                @method('PUT')

                <!-- Status Indicator -->
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="fas fa-info-circle me-3 fs-4"></i>
                    <div>
                        <strong>Status Pengajuan:</strong>
                        <span class="badge bg-{{ $sib->status === 'Disetujui' ? 'success' : ($sib->status === 'Ditolak' ? 'danger' : 'warning') }}">
                            {{ $sib->status ?? 'Menunggu Persetujuan' }}
                        </span>
                        @if($sib->status === 'Ditolak' && $sib->alasan_penolakan)
                            <div class="mt-1"><strong>Alasan Penolakan:</strong> {{ $sib->alasan_penolakan }}</div>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Informasi Pemohon</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                                           id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $sib->nama_lengkap) }}" required>
                                    @error('nama_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="nrp" class="form-label">NRP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nrp') is-invalid @enderror"
                                           id="nrp" name="nrp" value="{{ old('nrp', $sib->nrp) }}" required>
                                    @error('nrp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="departemen" class="form-label">Departemen <span class="text-danger">*</span></label>
                                    <select class="form-select @error('departemen') is-invalid @enderror"
                                            id="departemen" name="departemen" required>
                                        <option value="">Pilih Departemen</option>
                                        @foreach($departemenOptions as $value => $label)
                                            <option value="{{ $value }}" @selected(old('departemen', $sib->departemen) == $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('departemen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Informasi Pekerjaan</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="perihal" class="form-label">Perihal <span class="text-danger">*</span></label>
                                    <select class="form-select @error('perihal') is-invalid @enderror"
                                            id="perihal" name="perihal" required>
                                        <option value="">Pilih Perihal</option>
                                        @foreach($perihalOptions as $value => $label)
                                            <option value="{{ $value }}" @selected(old('perihal', $sib->perihal) == $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('perihal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                    <select class="form-select @error('lokasi') is-invalid @enderror"
                                            id="lokasi" name="lokasi" required>
                                        <option value="">Pilih Lokasi</option>
                                        @foreach($lokasiOptions as $value => $label)
                                            <option value="{{ $value }}" @selected(old('lokasi', $sib->lokasi) == $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('lokasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="jenis_pekerjaan" class="form-label">Jenis Pekerjaan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('jenis_pekerjaan') is-invalid @enderror"
                                            id="jenis_pekerjaan" name="jenis_pekerjaan" required>
                                        <option value="">Pilih Jenis Pekerjaan</option>
                                        @foreach($jenisPekerjaanOptions as $value => $label)
                                            <option value="{{ $value }}" @selected(old('jenis_pekerjaan', $sib->jenis_pekerjaan) == $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis_pekerjaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Periode Pekerjaan</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                           id="tanggal_mulai" name="tanggal_mulai"
                                           value="{{ old('tanggal_mulai', $sib->tanggal_mulai->format('Y-m-d')) }}"
                                           min="{{ now()->format('Y-m-d') }}" required>
                                    @error('tanggal_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_akhir') is-invalid @enderror"
                                           id="tanggal_akhir" name="tanggal_akhir"
                                           value="{{ old('tanggal_akhir', $sib->tanggal_akhir->format('Y-m-d')) }}"
                                           min="{{ now()->addDay()->format('Y-m-d') }}" required>
                                    @error('tanggal_akhir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Ketentuan Pengajuan</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Pengajuan Baru (H-7) <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pengajuan_baru_h7" id="pengajuan_baru_h7_ya"
                                               value="Ya" @checked(old('pengajuan_baru_h7', $sib->pengajuan_baru_h7) == 'Ya') required>
                                        <label class="form-check-label" for="pengajuan_baru_h7_ya">Ya</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pengajuan_baru_h7" id="pengajuan_baru_h7_tidak"
                                               value="Tidak" @checked(old('pengajuan_baru_h7', $sib->pengajuan_baru_h7) == 'Tidak')>
                                        <label class="form-check-label" for="pengajuan_baru_h7_tidak">Tidak</label>
                                    </div>
                                    @error('pengajuan_baru_h7')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Perpanjangan (H-2) <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="perpanjangan_h2" id="perpanjangan_h2_ya"
                                               value="Ya" @checked(old('perpanjangan_h2', $sib->perpanjangan_h2) == 'Ya') required>
                                        <label class="form-check-label" for="perpanjangan_h2_ya">Ya</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="perpanjangan_h2" id="perpanjangan_h2_tidak"
                                               value="Tidak" @checked(old('perpanjangan_h2', $sib->perpanjangan_h2) == 'Tidak')>
                                        <label class="form-check-label" for="perpanjangan_h2_tidak">Tidak</label>
                                    </div>
                                    @error('perpanjangan_h2')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Dokumen Pendukung</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Petunjuk Upload:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Format file: PDF (maksimal 10MB per file)</li>
                                <li>Kosongkan file jika tidak ingin mengubah dokumen yang sudah ada</li>
                                <li>Dokumen yang sudah diupload akan tetap tersimpan jika tidak diubah</li>
                            </ul>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="work_permit" class="form-label">Work Permit</label>
                                    <input type="file" class="form-control @error('work_permit') is-invalid @enderror"
                                           id="work_permit" name="work_permit" accept=".pdf">
                                    @error('work_permit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($sib->work_permit)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            <a href="{{ asset('storage/' . $sib->work_permit) }}" target="_blank" class="ms-2">
                                                <i class="fas fa-file-pdf me-1"></i>Lihat Dokumen
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDelete('work_permit')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="jsa" class="form-label">JSA (Maks 5 file)</label>
                                    <input type="file" class="form-control @error('jsa') is-invalid @enderror"
                                           id="jsa" name="jsa[]" accept=".pdf" multiple>
                                    @error('jsa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Pekerjaan remove & instal JSA terpisah</small>
                                    @if($sib->jsa)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            @foreach(json_decode($sib->jsa) as $file)
                                                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="ms-2">
                                                    <i class="fas fa-file-pdf me-1"></i>Dokumen {{ $loop->iteration }}
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDeleteFile('jsa', {{ $loop->index }})">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="ibpr" class="form-label">IBPR (Maks 5 file)</label>
                                    <input type="file" class="form-control @error('ibpr') is-invalid @enderror"
                                           id="ibpr" name="ibpr[]" accept=".pdf" multiple>
                                    @error('ibpr')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($sib->ibpr)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            @foreach(json_decode($sib->ibpr) as $file)
                                                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="ms-2">
                                                    <i class="fas fa-file-pdf me-1"></i>Dokumen {{ $loop->iteration }}
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDeleteFile('ibpr', {{ $loop->index }})">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="emergency_preparedness" class="form-label">Emergency Preparedness</label>
                                    <input type="file" class="form-control @error('emergency_preparedness') is-invalid @enderror"
                                           id="emergency_preparedness" name="emergency_preparedness" accept=".pdf">
                                    @error('emergency_preparedness')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($sib->emergency_preparedness)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            <a href="{{ asset('storage/' . $sib->emergency_preparedness) }}" target="_blank" class="ms-2">
                                                <i class="fas fa-file-pdf me-1"></i>Lihat Dokumen
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDelete('emergency_preparedness')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="emergency_escape_plan" class="form-label">Emergency Escape Plan</label>
                                    <input type="file" class="form-control @error('emergency_escape_plan') is-invalid @enderror"
                                           id="emergency_escape_plan" name="emergency_escape_plan" accept=".pdf">
                                    @error('emergency_escape_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($sib->emergency_escape_plan)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            <a href="{{ asset('storage/' . $sib->emergency_escape_plan) }}" target="_blank" class="ms-2">
                                                <i class="fas fa-file-pdf me-1"></i>Lihat Dokumen
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDelete('emergency_escape_plan')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3" id="staggling_plan_container">
                                    <label for="staggling_plan" class="form-label">Staggling Plan</label>
                                    <input type="file" class="form-control @error('staggling_plan') is-invalid @enderror"
                                           id="staggling_plan" name="staggling_plan" accept=".pdf">
                                    @error('staggling_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Tidak untuk pekerjaan tertentu (lihat ketentuan)</small>
                                    @if($sib->staggling_plan)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            <a href="{{ asset('storage/' . $sib->staggling_plan) }}" target="_blank" class="ms-2">
                                                <i class="fas fa-file-pdf me-1"></i>Lihat Dokumen
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDelete('staggling_plan')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="history_training" class="form-label">History Training</label>
                                    <input type="file" class="form-control @error('history_training') is-invalid @enderror"
                                           id="history_training" name="history_training" accept=".pdf">
                                    @error('history_training')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($sib->history_training)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            <a href="{{ asset('storage/' . $sib->history_training) }}" target="_blank" class="ms-2">
                                                <i class="fas fa-file-pdf me-1"></i>Lihat Dokumen
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDelete('history_training')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3" id="kajian_geotek_container">
                                    <label for="kajian_geotek" class="form-label">Kajian Geotek</label>
                                    <input type="file" class="form-control @error('kajian_geotek') is-invalid @enderror"
                                           id="kajian_geotek" name="kajian_geotek" accept=".pdf">
                                    @error('kajian_geotek')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Hanya untuk pekerjaan tertentu</small>
                                    @if($sib->kajian_geotek)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            <a href="{{ asset('storage/' . $sib->kajian_geotek) }}" target="_blank" class="ms-2">
                                                <i class="fas fa-file-pdf me-1"></i>Lihat Dokumen
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDelete('kajian_geotek')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="form_fpp" class="form-label">Form FPP</label>
                                    <input type="file" class="form-control @error('form_fpp') is-invalid @enderror"
                                           id="form_fpp" name="form_fpp" accept=".pdf">
                                    @error('form_fpp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($sib->form_fpp)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            <a href="{{ asset('storage/' . $sib->form_fpp) }}" target="_blank" class="ms-2">
                                                <i class="fas fa-file-pdf me-1"></i>Lihat Dokumen
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDelete('form_fpp')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="form_observasi_berjenjang" class="form-label">Form Observasi Berjenjang</label>
                                    <input type="file" class="form-control @error('form_observasi_berjenjang') is-invalid @enderror"
                                           id="form_observasi_berjenjang" name="form_observasi_berjenjang" accept=".pdf">
                                    @error('form_observasi_berjenjang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($sib->form_observasi_berjenjang)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            <a href="{{ asset('storage/' . $sib->form_observasi_berjenjang) }}" target="_blank" class="ms-2">
                                                <i class="fas fa-file-pdf me-1"></i>Lihat Dokumen
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDelete('form_observasi_berjenjang')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3" id="form_p2h_container">
                                    <label for="form_p2h_unit_lifting" class="form-label">Form P2H Unit Lifting</label>
                                    <input type="file" class="form-control @error('form_p2h_unit_lifting') is-invalid @enderror"
                                           id="form_p2h_unit_lifting" name="form_p2h_unit_lifting" accept=".pdf">
                                    @error('form_p2h_unit_lifting')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Khusus untuk aktifitas lifting</small>
                                    @if($sib->form_p2h_unit_lifting)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            <a href="{{ asset('storage/' . $sib->form_p2h_unit_lifting) }}" target="_blank" class="ms-2">
                                                <i class="fas fa-file-pdf me-1"></i>Lihat Dokumen
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDelete('form_p2h_unit_lifting')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3" id="form_inspeksi_container">
                                    <label for="form_inspeksi_tools" class="form-label">Form Inspeksi Tools</label>
                                    <input type="file" class="form-control @error('form_inspeksi_tools') is-invalid @enderror"
                                           id="form_inspeksi_tools" name="form_inspeksi_tools" accept=".pdf">
                                    @error('form_inspeksi_tools')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Khusus untuk aktifitas lifting</small>
                                    @if($sib->form_inspeksi_tools)
                                        <div class="mt-2">
                                            <span class="badge bg-success">File Terupload</span>
                                            <a href="{{ asset('storage/' . $sib->form_inspeksi_tools) }}" target="_blank" class="ms-2">
                                                <i class="fas fa-file-pdf me-1"></i>Lihat Dokumen
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="confirmDelete('form_inspeksi_tools')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('data-sib.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <div>
                        <button type="button" class="btn btn-outline-danger me-2" onclick="resetForm()">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form elements
        const jenisPekerjaanSelect = document.getElementById('jenis_pekerjaan');
        const stagglingPlanContainer = document.getElementById('staggling_plan_container');
        const kajianGeotekContainer = document.getElementById('kajian_geotek_container');
        const p2hContainer = document.getElementById('form_p2h_container');
        const inspeksiContainer = document.getElementById('form_inspeksi_container');

        // Define job types with special requirements
        const noStagglingPlanJobs = [
            'Bekerja di Ketinggian >1.8 meter',
            'Bekerja di Dekat Air',
            'Bekerja Kelistrikan >380 V',
            'Pelepasan dan Pemasangan Tyre OHT di Jalan Tambang',
            'Maintenance Conveyor',
            'Penggalian/Gangguan di Sekitar Bangunan'
        ];

        const geotekRequiredJobs = [
            'Dumping & Loading HRA',
            'Bekerja di Dekat/Bawah Tebing Rawan Longsor FK<1.3',
            'Pelepasan dan Pemasangan Tyre OHT di Jalan Tambang',
            'Aktifitas Land Clearing',
            'Aktifitas Pengelasan Bahan Mudah Terbakar'
        ];

        const liftingJob = 'Pengangkatan/Lifting';

        // Toggle field requirements based on selected job type
        function toggleFieldRequirements() {
            const selectedJob = jenisPekerjaanSelect.value;

            // Staggling plan
            if (noStagglingPlanJobs.includes(selectedJob)) {
                stagglingPlanContainer.style.display = 'none';
                document.getElementById('staggling_plan').required = false;
            } else {
                stagglingPlanContainer.style.display = 'block';
                document.getElementById('staggling_plan').required = true;
            }

            // Kajian geotek
            if (geotekRequiredJobs.includes(selectedJob)) {
                kajianGeotekContainer.style.display = 'block';
                document.getElementById('kajian_geotek').required = true;
            } else {
                kajianGeotekContainer.style.display = 'none';
                document.getElementById('kajian_geotek').required = false;
            }

            // Lifting forms
            if (selectedJob === liftingJob) {
                p2hContainer.style.display = 'block';
                inspeksiContainer.style.display = 'block';
                document.getElementById('form_p2h_unit_lifting').required = true;
                document.getElementById('form_inspeksi_tools').required = true;
            } else {
                p2hContainer.style.display = 'none';
                inspeksiContainer.style.display = 'none';
                document.getElementById('form_p2h_unit_lifting').required = false;
                document.getElementById('form_inspeksi_tools').required = false;
            }
        }

        // Initialize form
        toggleFieldRequirements();

        // Event listeners
        jenisPekerjaanSelect.addEventListener('change', toggleFieldRequirements);

        // Date validation
        const tanggalMulai = document.getElementById('tanggal_mulai');
        const tanggalAkhir = document.getElementById('tanggal_akhir');

        tanggalMulai.addEventListener('change', function() {
            if (tanggalAkhir.value && new Date(tanggalAkhir.value) < new Date(this.value)) {
                alert('Tanggal akhir harus setelah tanggal mulai');
                this.value = '';
            }
            // Set minimum end date to one day after start date
            if (this.value) {
                const minEndDate = new Date(this.value);
                minEndDate.setDate(minEndDate.getDate() + 1);
                tanggalAkhir.min = minEndDate.toISOString().split('T')[0];
            }
        });

        tanggalAkhir.addEventListener('change', function() {
            if (tanggalMulai.value && new Date(this.value) < new Date(tanggalMulai.value)) {
                alert('Tanggal akhir harus setelah tanggal mulai');
                this.value = '';
            }
        });

        // Perihal validation
        const perihalSelect = document.getElementById('perihal');
        const pengajuanBaruYa = document.getElementById('pengajuan_baru_h7_ya');
        const pengajuanBaruTidak = document.getElementById('pengajuan_baru_h7_tidak');
        const perpanjanganYa = document.getElementById('perpanjangan_h2_ya');
        const perpanjanganTidak = document.getElementById('perpanjangan_h2_tidak');

        perihalSelect.addEventListener('change', function() {
            if (this.value === 'Pengajuan SIB Baru') {
                pengajuanBaruYa.checked = true;
                perpanjanganTidak.checked = true;
            } else if (this.value === 'Perpanjangan SIB') {
                pengajuanBaruTidak.checked = true;
                perpanjanganYa.checked = true;
            }
        });

        // File size validation
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.files[0] && this.files[0].size > 10 * 1024 * 1024) { // 10MB
                    alert('Ukuran file melebihi 10MB');
                    this.value = '';
                }
            });
        });
    });

    // Confirm file deletion
    function confirmDelete(fieldName) {
        if (confirm('Apakah Anda yakin ingin menghapus file ini?')) {
            // Create hidden input to mark file for deletion
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = `delete_${fieldName}`;
            deleteInput.value = '1';
            document.getElementById('sibForm').appendChild(deleteInput);

            // Hide the file display
            const fileDisplay = document.querySelector(`[onclick="confirmDelete('${fieldName}')]`).parentNode;
            fileDisplay.style.display = 'none';
        }
    }

    // Confirm deletion of specific file in array
    function confirmDeleteFile(fieldName, index) {
        if (confirm('Apakah Anda yakin ingin menghapus file ini?')) {
            // Create hidden input to mark file for deletion
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = `delete_${fieldName}[]`;
            deleteInput.value = index;
            document.getElementById('sibForm').appendChild(deleteInput);

            // Hide the specific file display
            const fileDisplay = document.querySelector(`[onclick="confirmDeleteFile('${fieldName}', ${index})"]`).parentNode;
            fileDisplay.style.display = 'none';
        }
    }

    // Reset form
    function resetForm() {
        if (confirm('Apakah Anda yakin ingin mengembalikan semua perubahan?')) {
            document.getElementById('sibForm').reset();
            // Reinitialize field requirements
            document.dispatchEvent(new Event('DOMContentLoaded'));
        }
    }
</script>
@endpush
@endsection
