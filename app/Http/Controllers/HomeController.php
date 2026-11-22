<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\CatalogView;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $featuredProducts = Product::query()
            ->active()
            ->with('category')
            ->orderBy('title')
            ->limit(4)
            ->get()
            ->map(fn (Product $product) => CatalogView::product($product))
            ->all();

        $categories = Category::query()
            ->active()
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => CatalogView::category($category))
            ->all();

        return Inertia::render('Welcome', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
        ]);
    }
}
