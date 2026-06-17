<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <script>
        tailwind.config = { darkMode: 'class' }
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        }
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        }
    </script>
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
<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen transition-colors duration-300">

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                        Order Management System
                    </h1>
                </div>
                
                <div class="flex gap-4 items-center">
                    <button onclick="toggleDarkMode()" class="p-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-gray-800 dark:text-white">
                        <i class="ri-moon-line dark:ri-sun-line"></i>
                    </button>
                    <div class="flex gap-2 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                        <a href="/?role=user&status={{ $statusFilter }}" 
                           class="px-4 py-2 rounded-lg transition-all {{ $role == 'user' ? 'bg-indigo-500 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                            <i class="ri-user-line mr-1"></i> User
                        </a>
                        <a href="/?role=admin&status={{ $statusFilter }}" 
                           class="px-4 py-2 rounded-lg transition-all {{ $role == 'admin' ? 'bg-indigo-500 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                            <i class="ri-admin-line mr-1"></i> Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-6">
            <div class="flex flex-wrap gap-4 justify-between items-center">
                <div class="flex gap-2">
                    <a href="{{ route('orders.create') }}?role={{ $role }}" 
                       class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition-all flex items-center gap-2">
                        <i class="ri-add-line"></i> New Order
                    </a>
                    
                    @if($role == 'admin')
                    <a href="{{ route('orders.export') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all flex items-center gap-2">
                        <i class="ri-file-excel-2-line"></i> Export
                    </a>
                    @endif
                    
                    <div class="flex gap-2 ml-2">
                        <a href="/?role={{ $role }}&status=all" 
                           class="px-3 py-2 rounded-lg text-sm {{ $statusFilter == 'all' ? 'bg-gray-800 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-gray-300' }}">
                            All
                        </a>
                        <a href="/?role={{ $role }}&status=pending" 
                           class="px-3 py-2 rounded-lg text-sm {{ $statusFilter == 'pending' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500' }}">
                            Pending
                        </a>
                        <a href="/?role={{ $role }}&status=processing" 
                           class="px-3 py-2 rounded-lg text-sm {{ $statusFilter == 'processing' ? 'bg-blue-500 text-white' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-500' }}">
                            Processing
                        </a>
                        <a href="/?role={{ $role }}&status=complete" 
                           class="px-3 py-2 rounded-lg text-sm {{ $statusFilter == 'complete' ? 'bg-green-500 text-white' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-500' }}">
                            Completed
                        </a>
                        <a href="/?role={{ $role }}&status=canceled" 
                           class="px-3 py-2 rounded-lg text-sm {{ $statusFilter == 'canceled' ? 'bg-red-500 text-white' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-500' }}">
                            Canceled
                        </a>
                    </div>
                </div>
                
                @if($role == 'admin')
                <form action="{{ route('orders.bulk-transition') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="order_ids" id="bulkOrderIds" value="">
                    <input type="hidden" name="new_status" id="bulkNewStatus" value="">
                    <select id="bulkStatusSelect" class="px-3 py-2 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="">Bulk Action</option>
                        <option value="processing">Process Selected</option>
                        <option value="complete">Complete Selected</option>
                        <option value="canceled">Cancel Selected</option>
                    </select>
                    <button type="button" onclick="performBulkAction()" class="bg-gray-700 dark:bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-800">
                        Apply
                    </button>
                </form>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-400 rounded-lg flex items-center gap-2">
                <i class="ri-checkbox-circle-line"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 text-red-700 dark:text-red-400 rounded-lg flex items-center gap-2">
                <i class="ri-alert-line"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($orders->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($orders as $order)
                <div class="order-card bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700 {{ $order->status_badge_class }} bg-opacity-50 dark:bg-opacity-20">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-file-copy-line text-gray-500"></i>
                                    <h3 class="font-semibold text-gray-800 dark:text-white">#{{ $order->id }}</h3>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $order->name }}</h2>
                                @if($order->description)
                                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ Str::limit($order->description, 80) }}</p>
                                @endif
                                @if($order->amount)
                                    <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-2">{{ number_format($order->amount, 2) }}</p>
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
                        
                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-semibold {{ $order->status_badge_class }}">
                            <span class="w-2 h-2 rounded-full bg-current"></span>
                            {{ ucfirst($order->status) }}
                        </div>
                    </div>
                    
                    <div class="p-5 dark:bg-gray-800">
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Change Status:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($allowedTransitions[$order->status] ?? [] as $nextStatus)
                                    @if(!($role === 'user' && $order->status === 'processing' && $nextStatus === 'canceled'))
                                        <a href="{{ route('order.transition', [$order->id, $nextStatus]) }}?role={{ $role }}"
                                           class="transition-btn px-3 py-1.5 rounded-lg text-sm font-semibold text-white bg-indigo-500 hover:bg-indigo-600">
                                            {{ ($nextStatus == 'pending' && $order->status == 'canceled') ? 'Reorder' : ucfirst($nextStatus) }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        
                        <a href="{{ route('orders.show', $order->id) }}?role={{ $role }}" 
                           class="inline-flex items-center gap-1 text-indigo-500 hover:text-indigo-600 text-sm font-medium">
                            View Details <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center">
                <i class="ri-inbox-line text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400 text-lg">No orders found</p>
            </div>
        @endif
    </div>

    <script>
        function performBulkAction() {
            const status = document.getElementById('bulkStatusSelect').value;
            if (!status) { alert('Please select a status action'); return; }
            document.getElementById('bulkNewStatus').value = status;
            document.querySelector('form[action="{{ route('orders.bulk-transition') }}"]').submit();
        }
    </script>
</body>
</html>