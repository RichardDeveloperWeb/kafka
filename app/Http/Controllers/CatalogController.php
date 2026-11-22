<?php

namespace App\Http\Controllers;

use App\Enums\ProductFormat;
use App\Http\Requests\CatalogIndexRequest;
use App\Models\Address;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\CatalogView;
use App\Support\ClientView;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(CatalogIndexRequest $request): Response
    {
        $filters = $request->validated();
        $query = Product::query()->active()->with('category');

        if (filled($filters['category'] ?? null)) {
            $query->whereIn(
                'category_id',
                Category::query()->where('slug', $filters['category'])->pluck('id'),
            );
        }

        if (filled($filters['q'] ?? null)) {
            $query->whereAny(['title', 'author', 'isbn'], 'ilike', "%{$filters['q']}%");
        }

        if (filled($filters['format'] ?? null)) {
            $query->where('format', $filters['format']);
        }

        $products = $query
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Product $product) => CatalogView::product($product));

        $categories = Category::query()
            ->active()
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => CatalogView::category($category))
            ->all();

        return Inertia::render('catalog/Index', [
            'products' => $products,
            'categories' => $categories,
            'formats' => Collection::make(ProductFormat::cases())
                ->map(fn (ProductFormat $format) => ['value' => $format->value, 'label' => $format->label()])
                ->all(),
            'filters' => [
                'category' => $filters['category'] ?? '',
                'q' => $filters['q'] ?? '',
                'format' => $filters['format'] ?? '',
            ],
        ]);
    }

    public function show(Request $request, Product $product): Response
    {
        abort_unless($product->is_active, 404);

        $product->load('category');

        $user = $request->user();
        $addresses = $user instanceof User
            ? $user->addresses()
                ->orderByDesc('is_default')
                ->orderBy('label')
                ->get()
                ->map(fn (Address $address): array => ClientView::address($address))
                ->all()
            : [];

        $relatedProducts = Product::query()
            ->active()
            ->with('category')
            ->whereKeyNot($product->id)
            ->where('category_id', $product->category_id)
            ->orderBy('title')
            ->limit(4)
            ->get()
            ->map(fn (Product $related) => CatalogView::product($related))
            ->all();

        return Inertia::render('catalog/Show', [
            'product' => CatalogView::product($product),
            'addresses' => $addresses,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
