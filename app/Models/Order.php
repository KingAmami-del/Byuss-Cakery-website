<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_number',
    'access_token_hash',
    'customer_name',
    'customer_phone',
    'customer_address',
    'customer_note',
    'payment_method',
    'total_amount',
    'status',
    'proof_path',
    'payment_submitted_at',
    'paid_at',
    'admin_note',
])]
class Order extends Model
{
    public const STATUS_AWAITING_PAYMENT = 'awaiting_payment';

    public const STATUS_AWAITING_CONFIRMATION = 'awaiting_confirmation';

    public const STATUS_PAID = 'paid';

    public const STATUS_REJECTED = 'payment_rejected';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_DELIVERED = 'delivered';

    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'integer',
            'payment_submitted_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }
}
