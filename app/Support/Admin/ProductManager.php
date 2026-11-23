<?php

namespace App\Support\Admin;

use App\Enums\ProductAvailability;
use App\Enums\ProductFormat;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

final class ProductManager
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function save(array $data, ?Product $product = null): Product
    {
        $availability = ProductAvailability::from((string) $data['availability']);
        $isPreorder = $availability === ProductAvailability::OnDemand;

        $attributes = [
            'category_id' => (int) $data['category_id'],
            'title' => (string) $data['title'],
            'slug' => (string) $data['slug'],
            'author' => $this->nullableString($data['author'] ?? null),
            'publisher' => $this->nullableString($data['publisher'] ?? null),
            'isbn' => $this->nullableString($data['isbn'] ?? null),
            'format' => ProductFormat::from((string) $data['format']),
            'language' => (string) ($data['language'] ?? 'es'),
            'pages' => isset($data['pages']) && $data['pages'] !== '' ? (int) $data['pages'] : null,
            'edition_year' => isset($data['edition_year']) && $data['edition_year'] !== ''
                ? (int) $data['edition_year']
                : null,
            'synopsis' => $this->nullableString($data['synopsis'] ?? null),
            'price' => round((float) $data['price'], 2),
            'stock' => (int) $data['stock'],
            'availability' => $availability,
            'deposit_amount' => $isPreorder ? round((float) $data['deposit_amount'], 2) : null,
            'releases_at' => $isPreorder ? (string) $data['releases_at'] : null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];

        $product ??= new Product;
        $product->fill($attributes);
        $product->save();

        return $product;
    }

    public function destroy(Product $product): void
    {
        if ($product->reservations()->exists() || $product->orderItems()->exists() || $product->cartItems()->exists()) {
            throw ValidationException::withMessages([
                'product' => 'No se puede eliminar un producto con preventas, pedidos o carritos asociados.',
            ]);
        }

        $product->delete();
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        return $value;
    }
}
