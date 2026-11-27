<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AppFinController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// ─── Public marketing site ───────────────────────────────────
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/recursos', [SiteController::class, 'features'])->name('features');
Route::get('/precos', [SiteController::class, 'pricing'])->name('pricing');
Route::get('/sobre', [SiteController::class, 'about'])->name('about');
Route::get('/contato', [SiteController::class, 'contact'])->name('contact');

// ─── Auth (guest only) ───────────────────────────────────────
Route::middleware('guest.mock')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/esqueci-senha', [AuthController::class, 'showForgotPassword'])->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── App (financeiro — auth-gated, mock) ─────────────────────
Route::middleware('auth.mock')->group(function () {
    Route::get('/app', [AppController::class, 'dashboard'])->name('dashboard');
    Route::get('/app/pagar', [AppFinController::class, 'payable'])->name('app.payable');
    Route::get('/app/receber', [AppFinController::class, 'receivable'])->name('app.receivable');
    Route::get('/app/fluxo', [AppFinController::class, 'cashflow'])->name('app.cashflow');
    Route::get('/app/lancamentos', [AppFinController::class, 'entries'])->name('app.entries');
    Route::get('/app/notas', [AppFinController::class, 'invoices'])->name('app.invoices');
    Route::get('/app/relatorios', [AppFinController::class, 'reports'])->name('app.reports');
    Route::get('/app/configuracoes', [AppController::class, 'settings'])->name('app.settings');
});

// ─── Fallback 404 ────────────────────────────────────────────
Route::fallback(fn () => response()->view('pages.not-found', ['pageTitle' => '404'], 404));
