<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DataSibController;
use App\Http\Controllers\FirePreventiveController;
use App\Http\Controllers\FatigueActivityController;
use App\Http\Controllers\InspeksiKendaraanController;
use App\Http\Controllers\DevelopmentManpowerController;
use App\Http\Controllers\KeselamatanAreaKerjaController;
use App\Http\Controllers\ProgramKerjaKesehatanController;
use App\Http\Controllers\ProgramLingkunganHidupController;

Route::middleware(['inactivity', 'guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware(['auth', 'inactivity'])->group(function () {
    Route::get('/dashboard', function () {
        return view('layout.home');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Mitra Routes
    Route::prefix('mitra')->group(function () {
        Route::get('/', [MitraController::class, 'index'])->name('mitra.index');
        Route::get('/create', [MitraController::class, 'create'])->name('mitra.create');
        Route::post('/', [MitraController::class, 'store'])->name('mitra.store');
        Route::get('/{id}/edit', [MitraController::class, 'edit'])->name('mitra.edit');
        Route::put('/{id}', [MitraController::class, 'update'])->name('mitra.update');
        Route::delete('/{id}', [MitraController::class, 'destroy'])->name('mitra.destroy');
    });

    // Fatigue Preventive Routes
    Route::prefix('fatigue-preventive')->group(function () {
        Route::get('/dashboard', [FatigueActivityController::class, 'dashboard'])
             ->name('fatigue-preventive.dashboard');

        Route::prefix('activities')->group(function () {
            Route::get('/', [FatigueActivityController::class, 'index'])
                 ->name('fatigue-activities.index');
            Route::get('/{id}', [FatigueActivityController::class, 'show'])
                 ->name('fatigue-activities.show');
            Route::put('/{id}', [FatigueActivityController::class, 'update'])
                 ->name('fatigue-activities.update');
            Route::delete('/{id}', [FatigueActivityController::class, 'destroy'])
                 ->name('fatigue-activities.destroy');

            Route::prefix('create')->group(function () {
                Route::get('/ftw', [FatigueActivityController::class, 'createFtw'])
                     ->name('fatigue-activities.create-ftw');
                Route::get('/dfit', [FatigueActivityController::class, 'createDfit'])
                     ->name('fatigue-activities.create-dfit');
                Route::get('/fatigue-check', [FatigueActivityController::class, 'createFatigueCheck'])
                     ->name('fatigue-activities.create-fatigue-check');
                Route::get('/wakeup-call', [FatigueActivityController::class, 'createWakeupCall'])
                     ->name('fatigue-activities.create-wakeup-call');
                Route::get('/saga', [FatigueActivityController::class, 'createSaga'])
                     ->name('fatigue-activities.create-saga');
                Route::get('/sidak', [FatigueActivityController::class, 'createSidak'])
                     ->name('fatigue-activities.create-sidak');
                Route::get('/{type}', [FatigueActivityController::class, 'create'])
                     ->name('fatigue-activities.create-type');
            });

            Route::get('/{id}/edit', [FatigueActivityController::class, 'edit'])
                 ->name('fatigue-activities.edit');
            Route::post('/', [FatigueActivityController::class, 'store'])
                 ->name('fatigue-activities.store');
        });

        Route::patch('fatigue-activities/{id}/toggle-approval', [FatigueActivityController::class, 'toggleApproval'])
             ->name('fatigue-activities.toggle-approval');
    });

    // Keselamatan Area Kerja Routes - Improved Version
    Route::prefix('keselamatan-area-kerja')->name('keselamatan.')->group(function () {
        // Dashboard
        Route::get('/', [KeselamatanAreaKerjaController::class, 'dashboard'])->name('dashboard');

        // Type-specific routes (inspeksi/gelar/housekeeping)
        Route::prefix('{type}')->group(function () {
            // Index/list activities
            Route::get('/', [KeselamatanAreaKerjaController::class, 'index'])
                 ->name('type.index'); // Changed from 'type.index' to 'index'

            // Create form
            Route::get('/create', [KeselamatanAreaKerjaController::class, 'create'])
                 ->name('create'); // Changed from 'type.create' to 'create'

            // Store new activity
            Route::post('/', [KeselamatanAreaKerjaController::class, 'store'])
                 ->name('store'); // Changed from 'type.store' to 'store'

            // Report
            Route::get('/laporan', [KeselamatanAreaKerjaController::class, 'report'])
                 ->name('type.report'); // Changed from 'type.report' to 'report'
        });

        // Activity-specific operations
        Route::prefix('activity')->group(function () {
            // Show activity detail
            Route::get('/{activity}', [KeselamatanAreaKerjaController::class, 'show'])
                 ->name('show');

            // Edit form
            Route::get('/{activity}/edit', [KeselamatanAreaKerjaController::class, 'edit'])
                 ->name('edit');

            // Update activity
            Route::put('/{activity}', [KeselamatanAreaKerjaController::class, 'update'])
                 ->name('update');

            // Delete activity
            Route::delete('/{activity}', [KeselamatanAreaKerjaController::class, 'destroy'])
                 ->name('destroy');

            // Toggle approval
            Route::patch('/{activity}/toggle-approval', [KeselamatanAreaKerjaController::class, 'toggleApproval'])
                 ->name('toggle-approval');
        });
    });



    // Daftar Laporan
    Route::get('/daftar-laporan', function () {
        return view('daftarlaporan.daftarpengguna');
    })->name('daftar-laporan');

    Route::prefix('inspeksi')->group(function () {
        // Dashboard khusus inspeksi
        Route::get('/dashboard', [InspeksiKendaraanController::class, 'dashboard'])
            ->name('inspeksi.dashboard');

        // CRUD operations
        Route::get('/', [InspeksiKendaraanController::class, 'index'])
            ->name('inspeksi.index');
        Route::get('/create', [InspeksiKendaraanController::class, 'create'])
            ->name('inspeksi.create');
        Route::post('/', [InspeksiKendaraanController::class, 'store'])
            ->name('inspeksi.store');
        Route::get('/{inspeksi}', [InspeksiKendaraanController::class, 'show'])
            ->name('inspeksi.show');
        Route::get('/{inspeksi}/edit', [InspeksiKendaraanController::class, 'edit'])
            ->name('inspeksi.edit');
        Route::put('/{inspeksi}', [InspeksiKendaraanController::class, 'update'])
            ->name('inspeksi.update');
        Route::delete('/{inspeksi}', [InspeksiKendaraanController::class, 'destroy'])
            ->name('inspeksi.destroy');
    });
    Route::prefix('development-manpower')->group(function () {
    Route::get('/dashboard', [DevelopmentManpowerController::class, 'dashboard'])->name('development-manpower.dashboard');
    Route::get('/', [DevelopmentManpowerController::class, 'index'])->name('development-manpower.index');
    Route::get('/create/{kategori}', [DevelopmentManpowerController::class, 'create'])
        ->where('kategori', '.*')
        ->name('development-manpower.create');
    Route::post('/', [DevelopmentManpowerController::class, 'store'])->name('development-manpower.store');
    Route::get('/{development_manpower}', [DevelopmentManpowerController::class, 'show'])->name('development-manpower.show');
    Route::get('/{development_manpower}/edit', [DevelopmentManpowerController::class, 'edit'])->name('development-manpower.edit');
    Route::put('/{development_manpower}', [DevelopmentManpowerController::class, 'update'])->name('development-manpower.update');
    Route::delete('/{development_manpower}', [DevelopmentManpowerController::class, 'destroy'])->name('development-manpower.destroy');
});
Route::prefix('program-kesehatan')->group(function() {
    Route::get('/dashboard', [ProgramKerjaKesehatanController::class, 'dashboard'])->name('program-kesehatan.dashboard');
    Route::get('/', [ProgramKerjaKesehatanController::class, 'index'])->name('program-kesehatan.index');
    Route::get('/create', [ProgramKerjaKesehatanController::class, 'create'])->name('program-kesehatan.create');
    Route::post('/', [ProgramKerjaKesehatanController::class, 'store'])->name('program-kesehatan.store');
    Route::get('/{programKerjaKesehatan}', [ProgramKerjaKesehatanController::class, 'show'])->name('program-kesehatan.show');
    Route::get('/{programKerjaKesehatan}/edit', [ProgramKerjaKesehatanController::class, 'edit'])->name('program-kesehatan.edit');
    Route::put('/{programKerjaKesehatan}', [ProgramKerjaKesehatanController::class, 'update'])->name('program-kesehatan.update');
    Route::delete('/{programKerjaKesehatan}', [ProgramKerjaKesehatanController::class, 'destroy'])->name('program-kesehatan.destroy');
    Route::get('/{program}/download/{type}', [ProgramKerjaKesehatanController::class, 'downloadFile'])->name('program-kesehatan.download');
});
Route::prefix('program-lingkungan')->group(function () {
    Route::get('dashboard', [ProgramLingkunganHidupController::class, 'dashboard'])->name('program-lingkungan.dashboard');
    Route::get('/', [ProgramLingkunganHidupController::class, 'index'])->name('program-lingkungan.index');
    Route::get('create/{jenis}', [ProgramLingkunganHidupController::class, 'create'])->name('program-lingkungan.create');
    Route::post('/', [ProgramLingkunganHidupController::class, 'store'])->name('program-lingkungan.store');
    Route::put('program-lingkungan/{programLingkunganHidup}', [ProgramLingkunganHidupController::class, 'update'])
    ->name('program-lingkungan.update');
    Route::get('{programLingkunganHidup}', [ProgramLingkunganHidupController::class, 'show'])->name('program-lingkungan.show');
    Route::get('{programLingkunganHidup}/edit', [ProgramLingkunganHidupController::class, 'edit'])->name('program-lingkungan.edit');
    Route::delete('{programLingkunganHidup}', [ProgramLingkunganHidupController::class, 'destroy'])->name('program-lingkungan.destroy');
});
Route::prefix('fire-preventive')->group(function() {
    Route::get('/dashboard', [FirePreventiveController::class, 'dashboard'])->name('fire-preventive.dashboard');
    Route::get('/', [FirePreventiveController::class, 'index'])->name('fire-preventive.index');
    Route::get('/create/{type}', [FirePreventiveController::class, 'create'])->name('fire-preventive.create');
    Route::post('/', [FirePreventiveController::class, 'store'])->name('fire-preventive.store');
    Route::get('/{id}', [FirePreventiveController::class, 'show'])->name('fire-preventive.show');
    Route::get('/{id}/edit', [FirePreventiveController::class, 'edit'])->name('fire-preventive.edit');
    Route::put('/{id}', [FirePreventiveController::class, 'update'])->name('fire-preventive.update');
    Route::delete('/{id}', [FirePreventiveController::class, 'destroy'])->name('fire-preventive.destroy');
});
Route::prefix('data-sib')->name('data-sib.')->group(function () {
        Route::get('/', [DataSibController::class, 'index'])->name('index');
        Route::get('/create', [DataSibController::class, 'create'])->name('create');
        Route::post('/', [DataSibController::class, 'store'])->name('store');
        Route::get('/{dataSib}', [DataSibController::class, 'show'])->name('show');
            Route::get('/{dataSib}/edit', [DataSibController::class, 'edit'])->name('edit');
            Route::put('/{dataSib}', [DataSibController::class, 'update'])->name('update');
            Route::delete('/{dataSib}', [DataSibController::class, 'destroy'])->name('destroy');
    });

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/trashed', [UserController::class, 'trashed'])->name('users.trashed');
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/export', [ReportController::class, 'exportBisnis'])->name('reports.bisnis.export');
});
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/auto-logout', function() {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login')
        ->with('message', 'Anda telah logout karena tidak aktif selama 30 menit');
})->name('auto-logout');

Route::get('/test-fonnte', function() {
    // Verify API configuration
    if (!config('services.fonnte.key') || !config('services.fonnte.url')) {
        return response()->json([
            'status' => 'error',
            'message' => 'API configuration missing'
        ], 500);
    }

    // Test parameters
    $testPhone = '6281233838624'; // Replace with your test number
    $testMessage = 'Test message from Laravel at ' . now()->format('Y-m-d H:i:s');

    try {
        $response = Http::withHeaders([
            'Authorization' => config('services.fonnte.key'),
        ])
        ->timeout(10)
        ->post(config('services.fonnte.url').'/send', [
            'target' => $testPhone,
            'message' => $testMessage,
            'countryCode' => '62'
        ]);

        return response()->json([
            'status' => $response->successful() ? 'success' : 'error',
            'http_status' => $response->status(),
            'response' => $response->json(),
            'request' => [
                'phone' => $testPhone,
                'message' => $testMessage
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ], 500);
    }
});
