<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\MusikController;
use App\Http\Controllers\AlatMusikController;
use App\Http\Controllers\PemainMusikController;
use App\Http\Controllers\IbadahController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GenerateJadwalController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Halaman login
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);

// Proses login
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Semua route harus auth
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [PeriodeController::class, 'index'])->name('dashboard');

    // Resource Periode (store, update, destroy)
    Route::resource('periode', PeriodeController::class)->except(['show', 'create', 'edit']);

    // Halaman Alat Musik & Personil
    Route::get('/musikpersonil', [MusikController::class, 'index'])->name('musikpersonil');

    // Resource CRUD Alat Musik
    Route::resource('alat', AlatMusikController::class)->except(['show', 'create', 'edit']);

    // Resource CRUD Pemain Musik
    Route::resource('pemain', PemainMusikController::class)->except(['show', 'create', 'edit']);


Route::get('/ibadah', [IbadahController::class, 'index'])->name('ibadah.index');
Route::post('/ibadah/store', [IbadahController::class, 'store'])->name('ibadah.store');
Route::put('/ibadah/update/{id}', [IbadahController::class, 'update'])->name('ibadah.update');
Route::delete('/ibadah/{id}', [IbadahController::class, 'destroy'])->name('ibadah.destroy');

Route::post('/ibadah/storeWaktu', [IbadahController::class, 'storeWaktu'])->name('ibadah.storeWaktu');
Route::put('/ibadah/updateWaktu/{id}', [IbadahController::class, 'updateWaktu'])->name('ibadah.updateWaktu');
Route::delete('/ibadah/destroyWaktu/{id}', [IbadahController::class, 'destroyWaktu'])->name('ibadah.destroyWaktu');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/jadwal/generate', [GenerateJadwalController::class, 'index'])->name('generate.jadwal');
Route::post('/jadwal/generate', [GenerateJadwalController::class, 'generate'])->name('generate.jadwal');
Route::post('/jadwal/generate/save', [GenerateJadwalController::class, 'saveGenerated'])->name('generate.jadwal.save');