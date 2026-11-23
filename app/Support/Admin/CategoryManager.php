<?php

namespace App\Support\Admin;

use App\Models\Category;
use Illuminate\Validation\ValidationException;

final class CategoryManager
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function save(array $data, ?Category $category = null): Category
    {
        $attributes = [
            'name' => (string) $data['name'],
            'slug' => (string) $data['slug'],
            'description' => is_string($data['description'] ?? null) && $data['description'] !== ''
                ? $data['description']
                : null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];

        $category ??= new Category;
        $category->fill($attributes);
        $category->save();

        return $category;
    }

    public function destroy(Category $category): void
    {
        if ($category->products()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'No se puede eliminar una categoría que todavía tiene productos.',
            ]);
        }

        $category->delete();
    }
}
