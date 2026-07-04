<?php

use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/control/login', [AdminDashboardController::class, 'loginForm'])->name('control.login');
Route::post('/control/login', [AdminDashboardController::class, 'login'])->name('control.login.submit');
Route::post('/control/logout', [AdminDashboardController::class, 'logout'])->name('control.logout');

Route::get('/control', [AdminDashboardController::class, 'dashboard'])->name('control.dashboard');
Route::post('/control/transactions/{transaction}/approve', [AdminDashboardController::class, 'approveTransaction'])->name('control.transactions.approve');
Route::post('/control/transactions/{transaction}/reject', [AdminDashboardController::class, 'rejectTransaction'])->name('control.transactions.reject');
Route::post('/control/transactions/{transaction}/status', [AdminDashboardController::class, 'updateTransactionStatus'])->name('control.transactions.status');
Route::post('/control/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('control.users.update');
Route::post('/control/wallets', [AdminDashboardController::class, 'storeWallet'])->name('control.wallets.store');
Route::post('/control/wallets/{wallet}', [AdminDashboardController::class, 'updateWallet'])->name('control.wallets.update');
Route::post('/control/networks', [AdminDashboardController::class, 'storeNetwork'])->name('control.networks.store');
Route::post('/control/fees/{fee}', [AdminDashboardController::class, 'updateFee'])->name('control.fees.update');
