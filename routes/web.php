<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BiLinkController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConnectedUserController;
use App\Http\Controllers\PowerBIController;

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('dashboard');

    // BI Links CRUD
    Route::resource('bi-links', BiLinkController::class);

    // Users
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');

    // Connected Users
    Route::get('/connected-users', [ConnectedUserController::class, 'index'])->name('connected-users.index');
    Route::get('/connected-users/count', [ConnectedUserController::class, 'getActiveCount'])->name('connected-users.count');

    // Power BI reports (service-connected; no per-user Microsoft login)
    Route::get('/reports', [PowerBIController::class, 'showReport'])->name('reports.show');
    Route::get('/reports-error', [PowerBIController::class, 'error'])->name('reports.error');

    Route::redirect('/', '/reports')->name('home');
});
