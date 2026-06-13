<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * StoreShippingMethodRequest
 *
 * Validasi pembuatan Shipping Method.
 *
 * Laravel 13
 * PHP 8.4
 */
class StoreShippingMethodRequest extends FormRequest
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

            'service_code' =>
                filled($this->service_code)
                    ? strtoupper(
                        trim((string) $this->service_code)
                    )
                    : null,

            'service_name' =>
                filled($this->service_name)
                    ? trim((string) $this->service_name)
                    : null,

            'description' =>
                filled($this->description)
                    ? trim((string) $this->description)
                    : null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [

            'courier_id' => [

                'required',

                'integer',

                'exists:couriers,id',
            ],

            'service_code' => [

                'required',

                'string',

                'max:50',

                'alpha_dash',

                Rule::unique(
                    'shipping_methods',
                    'service_code'
                )->where(

                    fn ($query)

                        => $query->where(
                            'courier_id',
                            $this->input(
                                'courier_id'
                            )
                        )
                ),
            ],

            'service_name' => [

                'required',

                'string',

                'max:100',
            ],

            'description' => [

                'sometimes',

                'nullable',

                'string',

                'max:2000',
            ],

            'estimated_min_days' => [

                'required',

                'integer',

                'min:0',

                'max:365',
            ],

            'estimated_max_days' => [

                'required',

                'integer',

                'min:0',

                'max:365',

                'gte:estimated_min_days',
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

            'base_cost' => [

                'required',

                'numeric',

                'min:0',

                'max:999999999999.99',
            ],

            'cost_per_kg' => [

                'sometimes',

                'numeric',

                'min:0',

                'max:999999999999.99',
            ],

            'minimum_weight' => [

                'sometimes',

                'integer',

                'min:0',
            ],

            'maximum_weight' => [

                'sometimes',

                'nullable',

                'integer',

                'min:1',

                'gte:minimum_weight',
            ],

            'free_shipping_threshold' => [

                'sometimes',

                'nullable',

                'numeric',

                'min:0',

                'max:999999999999.99',
            ],

            'sla_hours' => [

                'sometimes',

                'nullable',

                'integer',

                'min:1',

                'max:720',
            ],

            'sort_order' => [

                'sometimes',

                'integer',

                'min:0',

                'max:9999',
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

            'courier_id.exists' =>
                'Kurir yang dipilih tidak ditemukan.',

            'service_code.unique' =>
                'Kode layanan sudah digunakan pada kurir ini.',

            'service_code.alpha_dash' =>
                'Kode layanan hanya boleh berisi huruf, angka, tanda hubung, dan underscore.',

            'estimated_max_days.gte' =>
                'Estimasi maksimal harus lebih besar atau sama dengan estimasi minimal.',

            'maximum_weight.gte' =>
                'Berat maksimum harus lebih besar atau sama dengan berat minimum.',
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

            'courier_id' =>
                'kurir',

            'service_code' =>
                'kode layanan',

            'service_name' =>
                'nama layanan',

            'description' =>
                'deskripsi',

            'estimated_min_days' =>
                'estimasi minimum',

            'estimated_max_days' =>
                'estimasi maksimum',

            'base_cost' =>
                'biaya dasar',

            'cost_per_kg' =>
                'biaya per kilogram',

            'minimum_weight' =>
                'berat minimum',

            'maximum_weight' =>
                'berat maksimum',

            'free_shipping_threshold' =>
                'batas gratis ongkir',

            'sla_hours' =>
                'SLA',

            'sort_order' =>
                'urutan tampilan',

            'is_featured' =>
                'status unggulan',

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

                    $this->filled(
                        'free_shipping_threshold'
                    )

                    &&

                    (float) $this->free_shipping_threshold
                    <
                    (float) $this->base_cost

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'free_shipping_threshold',
                            'Batas gratis ongkir tidak boleh lebih kecil dari biaya dasar.'
                        );
                }

                if (

                    $this->boolean(
                        'is_featured'
                    )

                    &&

                    $this->has('is_active')

                    &&

                    ! $this->boolean(
                        'is_active'
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'is_featured',
                            'Layanan unggulan harus dalam status aktif.'
                        );
                }
            }
        );
    }
}