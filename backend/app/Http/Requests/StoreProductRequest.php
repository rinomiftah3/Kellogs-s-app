<?php

namespace App\Http\Requests;

use App\Models\Product;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Str;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductRequest extends FormRequest
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
            'products.create'
        ) ?? false;
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $name = trim(
            (string) $this->input('name')
        );

        $status = $this->input('status');

        $this->merge([

            'short_description' => filled(
                $this->short_description
            )
                ? trim(
                    (string) $this->short_description
                )
                : null,

            'description' => filled(
                $this->description
            )
                ? trim(
                    (string) $this->description
                )
                : null,

            'status' => filled($status)
                ? strtolower(
                    trim((string) $status)
                )
                : Product::STATUS_DRAFT,

            'is_featured' => $this->has('is_featured')
                ? filter_var(
                    $this->input('is_featured'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : false,

            'is_active' => $this->has('is_active')
                ? filter_var(
                    $this->input('is_active'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : true,
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            'category_id' => [

                'bail',

                'required',

                'integer',

                Rule::exists(
                    'categories',
                    'id'
                ),
            ],

            'name' => [

                'bail',

                'required',

                'string',

                'min:2',

                'max:255',
            ],

            'short_description' => [

                'nullable',

                'string',

                'max:500',
            ],

            'description' => [

                'nullable',

                'string',
            ],

            'thumbnail' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:2048',
            ],

            'status' => [

                'required',

                'string',

                Rule::in([

                    Product::STATUS_DRAFT,

                    Product::STATUS_ACTIVE,

                    Product::STATUS_INACTIVE,

                    Product::STATUS_ARCHIVED,
                ]),
            ],

            'is_featured' => [

                'sometimes',

                'boolean',
            ],

            'is_active' => [

                'sometimes',

                'boolean',
            ],

            'published_at' => [

                'nullable',

                'date',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'category_id.required' =>
                'Kategori wajib dipilih.',

            'category_id.exists' =>
                'Kategori tidak ditemukan.',

            'name.required' =>
                'Nama produk wajib diisi.',

            'name.min' =>
                'Nama produk minimal 2 karakter.',

            'name.max' =>
                'Nama produk maksimal 255 karakter.',

            'short_description.max' =>
                'Deskripsi singkat maksimal 500 karakter.',

            'thumbnail.image' =>
                'Thumbnail harus berupa gambar.',

            'thumbnail.mimes' =>
                'Thumbnail harus berformat JPG, JPEG, PNG, atau WEBP.',

            'thumbnail.max' =>
                'Ukuran thumbnail maksimal 2 MB.',

            'status.required' =>
                'Status produk wajib dipilih.',

            'status.in' =>
                'Status produk tidak valid.',

            'is_featured.boolean' =>
                'Status featured tidak valid.',

            'is_active.boolean' =>
                'Status aktif tidak valid.',

            'published_at.date' =>
                'Tanggal publish tidak valid.',
        ];
    }

    /**
     * Friendly attribute names.
     */
    public function attributes(): array
    {
        return [

            'category_id' =>
                'kategori',

            'name' =>
                'nama produk',

            'short_description' =>
                'deskripsi singkat',

            'description' =>
                'deskripsi',

            'thumbnail' =>
                'thumbnail',

            'status' =>
                'status',

            'is_featured' =>
                'featured',

            'is_active' =>
                'status aktif',

            'published_at' =>
                'tanggal publish',
        ];
    }

    /**
     * Additional validation.
     */
    public function withValidator(
        Validator $validator
    ): void {

        $validator->after(

            function (
                Validator $validator
            ) {

                if (

                    $this->status === Product::STATUS_ACTIVE

                    &&

                    empty(
                        $this->published_at
                    )
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'published_at',
                            'Produk aktif harus memiliki tanggal publish.'
                        );
                }

                if (

                    $this->status === Product::STATUS_DRAFT

                    &&

                    ! empty(
                        $this->published_at
                    )
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'published_at',
                            'Produk draft tidak boleh memiliki tanggal publish.'
                        );
                }
            }
        );
    }
}