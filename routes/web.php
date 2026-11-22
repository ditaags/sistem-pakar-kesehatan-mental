<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;

// 1. Rute Publik (Dilindungi LogoutAdminDiHalamanPublik: admin otomatis ter-logout jika membuka halaman ini)
Route::middleware('admin.public.logout')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/konsultasi', [ConsultationController::class, 'index'])->name('konsultasi.index');
    Route::post('/konsultasi', [ConsultationController::class, 'store'])->name('konsultasi.store');
    Route::get('/hasil/{id}', [ConsultationController::class, 'show'])->name('hasil.show');
    Route::get('/tentang-kami', [AboutController::class, 'index'])->name('about');
});

// 2. Rute Autentikasi Admin (Headers no-store & pengamanan sesi)
Route::middleware('admin.security')->group(function () {
    Route::get('/kelola-pakar/login', [AdminController::class, 'loginForm'])->name('login');
    Route::get('/login', fn() => redirect()->route('login'))->name('admin.login');
    Route::post('/kelola-pakar/login', [AdminController::class, 'login'])->name('admin.login.submit');
    Route::post('/kelola-pakar/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
});

// 3. Rute Portal Admin Minimal (Dilindungi Auth Session & Admin Security Headers/Timeout)
Route::middleware(['auth', 'admin.security'])->prefix('kelola-pakar')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::post('/gejala', [AdminController::class, 'storeSymptom'])->name('symptoms.store');
    Route::put('/gejala/{code}', [AdminController::class, 'updateSymptom'])->name('symptoms.update');
    Route::delete('/gejala/{code}', [AdminController::class, 'destroySymptom'])->name('symptoms.destroy');
    Route::put('/aturan/{id}', [AdminController::class, 'updateRule'])->name('rules.update');
    Route::put('/kondisi/{code}', [AdminController::class, 'updateCondition'])->name('conditions.update');
});
