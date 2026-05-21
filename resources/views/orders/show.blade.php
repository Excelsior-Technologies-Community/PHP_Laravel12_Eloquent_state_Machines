<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - {{ $order->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="/?role={{ $role }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-indigo-500 transition-colors">
                <i class="ri-arrow-left-line"></i> Back to Orders
            </a>
        </div>

        <!-- Order Details Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
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
            
            <!-- Body -->
            <div class="p-6">
                <!-- Order Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    @if($order->description)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                             Description
                        </h3>
                        <p class="text-gray-600">{{ $order->description }}</p>
                    </div>
                    @endif
                    
                    @if($order->amount)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            Amount
                        </h3>
                        <p class="text-2xl font-bold text-indigo-600">{{ number_format($order->amount, 2) }}</p>
                    </div>
                    @endif
                </div>

                <!-- Status Actions -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                         Update Status
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($allowedTransitions[$order->status] ?? [] as $nextStatus)
                            @if(!($role === 'user' && $order->status === 'processing' && $nextStatus === 'canceled'))
                                <a href="{{ route('order.transition', [$order->id, $nextStatus]) }}?role={{ $role }}"
                                   class="px-5 py-2 rounded-lg font-semibold transition-all transform hover:scale-105
                                   @if($nextStatus == 'processing') bg-blue-500 hover:bg-blue-600 text-white shadow-md
                                   @elseif($nextStatus == 'complete') bg-green-500 hover:bg-green-600 text-white shadow-md
                                   @elseif($nextStatus == 'canceled') bg-red-500 hover:bg-red-600 text-white shadow-md
                                   @elseif($nextStatus == 'pending') bg-yellow-500 hover:bg-yellow-600 text-white shadow-md
                                   @endif">
                                    {{ ($nextStatus == 'pending' && $order->status == 'canceled') ? 'Reorder' : ucfirst($nextStatus) }}
                                </a>
                            @endif
                        @endforeach
                        @if(count($allowedTransitions[$order->status] ?? []) == 0)
                            <span class="text-gray-500 italic">No further transitions available</span>
                        @endif
                    </div>
                </div>

                <!-- History Timeline -->
                <div>
                    <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                         Status History
                    </h3>
                    
                    @if($order->histories->count() > 0)
                        <div class="space-y-3">
                            @foreach($order->histories as $history)
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                           
                                        </div>
                                    </div>
                                    <div class="flex-grow bg-gray-50 rounded-lg p-3">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <span class="font-medium text-gray-800">{{ ucfirst($history->from_status) }}</span>
                                              
                                                <span class="font-medium text-gray-800">{{ ucfirst($history->to_status) }}</span>
                                            </div>
                                            <span class="text-xs text-gray-400">{{ $history->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ $history->created_at->format('F j, Y, g:i a') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <i class="ri-information-line text-4xl text-gray-300"></i>
                            <p class="text-gray-400 mt-2">No history recorded yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>
</html>