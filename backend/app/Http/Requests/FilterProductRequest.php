<?php

namespace App\Http\Requests;

use App\Models\Product;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class FilterProductRequest extends FormRequest
{
    /**
     * Stop validation on first failure.
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine whether the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([

            'search' => filled($this->search)
                ? trim((string) $this->search)
                : null,

            'status' => filled($this->status)
                ? strtolower(
                    trim((string) $this->status)
                )
                : null,

            'sort' => filled($this->sort)
                ? strtolower(
                    trim((string) $this->sort)
                )
                : 'latest',

            'direction' => filled($this->direction)
                ? strtolower(
                    trim((string) $this->direction)
                )
                : 'desc',

            'is_active' => $this->has('is_active')
                ? filter_var(
                    $this->input('is_active'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : null,

            'is_featured' => $this->has('is_featured')
                ? filter_var(
                    $this->input('is_featured'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : null,

            'published' => $this->has('published')
                ? filter_var(
                    $this->input('published'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : null,

            'per_page' => max(
                1,
                min(
                    (int) $this->input('per_page', 15),
                    100
                )
            ),
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            'search' => [

                'nullable',

                'string',

                'max:255',
            ],

            'category_id' => [

                'nullable',

                'integer',

                Rule::exists(
                    'categories',
                    'id'
                ),
            ],

            'status' => [

                'nullable',

                'string',

                Rule::in([

                    Product::STATUS_DRAFT,

                    Product::STATUS_ACTIVE,

                    Product::STATUS_INACTIVE,

                    Product::STATUS_ARCHIVED,
                ]),
            ],

            'is_active' => [

                'nullable',

                'boolean',
            ],

            'is_featured' => [

                'nullable',

                'boolean',
            ],

            'published' => [

                'nullable',

                'boolean',
            ],

            'sort' => [

                'nullable',

                'string',

                Rule::in([

                    'latest',

                    'oldest',

                    'name',

                    'published_at',

                    'created_at',

                    'updated_at',
                ]),
            ],

            'direction' => [

                'nullable',

                'string',

                Rule::in([

                    'asc',

                    'desc',
                ]),
            ],

            'per_page' => [

                'nullable',

                'integer',

                'min:1',

                'max:100',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'search.max' =>
                'Kata kunci pencarian maksimal 255 karakter.',

            'category_id.integer' =>
                'Kategori tidak valid.',

            'category_id.exists' =>
                'Kategori tidak ditemukan.',

            'status.in' =>
                'Status produk tidak valid.',

            'is_active.boolean' =>
                'Filter status aktif tidak valid.',

            'is_featured.boolean' =>
                'Filter featured tidak valid.',

            'published.boolean' =>
                'Filter publish tidak valid.',

            'sort.in' =>
                'Metode pengurutan tidak valid.',

            'direction.in' =>
                'Arah pengurutan tidak valid.',

            'per_page.integer' =>
                'Jumlah data per halaman harus berupa angka.',

            'per_page.min' =>
                'Jumlah data per halaman minimal 1.',

            'per_page.max' =>
                'Jumlah data per halaman maksimal 100.',
        ];
    }

    /**
     * Friendly attribute names.
     */
    public function attributes(): array
    {
        return [

            'search' =>
                'kata kunci',

            'category_id' =>
                'kategori',

            'status' =>
                'status',

            'is_active' =>
                'status aktif',

            'is_featured' =>
                'featured',

            'published' =>
                'publish',

            'sort' =>
                'pengurutan',

            'direction' =>
                'arah pengurutan',

            'per_page' =>
                'jumlah data per halaman',
        ];
    }

    /**
     * Get validated filters.
     */
    public function filters(): array
    {
        return [

            'search' =>
                $this->validated('search'),

            'category_id' =>
                $this->validated('category_id'),

            'status' =>
                $this->validated('status'),

            'is_active' =>
                $this->validated('is_active'),

            'is_featured' =>
                $this->validated('is_featured'),

            'published' =>
                $this->validated('published'),

            'sort' =>
                $this->validated('sort', 'latest'),

            'direction' =>
                $this->validated('direction', 'desc'),
        ];
    }

    /**
     * Get pagination size.
     */
    public function perPage(): int
    {
        return (int) $this->validated(
            'per_page',
            15
        );
    }
}