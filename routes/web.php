<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\MusikController;
use App\Http\Controllers\AlatMusikController;
use App\Http\Controllers\PemainMusikController;

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
});
