<?php

namespace App\Http\Requests;

use App\Models\Shipment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * UpdateShipmentStatusRequest
 *
 * Validasi perubahan status Shipment.
 *
 * Laravel 13
 * PHP 8.4
 */
class UpdateShipmentStatusRequest extends FormRequest
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

            'tracking_url' =>
                filled($this->tracking_url)
                    ? trim((string) $this->tracking_url)
                    : null,

            'received_by' =>
                filled($this->received_by)
                    ? trim((string) $this->received_by)
                    : null,

            'failed_reason' =>
                filled($this->failed_reason)
                    ? trim((string) $this->failed_reason)
                    : null,

            'return_reason' =>
                filled($this->return_reason)
                    ? trim((string) $this->return_reason)
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

            'status' => [

                'required',

                'string',

                Rule::in([
                    Shipment::STATUS_PENDING,
                    Shipment::STATUS_READY_TO_SHIP,
                    Shipment::STATUS_PICKED_UP,
                    Shipment::STATUS_IN_TRANSIT,
                    Shipment::STATUS_OUT_FOR_DELIVERY,
                    Shipment::STATUS_DELIVERED,
                    Shipment::STATUS_FAILED_DELIVERY,
                    Shipment::STATUS_RETURNED,
                    Shipment::STATUS_CANCELLED,
                ]),
            ],

            'tracking_number' => [

                'sometimes',

                'nullable',

                'string',

                'max:100',
            ],

            'tracking_url' => [

                'sometimes',

                'nullable',

                'url',

                'max:2048',
            ],

            'received_by' => [

                'sometimes',

                'nullable',

                'string',

                'max:255',
            ],

            'failed_reason' => [

                'sometimes',

                'nullable',

                'string',

                'max:65535',
            ],

            'return_reason' => [

                'sometimes',

                'nullable',

                'string',

                'max:65535',
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

            'status.required' =>
                'Status pengiriman wajib diisi.',

            'status.in' =>
                'Status pengiriman tidak valid.',
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

            'status' =>
                'status pengiriman',

            'tracking_number' =>
                'nomor resi',

            'tracking_url' =>
                'URL pelacakan',

            'received_by' =>
                'penerima',

            'failed_reason' =>
                'alasan gagal kirim',

            'return_reason' =>
                'alasan pengembalian',

            'notes' =>
                'catatan',

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

                $shipment = $this->route(
                    'shipment'
                );

                if (
                    ! $shipment instanceof Shipment
                ) {
                    return;
                }

                $currentStatus =
                    $shipment->status;

                $newStatus =
                    $this->input('status');

                /*
                |--------------------------------------------------------------------------
                | Allowed Status Transition
                |--------------------------------------------------------------------------
                */

                $allowedTransitions = [

                    Shipment::STATUS_PENDING => [

                        Shipment::STATUS_READY_TO_SHIP,

                        Shipment::STATUS_CANCELLED,
                    ],

                    Shipment::STATUS_READY_TO_SHIP => [

                        Shipment::STATUS_PICKED_UP,

                        Shipment::STATUS_CANCELLED,
                    ],

                    Shipment::STATUS_PICKED_UP => [

                        Shipment::STATUS_IN_TRANSIT,

                        Shipment::STATUS_FAILED_DELIVERY,
                    ],

                    Shipment::STATUS_IN_TRANSIT => [

                        Shipment::STATUS_OUT_FOR_DELIVERY,

                        Shipment::STATUS_FAILED_DELIVERY,

                        Shipment::STATUS_RETURNED,
                    ],

                    Shipment::STATUS_OUT_FOR_DELIVERY => [

                        Shipment::STATUS_DELIVERED,

                        Shipment::STATUS_FAILED_DELIVERY,

                        Shipment::STATUS_RETURNED,
                    ],

                    Shipment::STATUS_FAILED_DELIVERY => [

                        Shipment::STATUS_OUT_FOR_DELIVERY,

                        Shipment::STATUS_RETURNED,
                    ],

                    Shipment::STATUS_DELIVERED => [],

                    Shipment::STATUS_RETURNED => [],

                    Shipment::STATUS_CANCELLED => [],
                ];

                if (

                    $currentStatus !== $newStatus

                    &&

                    ! in_array(

                        $newStatus,

                        $allowedTransitions[
                            $currentStatus
                        ] ?? [],

                        true
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'status',
                            "Tidak dapat mengubah status shipment dari {$currentStatus} menjadi {$newStatus}."
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Tracking Validation
                |--------------------------------------------------------------------------
                */

                if (

                    in_array(

                        $newStatus,

                        [
                            Shipment::STATUS_PICKED_UP,
                            Shipment::STATUS_IN_TRANSIT,
                            Shipment::STATUS_OUT_FOR_DELIVERY,
                            Shipment::STATUS_DELIVERED,
                            Shipment::STATUS_FAILED_DELIVERY,
                            Shipment::STATUS_RETURNED,
                        ],

                        true
                    )

                    &&

                    blank(

                        $this->tracking_number
                            ?? $shipment->tracking_number
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'tracking_number',
                            'Nomor resi wajib tersedia untuk status pengiriman ini.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Delivered Validation
                |--------------------------------------------------------------------------
                */

                if (

                    $newStatus ===
                    Shipment::STATUS_DELIVERED

                    &&

                    ! $this->filled(
                        'received_by'
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'received_by',
                            'Nama penerima wajib diisi saat paket telah diterima.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Failed Delivery Validation
                |--------------------------------------------------------------------------
                */

                if (

                    $newStatus ===
                    Shipment::STATUS_FAILED_DELIVERY

                    &&

                    ! $this->filled(
                        'failed_reason'
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'failed_reason',
                            'Alasan kegagalan pengiriman wajib diisi.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Returned Validation
                |--------------------------------------------------------------------------
                */

                if (

                    $newStatus ===
                    Shipment::STATUS_RETURNED

                    &&

                    ! $this->filled(
                        'return_reason'
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'return_reason',
                            'Alasan pengembalian wajib diisi.'
                        );
                }
            }
        );
    }
}