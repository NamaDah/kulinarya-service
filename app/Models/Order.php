<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'status',
        'payment_status',
        'payment_reference',
        'snap_token',
        'total_amount',
        'rating',
        'driver_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'rating' => 'integer',
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assigned driver.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the products for the order (pivot).
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }

    /**
     * Get the order product records directly.
     */
    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get the messages for this order.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Check if this order belongs to the given user.
     */
    public function belongsToUser(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Rate the order (1-5 stars).
     */
    public function rate(int $stars): void
    {
        $this->update(['rating' => $stars]);

        // Recalculate the driver's accumulated rating
        if ($this->driver_id && $this->driver) {
            $this->driver->recalculateRating();
        }
    }

    /**
     * Mark as paid.
     */
    public function markAsPaid(string $paymentReference): void
    {
        $this->update([
            'payment_status' => PaymentStatus::Paid,
            'payment_reference' => $paymentReference,
            'status' => OrderStatus::Confirmed,
        ]);
    }
}
