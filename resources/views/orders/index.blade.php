<!DOCTYPE html>
<html>
<head>
    <title>Order State Machine</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white rounded-2xl shadow-2xl p-10 max-w-md w-full text-center">

    <!-- ROLE SWITCH -->
    <div class="mb-4">
        <a href="/?role=user"
           class="px-3 py-1 rounded mr-2 {{ $role == 'user' ? 'bg-gray-800 text-white' : 'bg-gray-300' }}">
            User
        </a>

        <a href="/?role=admin"
           class="px-3 py-1 rounded {{ $role == 'admin' ? 'bg-gray-800 text-white' : 'bg-gray-300' }}">
            Admin
        </a>
    </div>

    <p class="mb-3 text-sm text-gray-600">
        Current Role: <strong>{{ $role }}</strong>
    </p>

    <!-- ORDER TITLE -->
    <h1 class="text-3xl font-bold mb-6 text-gray-800">
        Order: {{ $order->name }}
    </h1>

    <!-- STATUS BADGE -->
    <div class="mb-6">
        <span class="px-6 py-2 rounded-full font-semibold text-lg {{ $statusColors[$order->status] }}">
            {{ $order->status }}
        </span>
    </div>

    <!-- BUTTONS -->
    <h2 class="text-gray-700 mb-3 font-medium">Change Status:</h2>

    <div>
        @if(count($allowedTransitions[$order->status]) > 0)

            @foreach($allowedTransitions[$order->status] as $nextStatus)

                {{-- ❌ Hide cancel for user after processing --}}
                @if($role === 'user' && $order->status === 'processing' && $nextStatus === 'canceled')
                    @continue
                @endif

                <a href="{{ route('order.transition', [$order->id, $nextStatus]) }}?role={{ $role }}"
                   class="px-5 py-2 rounded-full font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-500 
                   hover:from-purple-500 hover:to-indigo-500 transition-all shadow-lg mr-2 mb-2 inline-block">

                    {{-- Show "Reorder" instead of pending --}}
                    {{ ($nextStatus == 'pending' && $order->status == 'canceled') ? 'Reorder' : ucfirst($nextStatus) }}

                </a>

            @endforeach

        @else
            <span class="text-gray-500 italic">No further transitions available</span>
        @endif
    </div>

    <!-- ERROR MESSAGE -->
    @if(session('error'))
        <div class="mt-4 text-red-500 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- HISTORY -->
    <h3 class="mt-6 font-semibold text-gray-800">History:</h3>

    <div class="mt-3 space-y-2">
        @forelse($order->histories as $history)
            <div class="text-sm text-gray-600 bg-gray-100 rounded px-3 py-2">
                {{ ucfirst($history->from_status) }} → {{ ucfirst($history->to_status) }}
            </div>
        @empty
            <p class="text-gray-400 text-sm">No history yet</p>
        @endforelse
    </div>

</div>

</body>
</html>