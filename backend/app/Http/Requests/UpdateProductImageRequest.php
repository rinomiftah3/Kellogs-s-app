<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Validator;

class UpdateProductImageRequest extends FormRequest
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

            'alt_text' => filled(
                $this->alt_text
            )
                ? trim(
                    (string) $this->alt_text
                )
                : null,

            'is_primary' => $this->has('is_primary')
                ? filter_var(
                    $this->input('is_primary'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : $this->input('is_primary'),

            'is_active' => $this->has('is_active')
                ? filter_var(
                    $this->input('is_active'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                )
                : $this->input('is_active'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [

            'image' => [

                'sometimes',

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:2048',
            ],

            'alt_text' => [

                'sometimes',

                'nullable',

                'string',

                'max:255',
            ],

            'sort_order' => [

                'sometimes',

                'nullable',

                'integer',

                'min:0',
            ],

            'is_primary' => [

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

            'image.image' =>
                'File harus berupa gambar.',

            'image.mimes' =>
                'Format gambar harus JPG, JPEG, PNG, atau WEBP.',

            'image.max' =>
                'Ukuran gambar maksimal 2 MB.',

            'alt_text.max' =>
                'Alt text maksimal 255 karakter.',

            'sort_order.integer' =>
                'Urutan tampil harus berupa angka.',

            'sort_order.min' =>
                'Urutan tampil minimal 0.',

            'is_primary.boolean' =>
                'Status primary tidak valid.',

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

            'image' =>
                'gambar',

            'alt_text' =>
                'alt text',

            'sort_order' =>
                'urutan tampil',

            'is_primary' =>
                'gambar utama',

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

                    $this->boolean('is_primary')

                    &&

                    $this->has('is_active')

                    &&

                    ! $this->boolean('is_active')
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'is_active',
                            'Gambar utama harus berstatus aktif.'
                        );
                }
            }
        );
    }
}