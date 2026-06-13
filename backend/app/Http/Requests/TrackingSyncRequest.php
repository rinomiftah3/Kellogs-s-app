<?php

namespace App\Http\Requests;

use App\Models\ShipmentTracking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TrackingSyncRequest extends FormRequest
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

            'tracking_code' =>
                filled($this->tracking_code)
                    ? strtoupper(
                        trim((string) $this->tracking_code)
                    )
                    : null,

            'tracking_event_code' =>
                filled($this->tracking_event_code)
                    ? strtoupper(
                        trim((string) $this->tracking_event_code)
                    )
                    : null,

            'status' =>
                filled($this->status)
                    ? trim((string) $this->status)
                    : null,

            'location' =>
                filled($this->location)
                    ? trim((string) $this->location)
                    : null,

            'city' =>
                filled($this->city)
                    ? trim((string) $this->city)
                    : null,

            'province' =>
                filled($this->province)
                    ? trim((string) $this->province)
                    : null,

            'description' =>
                filled($this->description)
                    ? trim((string) $this->description)
                    : null,

            'courier_status' =>
                filled($this->courier_status)
                    ? trim((string) $this->courier_status)
                    : null,

            'courier_code' =>
                filled($this->courier_code)
                    ? strtoupper(
                        trim((string) $this->courier_code)
                    )
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

            'shipment_id' => [

                'required',

                'integer',

                'exists:shipments,id',
            ],

            'tracking_code' => [

                'sometimes',

                'nullable',

                'string',

                'max:100',
            ],

            'tracking_event_code' => [

                'sometimes',

                'nullable',

                'string',

                'max:100',
            ],

            'status' => [

                'required',

                'string',

                'max:100',
            ],

            'location' => [

                'sometimes',

                'nullable',

                'string',

                'max:255',
            ],

            'city' => [

                'sometimes',

                'nullable',

                'string',

                'max:255',
            ],

            'province' => [

                'sometimes',

                'nullable',

                'string',

                'max:255',
            ],

            'description' => [

                'required',

                'string',

                'max:65535',
            ],

            'courier_status' => [

                'sometimes',

                'nullable',

                'string',

                'max:100',
            ],

            'courier_code' => [

                'sometimes',

                'nullable',

                'string',

                'max:50',
            ],

            'latitude' => [

                'sometimes',

                'nullable',

                'numeric',

                'between:-90,90',
            ],

            'longitude' => [

                'sometimes',

                'nullable',

                'numeric',

                'between:-180,180',
            ],

            'event_sequence' => [

                'sometimes',

                'integer',

                'min:1',
            ],

            'tracked_at' => [

                'required',

                'date',

                'before_or_equal:now',
            ],

            'is_latest' => [

                'sometimes',

                'boolean',
            ],

            'is_customer_visible' => [

                'sometimes',

                'boolean',
            ],

            'source' => [

                'sometimes',

                'string',

                Rule::in([
                    ShipmentTracking::SOURCE_SYSTEM,
                    ShipmentTracking::SOURCE_COURIER_API,
                    ShipmentTracking::SOURCE_ADMIN,
                ]),
            ],

            'payload' => [

                'sometimes',

                'nullable',

                'array',
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

            'shipment_id.required' =>
                'Shipment wajib dipilih.',

            'shipment_id.exists' =>
                'Shipment tidak ditemukan.',

            'status.required' =>
                'Status tracking wajib diisi.',

            'description.required' =>
                'Deskripsi tracking wajib diisi.',

            'tracked_at.required' =>
                'Waktu tracking wajib diisi.',

            'tracked_at.before_or_equal' =>
                'Waktu tracking tidak boleh melebihi waktu saat ini.',

            'source.in' =>
                'Sumber tracking tidak valid.',
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

            'shipment_id' =>
                'shipment',

            'tracking_code' =>
                'kode tracking',

            'tracking_event_code' =>
                'kode event tracking',

            'status' =>
                'status tracking',

            'location' =>
                'lokasi',

            'city' =>
                'kota',

            'province' =>
                'provinsi',

            'description' =>
                'deskripsi',

            'courier_status' =>
                'status kurir',

            'courier_code' =>
                'kode kurir',

            'latitude' =>
                'latitude',

            'longitude' =>
                'longitude',

            'event_sequence' =>
                'urutan event',

            'tracked_at' =>
                'waktu tracking',

            'is_latest' =>
                'event terbaru',

            'is_customer_visible' =>
                'visibilitas pelanggan',

            'source' =>
                'sumber tracking',

            'payload' =>
                'payload',

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

                /*
                |--------------------------------------------------------------------------
                | Coordinates Must Be Paired
                |--------------------------------------------------------------------------
                */

                if (

                    $this->filled('latitude')
                    xor
                    $this->filled('longitude')

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'latitude',
                            'Latitude dan longitude harus diisi bersamaan.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Courier API Requires Tracking Code
                |--------------------------------------------------------------------------
                */

                if (

                    $this->input('source')
                        === ShipmentTracking::SOURCE_COURIER_API

                    &&

                    ! $this->filled(
                        'tracking_code'
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'tracking_code',
                            'Kode tracking wajib diisi untuk event dari Courier API.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Event Sequence Must Be Unique Per Shipment
                |--------------------------------------------------------------------------
                */

                if (

                    $this->filled('shipment_id')
                    &&

                    $this->filled('event_sequence')

                    &&

                    ShipmentTracking::query()

                        ->where(
                            'shipment_id',
                            $this->shipment_id
                        )

                        ->where(
                            'event_sequence',
                            $this->event_sequence
                        )

                        ->exists()

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'event_sequence',
                            'Urutan event tracking sudah digunakan untuk shipment ini.'
                        );
                }
            }
        );
    }
}