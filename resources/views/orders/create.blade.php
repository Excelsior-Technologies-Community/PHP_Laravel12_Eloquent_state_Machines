<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Order</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">

    <div class="container mx-auto px-4 py-8 max-w-2xl">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="/?role={{ $role }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-indigo-500 transition-colors">
                <i class="ri-arrow-left-line"></i> Back to Orders
            </a>
        </div>

        <!-- Create Order Form -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-6">
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    
                    Create New Order
                </h1>
               
            </div>
            
            <form action="{{ route('orders.store') }}?role={{ $role }}" method="POST" class="p-6">
                @csrf
                
                <!-- Order Name -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-semibold mb-2">
                        Order Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Enter order name">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-semibold mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                              placeholder="Enter order description (optional)"></textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Amount -->
                <div class="mb-6">
                    <label for="amount" class="block text-gray-700 font-semibold mb-2">
                        Amount 
                    </label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Enter amount (optional)">
                    @error('amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold px-6 py-2 rounded-lg hover:from-indigo-600 hover:to-purple-600 transition-all">
                        Create Order
                    </button>
                    <a href="/?role={{ $role }}" 
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>