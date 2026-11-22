<?php

namespace App\Support;

use App\Enums\ProductAvailability;
use App\Enums\ProductFormat;
use App\Models\Category;
use App\Models\Product;
use Carbon\CarbonInterface;

class CatalogView
{
    /**
     * @return array<string, mixed>
     */
    public static function product(Product $product): array
    {
        $availability = $product->getAttribute('availability');
        $format = $product->getAttribute('format');
        $isPreorder = $product->isPreorder();
        $releasesAt = $product->getAttribute('releases_at');

        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'title' => $product->title,
            'author' => $product->author,
            'publisher' => $product->publisher,
            'isbn' => $product->isbn,
            'format' => $format instanceof ProductFormat ? $format->value : null,
            'formatLabel' => $format instanceof ProductFormat ? $format->label() : null,
            'language' => $product->language,
            'pages' => $product->pages,
            'editionYear' => $product->edition_year,
            'synopsis' => $product->synopsis,
            'price' => (float) $product->price,
            'stock' => $product->stock,
            'availability' => $availability instanceof ProductAvailability ? $availability->value : null,
            'availabilityLabel' => $isPreorder
                ? 'Preventa'
                : ($availability instanceof ProductAvailability ? $availability->label() : null),
            'isAvailable' => $availability instanceof ProductAvailability
                ? $availability !== ProductAvailability::OutOfStock
                : true,
            'isPreorder' => $isPreorder,
            'depositAmount' => $product->deposit_amount === null ? null : (float) $product->deposit_amount,
            'depositMinimum' => $isPreorder ? $product->minimumDepositFor(1) : null,
            'releasesAt' => $releasesAt instanceof CarbonInterface ? $releasesAt->toISOString() : null,
            'coverPath' => $product->cover_path,
            'category' => $product->category ? self::category($product->category) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function category(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
        ];
    }
}
