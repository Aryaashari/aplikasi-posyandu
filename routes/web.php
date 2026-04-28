<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    
    Route::get('/dashboard', function () {
        $posyanduId = Illuminate\Support\Facades\Auth::user()->id_posyandu;
        $currentMonth = date('m');
        $currentYear = date('Y');

        $totalBalita = \App\Models\MdAnak::where('id_posyandu', $posyanduId)->count();
        $hadirBulanIni = \App\Models\TrxKehadiran::where('id_posyandu', $posyanduId)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->distinct('id_anak')
            ->count('id_anak');

        $recentAttendance = \App\Models\TrxKehadiran::where('id_posyandu', $posyanduId)
            ->with(['anak'])
            ->latest('tanggal')
            ->latest('waktu_checkin')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_balita' => $totalBalita,
                'hadir_bulan_ini' => $hadirBulanIni,
            ],
            'recent_attendance' => $recentAttendance
        ]);
    })->name('dashboard');

    Route::resource('anak', \App\Http\Controllers\AnakController::class);
    Route::resource('pengukuran', \App\Http\Controllers\PengukuranController::class);

    // Kehadiran (Attendance) Routes
    Route::get('/kehadiran', [\App\Http\Controllers\KehadiranController::class, 'index'])->name('kehadiran.index');
    Route::get('/kehadiran/check-in', [\App\Http\Controllers\KehadiranController::class, 'checkIn'])->name('kehadiran.check-in');
    Route::post('/kehadiran', [\App\Http\Controllers\KehadiranController::class, 'store'])->name('kehadiran.store');

    // Imunisasi Routes
    Route::post('/imunisasi', [\App\Http\Controllers\ImunisasiController::class, 'store'])->name('imunisasi.store');

    // Laporan & Rekap Routes
    Route::get('/laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export/pdf', [\App\Http\Controllers\LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
    Route::get('/laporan/export/excel', [\App\Http\Controllers\LaporanController::class, 'exportExcel'])->name('laporan.export.excel');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
