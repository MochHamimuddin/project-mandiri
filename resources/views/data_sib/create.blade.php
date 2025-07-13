@extends('layout.index')
@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-file-alt me-2"></i>Formulir Pengajuan SIB</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('data-sib.store') }}" method="POST" enctype="multipart/form-data" id="sibForm">
                @csrf

                <!-- Progress Steps -->
                <div class="steps mb-4">
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-label">Informasi Dasar</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-label">Detail Pekerjaan</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-label">Dokumen Pendukung</div>
                    </div>
                </div>

                <!-- Step 1: Basic Information -->
                <div class="step-content active" data-step="1">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-user-circle me-2"></i>Informasi Pemohon
                            </h5>
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                                       id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', Auth::user()->name) }}" required>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nrp" class="form-label">NRP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nrp') is-invalid @enderror"
                                       id="nrp" name="nrp" value="{{ old('nrp', Auth::user()->nrp) }}" required>
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
                                        <option value="{{ $value }}" @selected(old('departemen') == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('departemen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-briefcase me-2"></i>Informasi Pekerjaan
                            </h5>
                            <div class="mb-3">
                                <label for="perihal" class="form-label">Jenis Pengajuan <span class="text-danger">*</span></label>
                                <select class="form-select @error('perihal') is-invalid @enderror"
                                        id="perihal" name="perihal" required>
                                    <option value="">Pilih Jenis Pengajuan</option>
                                    @foreach($perihalOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('perihal') == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('perihal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="lokasi" class="form-label">Lokasi Pekerjaan <span class="text-danger">*</span></label>
                                <select class="form-select @error('lokasi') is-invalid @enderror"
                                        id="lokasi" name="lokasi" required>
                                    <option value="">Pilih Lokasi</option>
                                    @foreach($lokasiOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('lokasi') == $value)>{{ $label }}</option>
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
                                        <option value="{{ $value }}" @selected(old('jenis_pekerjaan') == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_pekerjaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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

                <!-- Step 2: Work Details -->
                <div class="step-content" data-step="2">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-calendar-alt me-2"></i>Periode Pekerjaan
                            </h5>
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                       id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimal 7 hari sebelum pekerjaan untuk pengajuan baru</small>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_akhir" class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_akhir') is-invalid @enderror"
                                       id="tanggal_akhir" name="tanggal_akhir" value="{{ old('tanggal_akhir') }}" required>
                                @error('tanggal_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi_pekerjaan" class="form-label">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('deskripsi_pekerjaan') is-invalid @enderror"
                                          id="deskripsi_pekerjaan" name="deskripsi_pekerjaan" rows="3" required>{{ old('deskripsi_pekerjaan') }}</textarea>
                                @error('deskripsi_pekerjaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-clock me-2"></i>Ketentuan Pengajuan
                            </h5>
                            <div class="mb-3">
                                <label class="form-label">Pengajuan Baru (H-7) <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pengajuan_baru_h7" id="pengajuan_baru_h7_ya"
                                           value="Ya" @checked(old('pengajuan_baru_h7') == 'Ya') required>
                                    <label class="form-check-label" for="pengajuan_baru_h7_ya">Ya</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pengajuan_baru_h7" id="pengajuan_baru_h7_tidak"
                                           value="Tidak" @checked(old('pengajuan_baru_h7') == 'Tidak')>
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
                                           value="Ya" @checked(old('perpanjangan_h2') == 'Ya') required>
                                    <label class="form-check-label" for="perpanjangan_h2_ya">Ya</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="perpanjangan_h2" id="perpanjangan_h2_tidak"
                                           value="Tidak" @checked(old('perpanjangan_h2') == 'Tidak')>
                                    <label class="form-check-label" for="perpanjangan_h2_tidak">Tidak</label>
                                </div>
                                @error('perpanjangan_h2')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Pastikan pengajuan sesuai dengan timeline yang ditentukan untuk menghindari penolakan.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-prev="1">
                            <i class="fas fa-arrow-left me-2"></i>Sebelumnya
                        </button>
                        <button type="button" class="btn btn-primary next-step" data-next="3">
                            Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Supporting Documents -->
                <div class="step-content" data-step="3">
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
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Dokumen Wajib</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="work_permit" class="form-label">Work Permit <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control @error('work_permit') is-invalid @enderror"
                                                       id="work_permit" name="work_permit" accept=".pdf" required>
                                                @error('work_permit')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: work_permits/</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="emergency_preparedness" class="form-label">Emergency Preparedness <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control @error('emergency_preparedness') is-invalid @enderror"
                                                       id="emergency_preparedness" name="emergency_preparedness" accept=".pdf" required>
                                                @error('emergency_preparedness')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: emergency_docs/</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="emergency_escape_plan" class="form-label">Emergency Escape Plan <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control @error('emergency_escape_plan') is-invalid @enderror"
                                                       id="emergency_escape_plan" name="emergency_escape_plan" accept=".pdf" required>
                                                @error('emergency_escape_plan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: emergency_docs/</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="history_training" class="form-label">History Training <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control @error('history_training') is-invalid @enderror"
                                                       id="history_training" name="history_training" accept=".pdf" required>
                                                @error('history_training')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: training_docs/</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Dokumen Tambahan</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="jsa" class="form-label">JSA (Job Safety Analysis)</label>
                                                <input type="file" class="form-control @error('jsa') is-invalid @enderror"
                                                       id="jsa" name="jsa[]" accept=".pdf" multiple>
                                                @error('jsa')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Maks. 5 file (disimpan di: jsas/)</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="ibpr" class="form-label">IBPR (Maks. 5 file)</label>
                                                <input type="file" class="form-control @error('ibpr') is-invalid @enderror"
                                                       id="ibpr" name="ibpr[]" accept=".pdf" multiple>
                                                @error('ibpr')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: ibprs/</small>
                                            </div>

                                            <div class="mb-3" id="staggling_plan_container">
                                                <label for="staggling_plan" class="form-label">Staggling Plan</label>
                                                <input type="file" class="form-control @error('staggling_plan') is-invalid @enderror"
                                                       id="staggling_plan" name="staggling_plan" accept=".pdf">
                                                @error('staggling_plan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: emergency_docs/</small>
                                            </div>

                                            <div class="mb-3" id="kajian_geotek_container">
                                                <label for="kajian_geotek" class="form-label">Kajian Geotek</label>
                                                <input type="file" class="form-control @error('kajian_geotek') is-invalid @enderror"
                                                       id="kajian_geotek" name="kajian_geotek" accept=".pdf">
                                                @error('kajian_geotek')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: work_permits/</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Formulir Wajib</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="form_fpp" class="form-label">Form FPP <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control @error('form_fpp') is-invalid @enderror"
                                                       id="form_fpp" name="form_fpp" accept=".pdf" required>
                                                @error('form_fpp')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: forms/</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="form_observasi_berjenjang" class="form-label">Form Observasi Berjenjang <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control @error('form_observasi_berjenjang') is-invalid @enderror"
                                                       id="form_observasi_berjenjang" name="form_observasi_berjenjang" accept=".pdf" required>
                                                @error('form_observasi_berjenjang')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: forms/</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Formulir Khusus</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3" id="form_p2h_container">
                                                <label for="form_p2h_unit_lifting" class="form-label">Form P2H Unit Lifting</label>
                                                <input type="file" class="form-control @error('form_p2h_unit_lifting') is-invalid @enderror"
                                                       id="form_p2h_unit_lifting" name="form_p2h_unit_lifting" accept=".pdf">
                                                @error('form_p2h_unit_lifting')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: forms/</small>
                                            </div>

                                            <div class="mb-3" id="form_inspeksi_container">
                                                <label for="form_inspeksi_tools" class="form-label">Form Inspeksi Tools</label>
                                                <input type="file" class="form-control @error('form_inspeksi_tools') is-invalid @enderror"
                                                       id="form_inspeksi_tools" name="form_inspeksi_tools" accept=".pdf">
                                                @error('form_inspeksi_tools')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Disimpan di: forms/</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-prev="2">
                            <i class="fas fa-arrow-left me-2"></i>Sebelumnya
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle me-2"></i>Submit Pengajuan
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
