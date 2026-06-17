<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;

class OrderController extends Controller
{
    public function index()
    {
        $role = request('role', 'user');
        $statusFilter = request('status', 'all');
        
        $query = Order::with('histories');
        
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        
        $orders = $query->latest()->get();
        
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
        
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'complete' => Order::where('status', 'complete')->count(),
            'canceled' => Order::where('status', 'canceled')->count(),
        ];

        return view('orders.index', compact(
            'orders',
            'statusColors',
            'allowedTransitions',
            'role',
            'statusFilter',
            'stats'
        ));
    }

    public function show($id)
    {
        $role = request('role', 'user');
        $order = Order::with('histories')->findOrFail($id);
        
        $allowedTransitions = [
            'pending'    => ['processing', 'canceled'],
            'processing' => ['complete', 'canceled'],
            'complete'   => [],
            'canceled'   => ['pending'], 
        ];

        return view('orders.show', compact('order', 'role', 'allowedTransitions'));
    }

    public function transition($id, $status)
    {
        $role = request('role', 'user');

        $order = Order::findOrFail($id);

        try {
            $order->transition($status, $role);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'status' => $order->status,
                    'message' => "Order status updated to {$status}"
                ]);
            }
            
            return redirect()->route('home')->with('success', "Order status updated to {$status}");
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $role = request('role', 'user');
        return view('orders.create', compact('role'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $order = Order::create($validated);

        return redirect()->route('home')->with('success', 'Order created successfully!');
    }

    public function bulkTransition(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'new_status' => 'required|string'
        ]);

        $role = request('role', 'user');
        $successCount = 0;
        $failedOrders = [];

        foreach ($request->order_ids as $orderId) {
            $order = Order::find($orderId);
            try {
                $order->transition($request->new_status, $role);
                $successCount++;
            } catch (\Exception $e) {
                $failedOrders[] = $order->name;
            }
        }

        $message = "{$successCount} orders updated successfully.";
        if (!empty($failedOrders)) {
            $message .= " Failed: " . implode(', ', $failedOrders);
        }

        return redirect()->route('home')->with('success', $message);
    }

    public function export()
    {
        return Excel::download(new OrdersExport, 'orders_report.xlsx');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('home')->with('success', 'Order deleted successfully!');
    }
}