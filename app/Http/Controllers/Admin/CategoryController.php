<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Support\Admin\AdminView;
use App\Support\Admin\CategoryManager;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryManager $categories) {}

    public function index(): Response
    {
        return Inertia::render('admin/categories/Index', [
            'categories' => Category::query()
                ->withCount('products')
                ->orderBy('name')
                ->get()
                ->map(fn (Category $category): array => AdminView::category($category))
                ->values()
                ->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/categories/Form', [
            'category' => null,
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categories->save($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'La categoría se creó correctamente.',
        ]);

        return to_route('admin.categories.index');
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('admin/categories/Form', [
            'category' => AdminView::category($category),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categories->save($request->validated(), $category);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'La categoría se actualizó correctamente.',
        ]);

        return to_route('admin.categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categories->destroy($category);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'La categoría se eliminó correctamente.',
        ]);

        return to_route('admin.categories.index');
    }
}
