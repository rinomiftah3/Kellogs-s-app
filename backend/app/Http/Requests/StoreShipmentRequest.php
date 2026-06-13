<?php

namespace App\Http\Requests;

use App\Models\Shipment;
use App\Models\ShippingMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * StoreShipmentRequest
 *
 * Validasi pembuatan Shipment.
 *
 * Laravel 13
 * PHP 8.4
 */
class StoreShipmentRequest extends FormRequest
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
        return $this->user()?->can('manage orders')
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

            'tracking_number' =>
                filled($this->tracking_number)
                    ? strtoupper(
                        trim((string) $this->tracking_number)
                    )
                    : null,

            'courier_name' =>
                filled($this->courier_name)
                    ? trim((string) $this->courier_name)
                    : null,

            'courier_code' =>
                filled($this->courier_code)
                    ? strtoupper(
                        trim((string) $this->courier_code)
                    )
                    : null,

            'service_name' =>
                filled($this->service_name)
                    ? trim((string) $this->service_name)
                    : null,

            'service_code' =>
                filled($this->service_code)
                    ? strtoupper(
                        trim((string) $this->service_code)
                    )
                    : null,

            'tracking_url' =>
                filled($this->tracking_url)
                    ? trim((string) $this->tracking_url)
                    : null,

            'recipient_name' =>
                filled($this->recipient_name)
                    ? trim((string) $this->recipient_name)
                    : null,

            'recipient_phone' =>
                filled($this->recipient_phone)
                    ? trim((string) $this->recipient_phone)
                    : null,

            'recipient_address' =>
                filled($this->recipient_address)
                    ? trim((string) $this->recipient_address)
                    : null,

            'recipient_city' =>
                filled($this->recipient_city)
                    ? trim((string) $this->recipient_city)
                    : null,

            'recipient_province' =>
                filled($this->recipient_province)
                    ? trim((string) $this->recipient_province)
                    : null,

            'recipient_postal_code' =>
                filled($this->recipient_postal_code)
                    ? strtoupper(
                        trim((string) $this->recipient_postal_code)
                    )
                    : null,

            'notes' =>
                filled($this->notes)
                    ? trim((string) $this->notes)
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

            'order_id' => [

                'required',

                'integer',

                'exists:orders,id',
            ],

            'shipping_method_id' => [

                'required',

                'integer',

                Rule::exists(
                    'shipping_methods',
                    'id'
                )->where(
                    'is_active',
                    true
                ),
            ],

            'tracking_number' => [

                'sometimes',

                'nullable',

                'string',

                'max:100',
            ],

            'courier_name' => [

                'required',

                'string',

                'max:100',
            ],

            'courier_code' => [

                'required',

                'string',

                'max:50',
            ],

            'service_name' => [

                'required',

                'string',

                'max:100',
            ],

            'service_code' => [

                'required',

                'string',

                'max:50',
            ],

            'tracking_url' => [

                'sometimes',

                'nullable',

                'url',

                'max:2048',
            ],

            'shipping_cost' => [

                'required',

                'numeric',

                'min:0',

                'max:999999999999.99',
            ],

            'insurance_cost' => [

                'sometimes',

                'nullable',

                'numeric',

                'min:0',

                'max:999999999999.99',
            ],

            'is_insured' => [

                'sometimes',

                'boolean',
            ],

            'weight' => [

                'required',

                'numeric',

                'min:0.01',

                'max:999999.99',
            ],

            'item_count' => [

                'required',

                'integer',

                'min:1',
            ],

            'recipient_name' => [

                'required',

                'string',

                'max:100',
            ],

            'recipient_phone' => [

                'required',

                'string',

                'max:30',
            ],

            'recipient_address' => [

                'required',

                'string',

                'max:65535',
            ],

            'recipient_city' => [

                'required',

                'string',

                'max:100',
            ],

            'recipient_province' => [

                'required',

                'string',

                'max:100',
            ],

            'recipient_postal_code' => [

                'required',

                'string',

                'max:20',
            ],

            'estimated_delivery_at' => [

                'sometimes',

                'nullable',

                'date',

                'after:today',
            ],

            'notes' => [

                'sometimes',

                'nullable',

                'string',

                'max:5000',
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

            'order_id.exists' =>
                'Pesanan yang dipilih tidak ditemukan.',

            'shipping_method_id.exists' =>
                'Metode pengiriman tidak ditemukan atau tidak aktif.',

            'estimated_delivery_at.after' =>
                'Estimasi pengiriman harus berada di masa depan.',
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

            'order_id' => 'pesanan',

            'shipping_method_id' => 'metode pengiriman',

            'tracking_number' => 'nomor resi',

            'courier_name' => 'nama kurir',

            'courier_code' => 'kode kurir',

            'service_name' => 'nama layanan',

            'service_code' => 'kode layanan',

            'tracking_url' => 'URL pelacakan',

            'shipping_cost' => 'biaya pengiriman',

            'insurance_cost' => 'biaya asuransi',

            'is_insured' => 'status asuransi',

            'weight' => 'berat paket',

            'item_count' => 'jumlah barang',

            'recipient_name' => 'nama penerima',

            'recipient_phone' => 'telepon penerima',

            'recipient_address' => 'alamat penerima',

            'recipient_city' => 'kota penerima',

            'recipient_province' => 'provinsi penerima',

            'recipient_postal_code' => 'kode pos',

            'estimated_delivery_at' => 'estimasi pengiriman',

            'notes' => 'catatan',

            'metadata' => 'metadata',
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

                    Shipment::query()

                        ->where(
                            'order_id',
                            $this->order_id
                        )

                        ->exists()

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'order_id',
                            'Pesanan ini sudah memiliki shipment.'
                        );
                }

                if (

                    $this->boolean(
                        'is_insured'
                    )

                    &&

                    (
                        ! $this->filled(
                            'insurance_cost'
                        )

                        ||

                        (float) $this->insurance_cost <= 0
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'insurance_cost',
                            'Biaya asuransi wajib diisi jika paket diasuransikan.'
                        );
                }

                if (

                    $this->filled(
                        'tracking_number'
                    )

                    &&

                    ! $this->filled(
                        'tracking_url'
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'tracking_url',
                            'URL pelacakan wajib diisi jika nomor resi tersedia.'
                        );
                }

                $shippingMethod = ShippingMethod::find(
                    $this->shipping_method_id
                );

                if (

                    $shippingMethod
                    &&
                    ! $shippingMethod->isWeightAllowed(
                        (int) ceil(
                            (float) $this->weight * 1000
                        )
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'weight',
                            'Berat paket tidak memenuhi ketentuan metode pengiriman.'
                        );
                }
            }
        );
    }
}