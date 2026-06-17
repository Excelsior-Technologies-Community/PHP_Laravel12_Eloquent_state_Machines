<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderStatusHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Notifications\OrderStatusChanged;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'description', 'amount', 'user_id'];

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
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

        if ($currentStatus === 'pending' && $newStatus === 'complete') {
            throw new \Exception("Cannot skip steps: Pending to Complete is not allowed.");
        }

        if ($role === 'user' && $currentStatus === 'processing' && $newStatus === 'canceled') {
            throw new \Exception("Users cannot cancel after processing");
        }

        $oldStatus = $currentStatus;

        $this->status = $newStatus;
        $this->save();

        if ($this->user) {
            $this->user->notify(new OrderStatusChanged($this));
        }

        $this->histories()->create([
            'from_status' => $oldStatus,
            'to_status'   => $newStatus,
        ]);

        return true;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'pending' => 'yellow',
            'processing' => 'blue',
            'complete' => 'green',
            'canceled' => 'red',
        ][$this->status] ?? 'gray';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return [
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
            'complete' => 'bg-green-100 text-green-800 border-green-200',
            'canceled' => 'bg-red-100 text-red-800 border-red-200',
        ][$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}