<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $order = Order::firstOrCreate(['name' => 'Test Order']);

        $role = request('role', 'user');

        $statusColors = [
            'pending' => 'bg-yellow-400 text-yellow-900',
            'processing' => 'bg-blue-400 text-white',
            'complete' => 'bg-green-500 text-white',
            'canceled' => 'bg-red-500 text-white',
        ];

        $allowedTransitions = [
            'pending'    => ['processing', 'canceled'],
            'processing' => ['complete', 'canceled'],
            'complete'   => [],
            'canceled'   => ['pending'], 
        ];

        return view('orders.index', compact(
            'order',
            'statusColors',
            'allowedTransitions',
            'role'
        ));
    }

    public function transition($id, $status)
    {
        $role = request('role', 'user');

        $order = Order::findOrFail($id);

        try {
            $order->transition($status, $role);
            return redirect()->route('home');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}