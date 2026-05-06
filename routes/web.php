<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', [OrderController::class, 'index'])->name('home');

Route::get('/order/{id}/transition/{status}', [OrderController::class, 'transition'])
    ->name('order.transition');