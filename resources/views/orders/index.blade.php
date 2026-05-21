<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .order-card {
            animation: fadeIn 0.3s ease-out;
        }
        .transition-btn {
            transition: all 0.2s ease;
        }
        .transition-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                     
                        Order Management System
                    </h1>
                    
                </div>
                
                <!-- Role Switch -->
                <div class="flex gap-2 bg-gray-100 rounded-lg p-1">
                    <a href="/?role=user&status={{ $statusFilter }}" 
                       class="px-4 py-2 rounded-lg transition-all {{ $role == 'user' ? 'bg-indigo-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-200' }}">
                        <i class="ri-user-line mr-1"></i> User
                    </a>
                    <a href="/?role=admin&status={{ $statusFilter }}" 
                       class="px-4 py-2 rounded-lg transition-all {{ $role == 'admin' ? 'bg-indigo-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-200' }}">
                        <i class="ri-admin-line mr-1"></i> Admin
                    </a>
                </div>
            </div>
        </div>

        

        <!-- Actions Bar -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <div class="flex flex-wrap gap-4 justify-between items-center">
                <div class="flex gap-2">
                    <a href="{{ route('orders.create') }}?role={{ $role }}" 
                       class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition-all flex items-center gap-2">
                        <i class="ri-add-line"></i> New Order
                    </a>
                    
                    <!-- Filter Buttons -->
                    <div class="flex gap-2 ml-2">
                        <a href="/?role={{ $role }}&status=all" 
                           class="px-3 py-2 rounded-lg text-sm {{ $statusFilter == 'all' ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}">
                            All
                        </a>
                        <a href="/?role={{ $role }}&status=pending" 
                           class="px-3 py-2 rounded-lg text-sm {{ $statusFilter == 'pending' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                            Pending
                        </a>
                        <a href="/?role={{ $role }}&status=processing" 
                           class="px-3 py-2 rounded-lg text-sm {{ $statusFilter == 'processing' ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                            Processing
                        </a>
                        <a href="/?role={{ $role }}&status=complete" 
                           class="px-3 py-2 rounded-lg text-sm {{ $statusFilter == 'complete' ? 'bg-green-500 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                            Completed
                        </a>
                        <a href="/?role={{ $role }}&status=canceled" 
                           class="px-3 py-2 rounded-lg text-sm {{ $statusFilter == 'canceled' ? 'bg-red-500 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                            Canceled
                        </a>
                    </div>
                </div>
                
                @if($role == 'admin')
                <form action="{{ route('orders.bulk-transition') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="order_ids" id="bulkOrderIds" value="">
                    <input type="hidden" name="new_status" id="bulkNewStatus" value="">
                    <select id="bulkStatusSelect" class="px-3 py-2 border rounded-lg">
                        <option value="">Bulk Action</option>
                        <option value="processing">Process Selected</option>
                        <option value="complete">Complete Selected</option>
                        <option value="canceled">Cancel Selected</option>
                    </select>
                    <button type="button" onclick="performBulkAction()" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-800">
                        Apply
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center gap-2">
                <i class="ri-checkbox-circle-line"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center gap-2">
                <i class="ri-alert-line"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Orders Grid -->
        @if($orders->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($orders as $order)
                <div class="order-card bg-white rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden">
                    <!-- Order Header -->
                    <div class="p-5 border-b {{ $order->status_badge_class }} bg-opacity-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-file-copy-line text-gray-500"></i>
                                    <h3 class="font-semibold text-gray-800">#{{ $order->id }}</h3>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">{{ $order->name }}</h2>
                                @if($order->description)
                                    <p class="text-gray-500 text-sm mt-1">{{ Str::limit($order->description, 80) }}</p>
                                @endif
                                @if($order->amount)
                                    <p class="text-lg font-bold text-indigo-600 mt-2">{{ number_format($order->amount, 2) }}</p>
                                @endif
                            </div>
                            @if($role == 'admin')
                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Delete this order?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors">
                                    <i class="ri-delete-bin-line text-xl"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-semibold {{ $order->status_badge_class }}">
                            <span class="w-2 h-2 rounded-full bg-current"></span>
                            {{ ucfirst($order->status) }}
                        </div>
                    </div>
                    
                    <!-- Order Body -->
                    <div class="p-5">
                        <!-- Transition Buttons -->
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-2">Change Status:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($allowedTransitions[$order->status] ?? [] as $nextStatus)
                                    @if(!($role === 'user' && $order->status === 'processing' && $nextStatus === 'canceled'))
                                        <a href="{{ route('order.transition', [$order->id, $nextStatus]) }}?role={{ $role }}"
                                           class="transition-btn px-3 py-1.5 rounded-lg text-sm font-semibold
                                           @if($nextStatus == 'processing') bg-blue-500 hover:bg-blue-600 text-white
                                           @elseif($nextStatus == 'complete') bg-green-500 hover:bg-green-600 text-white
                                           @elseif($nextStatus == 'canceled') bg-red-500 hover:bg-red-600 text-white
                                           @elseif($nextStatus == 'pending') bg-yellow-500 hover:bg-yellow-600 text-white
                                           @endif">
                                            {{ ($nextStatus == 'pending' && $order->status == 'canceled') ? 'Reorder' : ucfirst($nextStatus) }}
                                        </a>
                                    @endif
                                @endforeach
                                @if(count($allowedTransitions[$order->status] ?? []) == 0)
                                    <span class="text-gray-400 text-sm italic">No actions available</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Quick View Link -->
                        <a href="{{ route('orders.show', $order->id) }}?role={{ $role }}" 
                           class="inline-flex items-center gap-1 text-indigo-500 hover:text-indigo-600 text-sm font-medium">
                            View Details <i class="ri-arrow-right-line"></i>
                        </a>
                        
                        <!-- History Preview -->
                        @if($order->histories->count() > 0)
                        <div class="mt-4 pt-3 border-t">
                            <p class="text-xs text-gray-500">Last update:</p>
                            <p class="text-xs text-gray-600 mt-1">
                                {{ $order->histories->first()->from_status }} → {{ $order->histories->first()->to_status }}
                                <span class="text-gray-400 ml-1">{{ $order->histories->first()->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <i class="ri-inbox-line text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No orders found</p>
                <a href="{{ route('orders.create') }}?role={{ $role }}" class="inline-block mt-4 text-indigo-500 hover:text-indigo-600">
                    Create your first order →
                </a>
            </div>
        @endif
    </div>

    <script>
        // Select all checkboxes functionality
        let selectedOrders = [];
        
        function toggleSelectAll(checkbox) {
            document.querySelectorAll('.order-checkbox').forEach(cb => {
                cb.checked = checkbox.checked;
            });
            updateSelectedOrders();
        }
        
        function updateSelectedOrders() {
            selectedOrders = [];
            document.querySelectorAll('.order-checkbox:checked').forEach(cb => {
                selectedOrders.push(cb.value);
            });
            document.getElementById('bulkOrderIds').value = selectedOrders.join(',');
        }
        
        function performBulkAction() {
            const status = document.getElementById('bulkStatusSelect').value;
            if (!status) {
                alert('Please select a status action');
                return;
            }
            if (selectedOrders.length === 0) {
                alert('Please select at least one order');
                return;
            }
            document.getElementById('bulkNewStatus').value = status;
            document.getElementById('bulkOrderIds').value = selectedOrders.join(',');
            document.querySelector('form[action="{{ route('orders.bulk-transition') }}"]').submit();
        }
    </script>
</body>
</html>