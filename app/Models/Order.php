<?php

namespace App\Models;

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
        'status',
        'payment_status',
        'payment_reference',
        'snap_token',
        'total_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
     * Check if this order belongs to the given user.
     */
    public function belongsToUser(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Mark as paid.
     */
    public function markAsPaid(string $paymentReference): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_reference' => $paymentReference,
            'status' => 'confirmed',
        ]);
    }
}
