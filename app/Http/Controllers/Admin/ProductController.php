<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Support\Admin\AdminView;
use App\Support\Admin\ProductManager;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private readonly ProductManager $products) {}

    public function index(): Response
    {
        return Inertia::render('admin/products/Index', [
            'products' => Product::query()
                ->with('category')
                ->orderBy('title')
                ->get()
                ->map(fn (Product $product): array => AdminView::product($product))
                ->values()
                ->all(),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function create(): Response
    {
        return $this->form(null);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->products->save($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El producto se creó correctamente.',
        ]);

        return to_route('admin.products.index');
    }

    public function edit(Product $product): Response
    {
        return $this->form($product);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->products->save($request->validated(), $product);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El producto se actualizó correctamente.',
        ]);

        return to_route('admin.products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->products->destroy($product);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El producto se eliminó correctamente.',
        ]);

        return to_route('admin.products.index');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function categoryOptions(): array
    {
        return array_values(Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category): array => AdminView::category($category))
            ->all());
    }

    private function form(?Product $product): Response
    {
        return Inertia::render('admin/products/Form', [
            'product' => $product ? AdminView::product($product) : null,
            'categories' => $this->categoryOptions(),
            'formats' => AdminView::productFormats(),
            'availabilities' => AdminView::productAvailabilities(),
        ]);
    }
}
