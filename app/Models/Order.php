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