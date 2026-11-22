<?php

namespace App\Models;

use App\Enums\ProductAvailability;
use App\Enums\ProductFormat;
use Carbon\CarbonInterface;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id',
    'title',
    'slug',
    'author',
    'publisher',
    'isbn',
    'format',
    'language',
    'pages',
    'edition_year',
    'synopsis',
    'price',
    'stock',
    'availability',
    'deposit_amount',
    'releases_at',
    'cover_path',
    'is_active',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'format' => ProductFormat::class,
            'availability' => ProductAvailability::class,
            'pages' => 'integer',
            'edition_year' => 'integer',
            'price' => 'decimal:2',
            'stock' => 'integer',
            'deposit_amount' => 'decimal:2',
            'releases_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<CartItem, $this> */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /** @return HasMany<Reservation, $this> */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    #[Scope]
    protected function inStock(Builder $query): Builder
    {
        return $query->where('availability', ProductAvailability::InStock);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    #[Scope]
    protected function preorder(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('availability', ProductAvailability::OnDemand)
            ->whereNotNull('deposit_amount')
            ->where('releases_at', '>', now());
    }

    public function isPreorder(): bool
    {
        $availability = $this->getAttribute('availability');
        $releasesAt = $this->getAttribute('releases_at');

        return $this->is_active
            && $availability === ProductAvailability::OnDemand
            && $releasesAt instanceof CarbonInterface
            && $releasesAt->isFuture()
            && $this->deposit_amount !== null;
    }

    public function minimumDepositFor(int $quantity): float
    {
        return round((float) $this->deposit_amount * $quantity, 2);
    }
}
