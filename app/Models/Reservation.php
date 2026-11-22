<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'product_id',
    'order_id',
    'quantity',
    'status',
    'deposit_amount',
    'balance_amount',
    'releases_at',
    'shipping_address',
    'charged_at',
    'refunded_at',
])]
class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'status' => ReservationStatus::class,
            'deposit_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
            'releases_at' => 'datetime',
            'shipping_address' => 'array',
            'charged_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return HasMany<PaymentTransaction, $this> */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * @param  Builder<Reservation>  $query
     * @return Builder<Reservation>
     */
    #[Scope]
    protected function pendingRelease(Builder $query): Builder
    {
        return $query
            ->where('status', ReservationStatus::Deposited)
            ->where('releases_at', '>', now());
    }

    /**
     * @param  Builder<Reservation>  $query
     * @return Builder<Reservation>
     */
    #[Scope]
    protected function chargeable(Builder $query): Builder
    {
        return $query
            ->where('status', ReservationStatus::Deposited)
            ->where('releases_at', '<=', now());
    }
}
