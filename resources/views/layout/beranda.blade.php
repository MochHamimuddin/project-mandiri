<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - KPP MINING</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('admin/img/kpp.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        body {
            background-color: #f8f9fa;
            background-image: linear-gradient(to bottom, #ffffff, #f1f5f9);
        }
        .selection-container {
            min-height: 100vh;
            padding: 2rem;
        }
        .btn-card {
            transition: all 0.2s ease;
            border-width: 2px;
            font-weight: 500;
        }
        .btn-card:hover {
            transform: scale(1.03);
        }
        .header-text {
            color: #2c3e50;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .lead-text {
            color: #4a5568;
        }
        .card {
            border-radius: 12px;
            overflow: hidden;
        }
        .card-body {
            padding: 2.5rem;
        }
    </style>
</head>
<body>
    <div class="container selection-container d-flex justify-content-center align-items-center">
        <div class="row w-100 justify-content-center">
            <div class="col-12 text-center mb-5">
                <img src="{{ asset('admin/img/kpp.png') }}" alt="Logo KPP" style="height: 80px; margin-bottom: 1rem;">
                <h1 class="display-5 fw-bold header-text">SELAMAT DATANG DI SISTEM KPP MINING</h1>
                <p class="lead lead-text">Silakan pilih menu yang tersedia di bawah ini</p>
            </div>

            @if(!request()->is('daftar-laporan*'))
            <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
                <a href="{{ route('daftar-laporan') }}" class="text-decoration-none">
                    <div class="card card-hover h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-4">
                                <i class="fas fa-file-alt fa-4x text-primary"></i>
                            </div>
                            <h3 class="card-title mb-3">DAFTAR LAPORAN</h3>
                            <p class="card-text mb-4">Akses menu untuk melihat daftar laporan yang tersedia</p>
                            <div class="d-grid">
                                <button class="btn btn-outline-primary btn-card py-2">
                                    <i class="fas fa-list me-2"></i>Pilih Daftar Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(!request()->is('prosedur*') && auth()->user()->code_role === '002')
            <div class="col-lg-5 col-md-6">
                <a href="{{ route('prosedur-sib') }}" class="text-decoration-none">
                    <div class="card card-hover h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-4">
                                <i class="fas fa-file-upload fa-4x text-success"></i>
                            </div>
                            <h3 class="card-title mb-3">PENGAJUAN SIB HRA</h3>
                            <p class="card-text mb-4">Akses menu untuk pengajuan SIB HRA baru</p>
                            <div class="d-grid">
                                <button class="btn btn-outline-success btn-card py-2">
                                    <i class="fas fa-plus-circle me-2"></i>Pilih Pengajuan SIB HRA
                                </button>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(auth()->user()->code_role !== '002')
            <div class="col-12 text-center mt-4">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Anda tidak memiliki akses ke menu Pengajuan SIB HRA
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
