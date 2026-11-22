<?php

namespace App\Http\Requests;

use App\Enums\ProductFormat;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatalogIndexRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', 'exists:categories,slug'],
            'q' => ['nullable', 'string', 'max:120'],
            'format' => ['nullable', Rule::enum(ProductFormat::class)],
        ];
    }
}
