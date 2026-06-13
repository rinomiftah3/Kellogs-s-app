<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * UpdateCourierRequest
 *
 * Validasi pembaruan data kurir.
 *
 * Laravel 13
 * PHP 8.4
 */
class UpdateCourierRequest extends FormRequest
{
    /**
     * Stop validation on first failure.
     */
    protected $stopOnFirstFailure = true;

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        return $this->user()?->can('manage couriers')
            ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([

            'name' =>
                filled($this->name)
                    ? trim((string) $this->name)
                    : $this->name,

            'code' =>
                filled($this->code)
                    ? strtolower(
                        trim((string) $this->code)
                    )
                    : $this->code,

            'provider' =>
                filled($this->provider)
                    ? trim((string) $this->provider)
                    : $this->provider,

            'description' =>
                filled($this->description)
                    ? trim((string) $this->description)
                    : $this->description,

            'website' =>
                filled($this->website)
                    ? trim((string) $this->website)
                    : $this->website,

            'contact_email' =>
                filled($this->contact_email)
                    ? strtolower(
                        trim((string) $this->contact_email)
                    )
                    : $this->contact_email,

            'contact_phone' =>
                filled($this->contact_phone)
                    ? trim((string) $this->contact_phone)
                    : $this->contact_phone,

            'tracking_url_template' =>
                filled($this->tracking_url_template)
                    ? trim((string) $this->tracking_url_template)
                    : $this->tracking_url_template,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        $courierId = $this->route('courier')?->id
            ?? $this->route('id');

        return [

            'name' => [

                'sometimes',

                'string',

                'max:100',
            ],

            'code' => [

                'sometimes',

                'string',

                'max:50',

                'alpha_dash',

                Rule::unique(
                    'couriers',
                    'code'
                )->ignore(
                    $courierId,
                    'id'
                ),
            ],

            'provider' => [

                'sometimes',

                'nullable',

                'string',

                'max:100',
            ],

            'description' => [

                'sometimes',

                'nullable',

                'string',

                'max:2000',
            ],

            'logo' => [

                'sometimes',

                'nullable',

                'file',

                'mimes:jpg,jpeg,png,webp,svg',

                'max:2048',
            ],

            'website' => [

                'sometimes',

                'nullable',

                'url',

                'max:255',
            ],

            'contact_email' => [

                'sometimes',

                'nullable',

                'email',

                'max:255',
            ],

            'contact_phone' => [

                'sometimes',

                'nullable',

                'string',

                'max:30',
            ],

            'tracking_url_template' => [

                'sometimes',

                'nullable',

                'string',

                'max:500',
            ],

            'supports_tracking' => [

                'sometimes',

                'boolean',
            ],

            'supports_cod' => [

                'sometimes',

                'boolean',
            ],

            'supports_insurance' => [

                'sometimes',

                'boolean',
            ],

            'sort_order' => [

                'sometimes',

                'integer',

                'min:0',

                'max:9999',
            ],

            'is_active' => [

                'sometimes',

                'boolean',
            ],

            'published_at' => [

                'sometimes',

                'nullable',

                'date',
            ],

            'metadata' => [

                'sometimes',

                'nullable',

                'array',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Messages
    |--------------------------------------------------------------------------
    */

    public function messages(): array
    {
        return [

            'code.unique' =>
                'Kode kurir sudah digunakan.',

            'code.alpha_dash' =>
                'Kode kurir hanya boleh berisi huruf, angka, tanda hubung, dan underscore.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Friendly Attributes
    |--------------------------------------------------------------------------
    */

    public function attributes(): array
    {
        return [

            'name' =>
                'nama kurir',

            'code' =>
                'kode kurir',

            'provider' =>
                'provider',

            'description' =>
                'deskripsi',

            'logo' =>
                'logo kurir',

            'website' =>
                'website',

            'contact_email' =>
                'email kontak',

            'contact_phone' =>
                'nomor telepon',

            'tracking_url_template' =>
                'template URL pelacakan',

            'supports_tracking' =>
                'dukungan pelacakan',

            'supports_cod' =>
                'dukungan COD',

            'supports_insurance' =>
                'dukungan asuransi',

            'sort_order' =>
                'urutan tampilan',

            'is_active' =>
                'status aktif',

            'published_at' =>
                'tanggal publikasi',

            'metadata' =>
                'metadata',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Validation
    |--------------------------------------------------------------------------
    */

    public function withValidator(
        Validator $validator
    ): void {

        $validator->after(

            function (
                Validator $validator
            ) {

                if (
                    filled(
                        $this->tracking_url_template
                    )
                    &&
                    ! str_contains(
                        $this->tracking_url_template,
                        '{tracking_number}'
                    )
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'tracking_url_template',
                            'Template URL pelacakan harus mengandung placeholder {tracking_number}.'
                        );
                }
            }
        );
    }
}