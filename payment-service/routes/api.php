<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;

Route::get('/transactions', [TransactionController::class, 'index']);
Route::post('/transactions/topup', [TransactionController::class, 'topup']);
Route::post('/transactions/pay', [TransactionController::class, 'pay']);
