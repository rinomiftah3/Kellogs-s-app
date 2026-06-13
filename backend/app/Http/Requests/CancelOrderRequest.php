<?php

namespace App\Http\Requests;

use App\Models\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * CancelOrderRequest
 *
 * Validates order cancellation requests.
 *
 * Rules:
 * - Customers may only cancel their own pending orders.
 * - Admins/operators with manage orders permission
 *   may cancel any non-final order.
 *
 * Laravel 13
 * PHP 8.4
 */
class CancelOrderRequest extends FormRequest
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
        $user = $this->user();

        return $user?->hasRole('customer')
            || $user?->can('manage orders')
            || false;
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([

            'reason' => filled($this->reason)
                ? trim((string) $this->reason)
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

            'reason' => [

                'sometimes',

                'nullable',

                'string',

                'max:500',
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

            'reason.max' =>
                'Alasan pembatalan maksimal 500 karakter.',
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

            'reason' =>
                'alasan pembatalan',
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

                $user = $this->user();

                /*
                |--------------------------------------------------------------------------
                | Final States
                |--------------------------------------------------------------------------
                */

                if ($order->isCancelled()) {

                    $validator
                        ->errors()
                        ->add(
                            'order',
                            'Pesanan telah dibatalkan.'
                        );

                    return;
                }

                if ($order->isCompleted()) {

                    $validator
                        ->errors()
                        ->add(
                            'order',
                            'Pesanan yang telah selesai tidak dapat dibatalkan.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Customer Ownership
                |--------------------------------------------------------------------------
                */

                if ($user?->hasRole('customer')) {

                    $customerProfileId =
                        $user->customerProfile?->id;

                    if (

                        filled($customerProfileId)
                        &&
                        $order->customer_profile_id
                            !== $customerProfileId

                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'order',
                                'Anda tidak memiliki akses untuk membatalkan pesanan ini.'
                            );

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Customer Status Restriction
                    |--------------------------------------------------------------------------
                    */

                    if (! $order->isPending()) {

                        $validator
                            ->errors()
                            ->add(
                                'order',
                                'Hanya pesanan dengan status pending yang dapat dibatalkan oleh pelanggan.'
                            );

                        return;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Admin Override
                |--------------------------------------------------------------------------
                |
                | Admin/operator dengan permission manage orders
                | diperbolehkan membatalkan order apa pun
                | selama belum completed atau cancelled.
                |
                */
            }
        );
    }
}