<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Order;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Order::where('status', 'pending')
        ->where('created_at', '<', Carbon::now()->subHours(24))
        ->get()
        ->each(fn($order) => $order->transition('canceled', 'system'));
})->daily();