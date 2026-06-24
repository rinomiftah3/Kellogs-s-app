<?php

namespace App\Http\Requests;

use App\Models\ProductOptionValue;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProductOptionValueRequest extends FormRequest
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
        $this->merge([

            'value' => filled(
                $this->value
            )
                ? trim(
                    (string) $this->value
                )
                : $this->input(
                    'value'
                ),

            'code' => filled(
                $this->code
            )
                ? strtoupper(
                    trim(
                        (string) $this->code
                    )
                )
                : (
                    $this->has('code')
                        ? null
                        : $this->input(
                            'code'
                        )
                ),

            'is_active' => $this->has('is_active')
                ? filter_var(
                    $this->input('is_active'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : $this->input(
                    'is_active'
                ),
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        $optionValue = $this->route(
            'productOptionValue'
        );

        $valueId = $optionValue instanceof ProductOptionValue
            ? $optionValue->id
            : $optionValue;

        $productOptionId = $optionValue instanceof ProductOptionValue
            ? $optionValue->product_option_id
            : null;

        return [

            'value' => [

                'sometimes',

                'required',

                'string',

                'min:1',

                'max:100',

                Rule::unique(
                    'product_option_values',
                    'value'
                )
                    ->ignore(
                        $valueId
                    )
                    ->where(
                        fn ($query) => $query->where(
                            'product_option_id',
                            $productOptionId
                        )
                    ),
            ],

            'code' => [

                'sometimes',

                'nullable',

                'string',

                'max:50',

                Rule::unique(
                    'product_option_values',
                    'code'
                )
                    ->ignore(
                        $valueId
                    )
                    ->where(
                        fn ($query) => $query
                            ->where(
                                'product_option_id',
                                $productOptionId
                            )
                            ->whereNotNull(
                                'code'
                            )
                    ),
            ],

            'sort_order' => [

                'sometimes',

                'nullable',

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

            'value.required' =>
                'Nilai option wajib diisi.',

            'value.min' =>
                'Nilai option minimal 1 karakter.',

            'value.max' =>
                'Nilai option maksimal 100 karakter.',

            'value.unique' =>
                'Nilai option sudah digunakan pada option ini.',

            'code.max' =>
                'Kode maksimal 50 karakter.',

            'code.unique' =>
                'Kode sudah digunakan pada option ini.',

            'sort_order.integer' =>
                'Urutan tampil harus berupa angka.',

            'sort_order.min' =>
                'Urutan tampil minimal 0.',

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

            'value' =>
                'nilai option',

            'code' =>
                'kode',

            'sort_order' =>
                'urutan tampil',

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
                | Empty Code Validation
                |--------------------------------------------------------------------------
                */

                if (

                    $this->filled('code')

                    &&

                    trim(
                        (string) $this->code
                    ) === ''
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'code',
                            'Kode tidak boleh kosong.'
                        );
                }
            }
        );
    }
}