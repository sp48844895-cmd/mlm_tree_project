<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TreeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TreeController::class, 'index'])->name('home');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/referrals/validate', [AuthController::class, 'validateReferral'])->name('referral.validate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
