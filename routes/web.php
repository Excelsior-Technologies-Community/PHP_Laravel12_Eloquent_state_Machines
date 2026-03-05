<?php

use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Get or create a test order
    $order = Order::firstOrCreate(['name' => 'Test Order']);

    $currentStatus = $order->status;

    // Status colors for badge
    $statusColors = [
        'pending' => 'bg-yellow-400 text-yellow-900',
        'processing' => 'bg-blue-400 text-white',
        'complete' => 'bg-green-500 text-white',
        'canceled' => 'bg-red-500 text-white',
    ];

    // Allowed transitions
    $allowed = [
        'pending' => ['processing', 'canceled'],
        'processing' => ['complete', 'canceled'],
        'complete' => [],
        'canceled' => [],
    ];

    // Build transition buttons
    $buttons = '';
    if (isset($allowed[$currentStatus]) && count($allowed[$currentStatus]) > 0) {
        foreach ($allowed[$currentStatus] as $nextStatus) {
            $buttons .= "<a href='/order/{$order->id}/transition/{$nextStatus}' 
                class='px-5 py-2 rounded-full font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-500 
                hover:from-purple-500 hover:to-indigo-500 transition-all shadow-lg mr-3 mb-2 inline-block'>
                {$nextStatus}
            </a>";
        }
    } else {
        $buttons = "<span class='text-gray-500 italic'>No further transitions available</span>";
    }

    return "
    <html>
    <head>
        <script src='https://cdn.tailwindcss.com'></script>
        <title>Order Status</title>
    </head>
    <body class='bg-gray-100 flex items-center justify-center min-h-screen'>
        <div class='bg-white rounded-2xl shadow-2xl p-10 max-w-md w-full text-center'>
            <h1 class='text-3xl font-bold mb-6 text-gray-800'>Order: {$order->name}</h1>
            <div class='mb-6'>
                <span class='px-6 py-2 rounded-full font-semibold {$statusColors[$currentStatus]} text-lg'>
                    {$order->status}
                </span>
            </div>
            <h2 class='text-gray-700 mb-3 font-medium'>Change Status:</h2>
            <div>{$buttons}</div>
        </div>
    </body>
    </html>
    ";
});

// Route to handle status transition
Route::get('/order/{id}/transition/{status}', function ($id, $status) {
    $order = Order::findOrFail($id);

    try {
        $order->transition($status);
        return redirect('/');
    } catch (\Exception $e) {
        return "
        <html>
            <head><script src='https://cdn.tailwindcss.com'></script></head>
            <body class='flex items-center justify-center min-h-screen bg-gray-100'>
                <div class='bg-white p-8 rounded-2xl shadow-lg text-center max-w-sm'>
                    <p class='text-red-500 font-bold mb-4'>Error: {$e->getMessage()}</p>
                    <a href='/' class='inline-block px-5 py-2 rounded-full font-semibold text-white 
                        bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-purple-500 hover:to-indigo-500 transition-all shadow-lg'>
                        Go Back
                    </a>
                </div>
            </body>
        </html>
        ";
    }
});