<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class FilterCategoryRequest extends FormRequest
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
        return $this->user()?->can(
            'categories.view'
        ) ?? false;
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

            'is_active' => $this->has('is_active')
                ? filter_var(
                    $this->input('is_active'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : null,

            'has_products' => $this->has('has_products')
                ? filter_var(
                    $this->input('has_products'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : null,

            'is_parent' => $this->has('is_parent')
                ? filter_var(
                    $this->input('is_parent'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : null,

            'is_child' => $this->has('is_child')
                ? filter_var(
                    $this->input('is_child'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : null,

            'sort_by' => filled($this->sort_by)
                ? strtolower(
                    trim((string) $this->sort_by)
                )
                : 'sort_order',

            'sort_direction' => filled($this->sort_direction)
                ? strtolower(
                    trim((string) $this->sort_direction)
                )
                : 'asc',

            'per_page' => $this->filled('per_page')
                ? (int) $this->input('per_page')
                : 15,
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            'search' => [

                'bail',

                'nullable',

                'string',

                'max:255',
            ],

            'parent_id' => [

                'bail',

                'nullable',

                'integer',

                Rule::exists(
                    'categories',
                    'id'
                )->whereNull(
                    'deleted_at'
                ),
            ],

            'is_active' => [

                'nullable',

                'boolean',
            ],

            'has_products' => [

                'nullable',

                'boolean',
            ],

            'is_parent' => [

                'nullable',

                'boolean',
            ],

            'is_child' => [

                'nullable',

                'boolean',
            ],

            'sort_by' => [

                'nullable',

                Rule::in([

                    'name',

                    'slug',

                    'sort_order',

                    'created_at',

                    'updated_at',
                ]),
            ],

            'sort_direction' => [

                'nullable',

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
                'Pencarian maksimal 255 karakter.',

            'parent_id.exists' =>
                'Kategori induk tidak ditemukan.',

            'is_active.boolean' =>
                'Filter status aktif tidak valid.',

            'has_products.boolean' =>
                'Filter produk tidak valid.',

            'is_parent.boolean' =>
                'Filter kategori induk tidak valid.',

            'is_child.boolean' =>
                'Filter kategori anak tidak valid.',

            'sort_by.in' =>
                'Kolom pengurutan tidak valid.',

            'sort_direction.in' =>
                'Arah pengurutan tidak valid.',

            'per_page.integer' =>
                'Jumlah data per halaman harus berupa angka.',

            'per_page.min' =>
                'Jumlah data minimal 1.',

            'per_page.max' =>
                'Jumlah data maksimal 100.',
        ];
    }

    /**
     * Friendly attribute names.
     */
    public function attributes(): array
    {
        return [

            'search' =>
                'kata kunci pencarian',

            'parent_id' =>
                'kategori induk',

            'is_active' =>
                'status aktif',

            'has_products' =>
                'memiliki produk',

            'is_parent' =>
                'kategori induk',

            'is_child' =>
                'kategori anak',

            'sort_by' =>
                'kolom pengurutan',

            'sort_direction' =>
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
        return array_filter(

            [

                'search' =>
                    $this->validated('search'),

                'parent_id' =>
                    $this->validated('parent_id'),

                'is_active' =>
                    $this->validated('is_active'),

                'has_products' =>
                    $this->validated('has_products'),

                'is_parent' =>
                    $this->validated('is_parent'),

                'is_child' =>
                    $this->validated('is_child'),

                'sort_by' =>
                    $this->validated(
                        'sort_by',
                        'sort_order'
                    ),

                'sort_direction' =>
                    $this->validated(
                        'sort_direction',
                        'asc'
                    ),
            ],

            static fn ($value) => !is_null($value)
        );
    }

    /**
     * Get per page value.
     */
    public function perPage(): int
    {
        return max(
            1,
            min(
                (int) $this->validated(
                    'per_page',
                    15
                ),
                100
            )
        );
    }
}