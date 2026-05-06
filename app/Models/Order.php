<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderStatusHistory;

class Order extends Model
{
    protected $fillable = ['name', 'status'];

    protected $attributes = [
        'status' => 'pending',
    ];

   
    protected static array $allowedTransitions = [
        'pending'    => ['processing', 'canceled'],
        'processing' => ['complete', 'canceled'],
        'complete'   => [],
        'canceled'   => ['pending'], 
    ];

    public function histories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function transition(string $newStatus, string $role = 'user'): bool
    {
        $currentStatus = $this->status ?? 'pending';

        if (!isset(self::$allowedTransitions[$currentStatus])) {
            throw new \Exception("Invalid current status: {$currentStatus}");
        }

        if (!in_array($newStatus, self::$allowedTransitions[$currentStatus])) {
            throw new \Exception("Cannot transition from {$currentStatus} to {$newStatus}");
        }

        // Role restriction
        if ($role === 'user' && $currentStatus === 'processing' && $newStatus === 'canceled') {
            throw new \Exception("Users cannot cancel after processing");
        }

        $oldStatus = $currentStatus;

        $this->status = $newStatus;
        $this->save();

        $this->histories()->create([
            'from_status' => $oldStatus,
            'to_status'   => $newStatus,
        ]);

        return true;
    }
}