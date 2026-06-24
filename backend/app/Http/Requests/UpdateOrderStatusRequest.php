<?php

namespace App\Http\Requests;

use App\Models\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * UpdateOrderStatusRequest
 *
 * Validates updating an order status
 * by admin/operator users.
 *
 * Laravel 13
 * PHP 8.4
 */
class UpdateOrderStatusRequest extends FormRequest
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
        return $this->user()?->can('orders.update') ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([

            'status' => filled($this->status)
                ? trim((string) $this->status)
                : null,

            'fulfillment_status' => filled($this->fulfillment_status)
                ? trim((string) $this->fulfillment_status)
                : null,

            'admin_notes' => filled($this->admin_notes)
                ? trim((string) $this->admin_notes)
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
                    Order::STATUS_PENDING,
                    Order::STATUS_CONFIRMED,
                    Order::STATUS_PROCESSING,
                    Order::STATUS_SHIPPED,
                    Order::STATUS_COMPLETED,
                    Order::STATUS_CANCELLED,
                ]),
            ],

            'fulfillment_status' => [

                'sometimes',

                'nullable',

                'string',

                Rule::in([
                    Order::FULFILLMENT_PENDING,
                    Order::FULFILLMENT_PACKED,
                    Order::FULFILLMENT_SHIPPED,
                    Order::FULFILLMENT_DELIVERED,
                ]),
            ],

            'admin_notes' => [

                'sometimes',

                'nullable',

                'string',

                'max:1000',
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
                'Status pesanan wajib diisi.',

            'status.in' =>
                'Status pesanan tidak valid.',

            'fulfillment_status.in' =>
                'Status pemenuhan pesanan tidak valid.',

            'admin_notes.max' =>
                'Catatan admin maksimal 1.000 karakter.',
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
                'status pesanan',

            'fulfillment_status' =>
                'status pemenuhan',

            'admin_notes' =>
                'catatan admin',
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

                $order = $this->route('order');

                if (! $order instanceof Order) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Final States
                |--------------------------------------------------------------------------
                */

                if ($order->isCancelled()) {

                    $validator
                        ->errors()
                        ->add(
                            'status',
                            'Pesanan yang telah dibatalkan tidak dapat diubah.'
                        );

                    return;
                }

                if ($order->isCompleted()) {

                    $validator
                        ->errors()
                        ->add(
                            'status',
                            'Pesanan yang telah selesai tidak dapat diubah.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Allowed Status Transition
                |--------------------------------------------------------------------------
                */

                $allowedTransitions = [

                    Order::STATUS_PENDING => [
                        Order::STATUS_CONFIRMED,
                        Order::STATUS_CANCELLED,
                    ],

                    Order::STATUS_CONFIRMED => [
                        Order::STATUS_PROCESSING,
                        Order::STATUS_CANCELLED,
                    ],

                    Order::STATUS_PROCESSING => [
                        Order::STATUS_SHIPPED,
                        Order::STATUS_CANCELLED,
                    ],

                    Order::STATUS_SHIPPED => [
                        Order::STATUS_COMPLETED,
                    ],
                ];

                $currentStatus = $order->status;
                $newStatus = $this->status;

                if ($newStatus !== $currentStatus) {

                    $allowed =
                        $allowedTransitions[$currentStatus]
                        ?? [];

                    if (! in_array($newStatus, $allowed, true)) {

                        $validator
                            ->errors()
                            ->add(
                                'status',
                                sprintf(
                                    'Perubahan status dari "%s" ke "%s" tidak diperbolehkan.',
                                    $currentStatus,
                                    $newStatus
                                )
                            );

                        return;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Fulfillment Consistency
                |--------------------------------------------------------------------------
                */

                if ($this->filled('fulfillment_status')) {

                    $map = [

                        Order::STATUS_PENDING =>
                            Order::FULFILLMENT_PENDING,

                        Order::STATUS_CONFIRMED =>
                            Order::FULFILLMENT_PENDING,

                        Order::STATUS_PROCESSING =>
                            Order::FULFILLMENT_PACKED,

                        Order::STATUS_SHIPPED =>
                            Order::FULFILLMENT_SHIPPED,

                        Order::STATUS_COMPLETED =>
                            Order::FULFILLMENT_DELIVERED,
                    ];

                    if (
                        isset($map[$newStatus])
                        &&
                        $this->fulfillment_status !== $map[$newStatus]
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'fulfillment_status',
                                'Status pemenuhan tidak sesuai dengan status pesanan.'
                            );
                    }
                }
            }
        );
    }
}