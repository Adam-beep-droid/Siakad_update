<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Mail\NotifikasiMahasiswa;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SIAKAD (Sistem Informasi Akademik)
|--------------------------------------------------------------------------
*/

// ---- Auth Routes ----
Route::get('/', [AuthController::class, 'showLogin'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---- Protected Routes (Harus Login) ----
Route::middleware('auth')->group(function () {

    // ---- MAIN MAHASISWA ROUTES (Urutan Krusial) ----
    Route::get('/mahasiswa',            [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::get('/mahasiswa/create',     [MahasiswaController::class, 'create'])->name('mahasiswa.create');
    Route::post('/mahasiswa',           [MahasiswaController::class, 'store'])->name('mahasiswa.store');
    
    // Taruh Export dan Import di sini (Sebelum rute dinamis {mahasiswa})
    Route::get('/mahasiswa/export',     [MahasiswaController::class, 'export'])->name('mahasiswa.export');
    Route::get('/mahasiswa/import/form',[MahasiswaController::class, 'importForm'])->name('mahasiswa.import.form');
    Route::post('/mahasiswa/import',    [MahasiswaController::class, 'import'])->name('mahasiswa.import');

    // Rute detail parameter dinamis ditaruh paling bawah agar tidak bentrok
    Route::get('/mahasiswa/{mahasiswa}',        [MahasiswaController::class, 'show'])->name('mahasiswa.show');
    Route::get('/mahasiswa/{mahasiswa}/edit',   [MahasiswaController::class, 'edit'])->name('mahasiswa.edit');
    Route::put('/mahasiswa/{mahasiswa}',        [MahasiswaController::class, 'update'])->name('mahasiswa.update');
    Route::delete('/mahasiswa/{mahasiswa}',     [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');

    // ---- Activity Log ----
    Route::get('/logs', function () {
        $logs = ActivityLog::with('user')->latest()->paginate(20);
        return view('logs.index', compact('logs'));
    })->name('logs.index');

    Route::get('/logs/latest', function () {
        $logs = ActivityLog::with('user')->latest()->take(10)->get();

        return response()->json($logs->map(function ($log) {
            return [
                'id' => $log->id,
                'created_at' => $log->created_at->format('d M Y H:i:s'),
                'action' => $log->action,
                'action_badge' => $log->action_badge,
                'action_icon' => $log->action_icon,
                'description' => $log->description,
                'target' => $log->target,
                'algorithm' => $log->algorithm,
                'complexity' => $log->complexity,
                'execution_time' => $log->execution_time,
                'user_comment' => $log->user_comment,
                'user_name' => $log->user?->name ?? 'System',
                'data_count' => $log->data_count,
            ];
        }));
    })->name('logs.latest');

    Route::post('/logs/{log}/comment', [MahasiswaController::class, 'addComment'])->name('logs.comment');

    // ---- Kirim Email Mahasiswa ----
    Route::get('/notifications/send', [MahasiswaController::class, 'sendEmailForm'])->name('notifications.form');
    Route::post('/notifications/send', [MahasiswaController::class, 'sendEmail'])->name('notifications.send');
});

Route::get('/test-email', function () {
    Mail::to('emailtujuan@gmail.com')
        ->send(new NotifikasiMahasiswa('Adam', 'Selamat Datang', 'Test email dari aplikasi SIAKAD.'));

    return 'Email berhasil dikirim';
});