@extends('layout.index')
@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-file-alt me-2"></i>Insert Data Dokumentasi Pelaksanaan</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('list-dokumen.store') }}" method="POST" enctype="multipart/form-data" id="sibForm">
                @csrf

                <!-- Progress Steps -->
                <div class="steps mb-4">
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-label">Informasi Dasar</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-label">Dokumen Pendukung</div>
                    </div>
                </div>

                <!-- Step 1: Basic Information -->
                <div class="step-content active" data-step="1">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-user-circle me-2"></i>Informasi User
                            </h5>
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Informasi User <span class="text-danger">*</span></label>
                                <select class="form-select @error('nama_lengkap') is-invalid @enderror"
                                        id="nama_lengkap" name="nama_lengkap" required>
                                    <option value="">Pilih User</option>
                                    @foreach($ListUser as $value)
                                        <option value="{{ $value->id }}" @selected(old('nama_lengkap') == $value)>{{ $value->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-briefcase me-2"></i>Informasi Pekerjaan
                            </h5>
                            <div class="mb-3">
                                <label for="jenis_pekerjaan" class="form-label">Jenis Pekerjaan <span class="text-danger">*</span></label>
                                <select class="form-select @error('jenis_pekerjaan') is-invalid @enderror"
                                        id="jenis_pekerjaan" name="jenis_pekerjaan" required>
                                    <option value="">Pilih Jenis Pekerjaan</option>
                                    @foreach($ListPekerjaan as $value => $label)
                                        <option value="{{ $label }}" @selected(old('jenis_pekerjaan') == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_pekerjaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-briefcase me-2"></i>Informasi Tenggat Waktu
                            </h5>
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                       id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Setelah menambahkan file, otomatis semua deadline berkas menjadi 7 hari</small>
                            </div>
                        </div>
                    </div>


                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary disabled">
                            <i class="fas fa-arrow-left me-2"></i>Sebelumnya
                        </button>
                        <button type="button" class="btn btn-primary next-step" data-next="2">
                            Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Supporting Documents -->
                <div class="step-content" data-step="2">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-file-upload me-2"></i>Dokumen Pendukung
                            </h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Unggah semua dokumen dalam format PDF (maks. 10MB per file). File yang diunggah akan disimpan di folder yang sesuai.
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <!-- <div class="card-header bg-light">
                                            <h6 class="mb-0">Dokumen Wajib</h6>
                                        </div> -->
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="file_apd" class="form-label">Foto Kelengkapan APD</label>
                                                <input type="file" class="form-control @error('file_apd') is-invalid @enderror"
                                                       id="file_apd" name="file_apd" accept=".pdf">
                                                @error('file_apd')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: kelengkapan_apd/</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="file_p5m_jsa" class="form-label">Pelaksanaan P5M dan Sosialisasi JSA </label>
                                                <input type="file" class="form-control @error('file_p5m_jsa') is-invalid @enderror"
                                                       id="file_p5m_jsa" name="file_p5m_jsa" accept=".pdf">
                                                @error('file_p5m_jsa')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: pelaksanaa_p5m_jsa/</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="file_p2h" class="form-label">Pelaksanaan P2H pada unit </label>
                                                <input type="file" class="form-control @error('file_p2h') is-invalid @enderror"
                                                       id="file_p2h" name="file_p2h" accept=".pdf">
                                                @error('file_p2h')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: file_p2h/</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="file_inspeksi_fpp" class="form-label">Pelaksanaan Inspeksi FPP</label>
                                                <input type="file" class="form-control @error('file_inspeksi_fpp') is-invalid @enderror"
                                                       id="file_inspeksi_fpp" name="file_inspeksi_fpp" accept=".pdf" >
                                                @error('file_inspeksi_fpp')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: file_inspeksi_fpp/</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <!-- <div class="card-header bg-light">
                                            <h6 class="mb-0">Dokumen Tambahan</h6>
                                        </div> -->
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="file_form_fpp" class="form-label">Form FPP</label>
                                                <input type="file" class="form-control @error('file_form_fpp') is-invalid @enderror"
                                                       id="file_form_fpp" name="file_form_fpp" accept=".pdf" >
                                                @error('file_form_fpp')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">disimpan di: file_form_fpp/</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="file_kegiatan_berlangsung" class="form-label">Foto kegiatan berlangsung</label>
                                                <input type="file" class="form-control @error('file_kegiatan_berlangsung') is-invalid @enderror"
                                                       id="file_kegiatan_berlangsung" name="file_kegiatan_berlangsung" accept=".pdf">
                                                @error('file_kegiatan_berlangsung')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">disimpan di: file_kegiatan_berlangsung/</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="file_absensi_p5m" class="form-label">Absensi P5M</label>
                                                <input type="file" class="form-control @error('file_absensi_p5m') is-invalid @enderror"
                                                       id="file_absensi_p5m" name="file_absensi_p5m" accept=".pdf">
                                                @error('file_absensi_p5m')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">disimpan di: file_absensi_p5m/</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="file_inspeksi_observasi" class="form-label">Form Inspeksi & Observasi berjenjang</label>
                                                <input type="file" class="form-control @error('file_inspeksi_observasi') is-invalid @enderror"
                                                       id="file_inspeksi_observasi" name="file_inspeksi_observasi" accept=".pdf">
                                                @error('file_inspeksi_observasi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">disimpan di: file_inspeksi_observasi/</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-prev="1">
                            <i class="fas fa-arrow-left me-2"></i>Sebelumnya
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle me-2"></i>Submit Dokumen
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Step Progress Bar */
    .steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
    }

    .steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #dee2e6;
        z-index: 1;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
        flex: 1;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #dee2e6;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 0.5rem;
        border: 3px solid #dee2e6;
    }

    .step-label {
        color: #6c757d;
        font-weight: 500;
        text-align: center;
    }

    .step.active .step-circle {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    .step.active .step-label {
        color: #0d6efd;
        font-weight: bold;
    }

    /* Step Content */
    .step-content {
        display: none;
    }

    .step-content.active {
        display: block;
    }

    /* File input styling */
    .form-control-file {
        border: 1px dashed #ced4da;
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
    }

    .form-control-file:hover {
        border-color: #86b7fe;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Step navigation
        const steps = document.querySelectorAll('.step');
        const stepContents = document.querySelectorAll('.step-content');

        document.querySelectorAll('.next-step').forEach(button => {
            button.addEventListener('click', function() {
                const currentStep = document.querySelector('.step-content.active').dataset.step;
                const nextStep = this.dataset.next;

                // Validate current step before proceeding
                if (validateStep(currentStep)) {
                    // Hide current step
                    document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.remove('active');
                    document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');

                    // Show next step
                    document.querySelector(`.step-content[data-step="${nextStep}"]`).classList.add('active');
                    document.querySelector(`.step[data-step="${nextStep}"]`).classList.add('active');

                    // Scroll to top of form
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });

        document.querySelectorAll('.prev-step').forEach(button => {
            button.addEventListener('click', function() {
                const currentStep = document.querySelector('.step-content.active').dataset.step;
                const prevStep = this.dataset.prev;

                // Hide current step
                document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.remove('active');
                document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');

                // Show previous step
                document.querySelector(`.step-content[data-step="${prevStep}"]`).classList.add('active');
                document.querySelector(`.step[data-step="${prevStep}"]`).classList.add('active');

                // Scroll to top of form
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        function validateStep(step) {
            let isValid = true;
            const currentStepForm = document.querySelector(`.step-content[data-step="${step}"]`);

            // Check all required fields in current step
            const requiredFields = currentStepForm.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value) {
                    field.classList.add('is-invalid');
                    isValid = false;

                    // Add error message if not exists
                    if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'Field ini wajib diisi';
                        field.parentNode.insertBefore(errorDiv, field.nextSibling);
                    }
                }
            });

            // Special validation for dates
            if (step === '2') {
                const tanggalMulai = document.getElementById('tanggal_mulai');
                const tanggalAkhir = document.getElementById('tanggal_akhir');

                if (tanggalMulai.value && tanggalAkhir.value) {
                    const startDate = new Date(tanggalMulai.value);
                    const endDate = new Date(tanggalAkhir.value);

                    if (endDate < startDate) {
                        tanggalAkhir.classList.add('is-invalid');
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'Tanggal akhir harus setelah tanggal mulai';
                        tanggalAkhir.parentNode.insertBefore(errorDiv, tanggalAkhir.nextSibling);
                        isValid = false;
                    }
                }
            }

            if (!isValid) {
                // Scroll to first invalid field
                const firstInvalid = currentStepForm.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            return isValid;
        }

        // Job type specific fields
        const jenisPekerjaanSelect = document.getElementById('jenis_pekerjaan');
        const stagglingPlanContainer = document.getElementById('staggling_plan_container');
        const kajianGeotekContainer = document.getElementById('kajian_geotek_container');
        const p2hContainer = document.getElementById('form_p2h_container');
        const inspeksiContainer = document.getElementById('form_inspeksi_container');

        // Jobs that don't require staggling plan
        const noStagglingPlanJobs = [
            'Bekerja di Ketinggian >1.8 meter',
            'Bekerja di Dekat Air',
            'Bekerja Kelistrikan >380 V',
            'Pelepasan dan Pemasangan Tyre OHT di Jalan Tambang',
            'Maintenance Conveyor',
            'Penggalian/Gangguan di Sekitar Bangunan'
        ];

        // Jobs that require geotechnical study
        const geotekRequiredJobs = [
            'Dumping & Loading HRA',
            'Bekerja di Dekat/Bawah Tebing Rawan Longsor FK<1.3',
            'Pelepasan dan Pemasangan Tyre OHT di Jalan Tambang',
            'Aktifitas Land Clearing',
            'Aktifitas Pengelasan Bahan Mudah Terbakar'
        ];

        // Lifting job
        const liftingJob = 'Pengangkatan/Lifting';

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

            // Geotechnical study
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

        // Initial setup
        toggleFieldRequirements();

        // Event listener for job type change
        jenisPekerjaanSelect.addEventListener('change', toggleFieldRequirements);

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
        document.getElementById('sibForm').addEventListener('submit', function(e) {
            let isValid = true;
            const fileInputs = this.querySelectorAll('input[type="file"]');

            fileInputs.forEach(input => {
                if (input.files && input.files.length > 0) {
                    for (let i = 0; i < input.files.length; i++) {
                        if (input.files[i].size > 10 * 1024 * 1024) { // 10MB
                            input.classList.add('is-invalid');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback';
                            errorDiv.textContent = 'Ukuran file melebihi 10MB';
                            input.parentNode.insertBefore(errorDiv, input.nextSibling);
                            isValid = false;
                        }

                        if (!input.files[i].name.toLowerCase().endsWith('.pdf')) {
                            input.classList.add('is-invalid');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback';
                            errorDiv.textContent = 'Hanya file PDF yang diperbolehkan';
                            input.parentNode.insertBefore(errorDiv, input.nextSibling);
                            isValid = false;
                        }
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Scroll to first invalid file input
                const firstInvalid = this.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
</script>
@endpush
@endsection
