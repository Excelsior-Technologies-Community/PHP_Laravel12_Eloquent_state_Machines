<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - {{ $order->name }}</title>
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
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        
        <div class="flex justify-between items-center mb-6">
            <a href="/?role={{ $role }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-indigo-500 transition-colors">
                <i class="ri-arrow-left-line"></i> Back to Orders
            </a>
            <button onclick="toggleDarkMode()" class="p-2 bg-gray-200 dark:bg-gray-800 rounded-lg text-gray-800 dark:text-gray-200">
                <i class="ri-moon-line dark:ri-sun-line"></i>
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden transition-colors">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <i class="ri-file-copy-line"></i>
                            <span class="text-sm opacity-90">Order #{{ $order->id }}</span>
                        </div>
                        <h1 class="text-3xl font-bold">{{ $order->name }}</h1>
                        <p class="mt-2 opacity-90">Created {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full px-4 py-2">
                        <span class="font-semibold">{{ ucfirst($order->status) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    @if($order->description)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-2 flex items-center gap-2">Description</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $order->description }}</p>
                    </div>
                    @endif
                    
                    @if($order->amount)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-2 flex items-center gap-2">Amount</h3>
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($order->amount, 2) }}</p>
                    </div>
                    @endif
                </div>

                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-3">Update Status</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($allowedTransitions[$order->status] ?? [] as $nextStatus)
                            @if(!($role === 'user' && $order->status === 'processing' && $nextStatus === 'canceled'))
                                <a href="{{ route('order.transition', [$order->id, $nextStatus]) }}?role={{ $role }}"
                                   class="px-5 py-2 rounded-lg font-semibold transition-all hover:scale-105 {{ $order->status_badge_class }}">
                                    {{ ucfirst($nextStatus) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-4">Status History</h3>
                    <div class="space-y-3">
                        @foreach($order->histories as $history)
                            <div class="flex items-start gap-3 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                <div class="flex-grow">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-800 dark:text-white">{{ ucfirst($history->from_status) }} → {{ ucfirst($history->to_status) }}</span>
                                        <span class="text-xs text-gray-400">{{ $history->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>