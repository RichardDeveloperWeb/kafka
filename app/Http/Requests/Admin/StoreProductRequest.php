<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductAvailability;
use App\Enums\ProductFormat;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        $ignore = $product instanceof Product ? $product->id : null;
        $isPreorder = $this->input('availability') === ProductAvailability::OnDemand->value;

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($ignore)],
            'author' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:20', Rule::unique('products', 'isbn')->ignore($ignore)],
            'format' => ['required', Rule::enum(ProductFormat::class)],
            'language' => ['required', 'string', 'max:10'],
            'pages' => ['nullable', 'integer', 'min:1'],
            'edition_year' => ['nullable', 'integer', 'between:1900,2100'],
            'synopsis' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'availability' => ['required', Rule::enum(ProductAvailability::class)],
            'deposit_amount' => Rule::when(
                $isPreorder,
                ['required', 'numeric', 'min:0'],
                ['nullable', 'numeric', 'min:0'],
            ),
            'releases_at' => Rule::when(
                $isPreorder,
                ['required', 'date', 'after:now'],
                ['nullable', 'date'],
            ),
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return list<\Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['price', 'deposit_amount', 'availability'])) {
                    return;
                }

                if ($this->input('availability') !== ProductAvailability::OnDemand->value) {
                    return;
                }

                if ((float) $this->input('deposit_amount') > (float) $this->input('price')) {
                    $validator->errors()->add('deposit_amount', 'El depósito no puede superar el precio del producto.');
                }
            },
        ];
    }
}
