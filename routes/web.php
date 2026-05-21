<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

// Order routes
Route::get('/', [OrderController::class, 'index'])->name('home');
Route::get('/order/{id}/transition/{status}', [OrderController::class, 'transition'])->name('order.transition');
Route::get('/order/{id}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::post('/orders/bulk-transition', [OrderController::class, 'bulkTransition'])->name('orders.bulk-transition');
Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');