<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['ok' => true]));

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/home', [WalletController::class, 'home']);
    Route::post('/receive', [WalletController::class, 'receive']);
    Route::post('/kyc', [KycController::class, 'store']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/stats', [TransactionController::class, 'stats']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
    Route::post('/transactions/send', [TransactionController::class, 'send']);
    Route::post('/transactions/deposit', [TransactionController::class, 'deposit']);
    Route::post('/transactions/withdraw', [TransactionController::class, 'withdraw']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read']);
    Route::post('/support', [SupportController::class, 'store']);
});
