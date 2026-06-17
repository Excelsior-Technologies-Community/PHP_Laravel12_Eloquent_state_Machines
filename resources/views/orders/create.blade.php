<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Order</title>
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
<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen transition-colors duration-300">

    <div class="container mx-auto px-4 py-8 max-w-2xl">
        
        <div class="flex justify-between items-center mb-6">
            <a href="/?role={{ $role }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-indigo-500 transition-colors">
                <i class="ri-arrow-left-line"></i> Back to Orders
            </a>
            <button onclick="toggleDarkMode()" class="p-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-gray-800 dark:text-white">
                <i class="ri-moon-line dark:ri-sun-line"></i>
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-6">
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    Create New Order
                </h1>
            </div>
            
            <form action="{{ route('orders.store') }}?role={{ $role }}" method="POST" class="p-6">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">
                        Order Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 dark:text-white"
                           placeholder="Enter order name">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 dark:text-white"
                              placeholder="Enter order description (optional)"></textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="amount" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">
                        Amount 
                    </label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 dark:text-white"
                           placeholder="Enter amount (optional)">
                    @error('amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold px-6 py-2 rounded-lg hover:from-indigo-600 hover:to-purple-600 transition-all">
                        Create Order
                    </button>
                    <a href="/?role={{ $role }}" 
                       class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>