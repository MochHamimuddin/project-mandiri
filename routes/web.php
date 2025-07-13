<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\FatigueActivityController;
use App\Http\Controllers\InspeksiKendaraanController;
use App\Http\Controllers\KeselamatanAreaKerjaController;

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

