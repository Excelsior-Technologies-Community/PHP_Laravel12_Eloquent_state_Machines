# PHP_Laravel12_Eloquent_state_Machines


## Project Description

This project shows how to implement a state machine in Laravel Eloquent models.
You can create orders and safely transition them between states while preventing invalid transitions.
The project uses Tailwind CSS for a modern UI and provides visual feedback of order status with colored badges and buttons.


## Features

- Create and view orders with current status

- State machine enforcement for valid transitions

- Buttons to change order status dynamically

- Status badges with color coding

- Error handling for invalid transitions

- Simple, modern UI using Tailwind CSS



## Technologies Used

1. Laravel 12 – PHP framework for backend

2. Eloquent ORM – For database models

3. laravel-eloquent-state-machines – State machine logic

4. spatie/laravel-model-states – Optional state management

5. MySQL – Database

6. Tailwind CSS – Frontend styling

7. PHP 8+ – Server-side scripting



---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_Eloquent_state_Machines "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_Eloquent_state_Machines

```

#### Explanation:

Installs a fresh Laravel 12 project and navigates into the project folder.




## STEP 2: Install laravel-eloquent-state-machines

### Run:

```
composer require asantibanez/laravel-eloquent-state-machines

composer require spatie/laravel-model-states

```

#### Explanation:

Installs packages that allow you to define state machines in your Eloquent models.





## STEP 3: Publish Package Configurations

### Run:

```
php artisan vendor:publish --provider="Asantibanez\LaravelEloquentStateMachines\ServiceProvider"

```

### That creates:

```
config/eloquent-state-machines.php

```

#### Explanation:

Creates a config file config/eloquent-state-machines.php for customizing state machine behavior.




## STEP 4: Database Setup (Optional)

### Update database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_Eloquent_state_Machines
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel12_Eloquent_state_Machines

```


### Then migrate:

```
php artisan migrate

```


#### Explanation:

Configures .env to connect Laravel with MySQL and creates the laravel12_Eloquent_state_Machines database.



## STEP 5: Create Example Model With State Machine

### Let's make an Order model that has states:

```
pending -> processing -> completed -> cancelled

```

### Generate Model + Migration

```
php artisan make:model Order -m

```

### Edit Migration: database/migrations/<timestamp>_create_orders_table.php

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // State column for the state machine
            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

```


### Then migrate:

```
php artisan migrate

```

#### Explanation:

Adds status column to store the current state of the order.




## STEP 6: Define State Machine on Order Model

### Open: app/Models/Order.php

#### Replace contents with:

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['name', 'status'];

    // Ensure new orders default to 'pending'
    protected $attributes = [
        'status' => 'pending',
    ];

    // Define allowed transitions
    protected static array $allowedTransitions = [
        'pending'    => ['processing', 'canceled'],
        'processing' => ['complete', 'canceled'],
        'complete'   => [],
        'canceled'   => [],
    ];

    /**
     * Transition the order to a new status.
     *
     * @param string $newStatus
     * @return bool
     */
    public function transition(string $newStatus): bool
    {
        $currentStatus = $this->status ?? 'pending'; // ✅ handle null safely

        if (!isset(self::$allowedTransitions[$currentStatus])) {
            throw new \Exception("Invalid current status: {$currentStatus}");
        }

        if (!in_array($newStatus, self::$allowedTransitions[$currentStatus])) {
            throw new \Exception("Cannot transition from {$currentStatus} to {$newStatus}");
        }

        $this->status = $newStatus;
        $this->save();

        // Optional: log transition
        \Log::info("Order #{$this->id} transitioned from {$currentStatus} to {$newStatus}");

        return true;
    }
}

```

#### Explanation:

This ensures orders only move between valid states.




## STEP 7: Using the State Machine

#### Create a route to test:

### Open: routes/web.php

```
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

```


#### Explanation:

Clicking a button changes the order’s status, and the page refreshes to show the new status.





## STEP 8: Run Project

### Run:

```
php artisan serve

```

### Open

```
http://127.0.0.1:8000

```

#### Explanation:

The homepage shows your test order with current status and buttons to change it.




## Expected Output:


### Initial Status: pending


<img width="1919" height="934" alt="Screenshot 2026-03-05 143945" src="https://github.com/user-attachments/assets/d2998373-28fc-4392-8742-87572d88edf7" />


### If Status: Cancel


<img width="1919" height="949" alt="Screenshot 2026-03-05 144425" src="https://github.com/user-attachments/assets/4c4c3a38-84f0-4daa-ae30-769850099345" />


### After start_processing: processing


<img width="1919" height="930" alt="Screenshot 2026-03-05 144011" src="https://github.com/user-attachments/assets/2ed86fee-7fd5-4dc1-ad4e-04172b8d335f" />


### Final Status: completed


<img width="1912" height="949" alt="Screenshot 2026-03-05 144037" src="https://github.com/user-attachments/assets/e523e8f8-7c59-4026-bb88-d722f8d44100" />



---

# Project Folder Structure:

```

PHP_Laravel12_Eloquent_state_Machines/
├── app/
│   └── Models/
│       └── Order.php         <-- Order model with state machine
├── database/
│   └── migrations/
│       └── xxxx_create_orders_table.php   <-- Orders migration
├── routes/
│   └── web.php               <-- Routes for order status and transitions
├── config/
│   └── eloquent-state-machines.php  <-- Package config
├── public/
│   └── index.php
└── README.md <-- Your instructions

```
