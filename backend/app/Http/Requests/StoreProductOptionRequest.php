<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Str;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductOptionRequest extends FormRequest
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
            'products.update'
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

        $code = trim(
            (string) $this->input('code')
        );

        $this->merge([

            'name' => $name,

            'code' => filled($code)
                ? Str::upper($code)
                : null,

            'is_required' => $this->has('is_required')
                ? filter_var(
                    $this->input('is_required'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : true,

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

            'product_id' => [

                'bail',

                'required',

                'integer',

                Rule::exists(
                    'products',
                    'id'
                ),
            ],

            'name' => [

                'bail',

                'required',

                'string',

                'min:2',

                'max:100',

                Rule::unique(
                    'product_options'
                )
                    ->where(
                        fn ($query) => $query
                            ->where(
                                'product_id',
                                $this->input(
                                    'product_id'
                                )
                            )
                    ),
            ],

            'code' => [

                'nullable',

                'string',

                'max:50',

                Rule::unique(
                    'product_options'
                )
                    ->where(
                        fn ($query) => $query
                            ->where(
                                'product_id',
                                $this->input(
                                    'product_id'
                                )
                            )
                    ),
            ],

            'sort_order' => [

                'sometimes',

                'integer',

                'min:0',
            ],

            'is_required' => [

                'sometimes',

                'boolean',
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

            'product_id.required' =>
                'Produk wajib dipilih.',

            'product_id.exists' =>
                'Produk tidak ditemukan.',

            'name.required' =>
                'Nama opsi wajib diisi.',

            'name.min' =>
                'Nama opsi minimal 2 karakter.',

            'name.max' =>
                'Nama opsi maksimal 100 karakter.',

            'name.unique' =>
                'Nama opsi sudah digunakan pada produk ini.',

            'code.max' =>
                'Kode opsi maksimal 50 karakter.',

            'code.unique' =>
                'Kode opsi sudah digunakan pada produk ini.',

            'sort_order.integer' =>
                'Urutan tampil harus berupa angka.',

            'sort_order.min' =>
                'Urutan tampil minimal 0.',

            'is_required.boolean' =>
                'Status wajib tidak valid.',

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

            'product_id' =>
                'produk',

            'name' =>
                'nama opsi',

            'code' =>
                'kode opsi',

            'sort_order' =>
                'urutan tampil',

            'is_required' =>
                'status wajib',

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

                /*
                |--------------------------------------------------------------------------
                | Required option should remain active
                |--------------------------------------------------------------------------
                */

                if (

                    $this->boolean(
                        'is_required'
                    )

                    &&

                    $this->has(
                        'is_active'
                    )

                    &&

                    ! $this->boolean(
                        'is_active'
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'is_active',
                            'Opsi wajib harus berstatus aktif.'
                        );
                }
            }
        );
    }
}