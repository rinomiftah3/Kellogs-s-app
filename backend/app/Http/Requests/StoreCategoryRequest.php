<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Str;

use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
            'categories.create'
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

        $slug = trim(
            (string) $this->input('slug')
        );

        $this->merge([

            'name' => $name,

            'slug' => filled($slug)
                ? Str::slug($slug)
                : (
                    filled($name)
                        ? Str::slug($name)
                        : null
                ),

            'description' => filled(
                $this->description
            )
                ? trim(
                    (string) $this->description
                )
                : null,

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

            'parent_id' => [

                'nullable',

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

            'slug' => [

                'bail',

                'required',

                'string',

                'max:255',

                Rule::unique(
                    'categories',
                    'slug'
                ),
            ],

            'description' => [

                'nullable',

                'string',
            ],

            'image' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:2048',
            ],

            'sort_order' => [

                'sometimes',

                'integer',

                'min:0',
            ],

            'is_active' => [

                'sometimes',

                'boolean',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'parent_id.exists' =>
                'Kategori induk tidak ditemukan.',

            'name.required' =>
                'Nama kategori wajib diisi.',

            'name.min' =>
                'Nama kategori minimal 2 karakter.',

            'name.max' =>
                'Nama kategori maksimal 255 karakter.',

            'slug.required' =>
                'Slug kategori wajib diisi.',

            'slug.unique' =>
                'Slug kategori sudah digunakan.',

            'slug.max' =>
                'Slug kategori maksimal 255 karakter.',

            'image.image' =>
                'File harus berupa gambar.',

            'image.mimes' =>
                'Format gambar harus JPG, JPEG, PNG, atau WEBP.',

            'image.max' =>
                'Ukuran gambar maksimal 2 MB.',

            'sort_order.integer' =>
                'Urutan kategori harus berupa angka.',

            'sort_order.min' =>
                'Urutan kategori minimal 0.',

            'is_active.boolean' =>
                'Status aktif tidak valid.',
        ];
    }

    /**
     * Friendly attribute names.
     */
    public function attributes(): array
    {
        return [

            'parent_id' =>
                'kategori induk',

            'name' =>
                'nama kategori',

            'slug' =>
                'slug',

            'description' =>
                'deskripsi',

            'image' =>
                'gambar',

            'sort_order' =>
                'urutan',

            'is_active' =>
                'status aktif',
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
                    $this->filled('parent_id')
                    &&
                    $this->parent_id ==
                    $this->route('category')?->id
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'parent_id',
                            'Kategori tidak boleh menjadi induknya sendiri.'
                        );
                }
            }
        );
    }
}