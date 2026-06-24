<?php

namespace App\Http\Requests;

use App\Models\CheckoutSession;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * PlaceOrderRequest
 *
 * Validates converting a checkout session
 * into an order and payment.
 *
 * Laravel 13
 * PHP 8.4
 */
class PlaceOrderRequest extends FormRequest
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
        return $this->user()?->hasRole('customer') ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([

            'gateway' => filled($this->gateway)
                ? strtolower(trim((string) $this->gateway))
                : null,

            'method' => filled($this->method)
                ? strtolower(trim((string) $this->method))
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

            'gateway' => [

                'required',

                'string',

                Rule::in([
                    'midtrans',
                    'manual',
                ]),
            ],

            'method' => [

                'required',

                'string',

                'max:50',
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

            'gateway.required' =>
                'Gateway pembayaran wajib dipilih.',

            'gateway.in' =>
                'Gateway pembayaran tidak didukung.',

            'method.required' =>
                'Metode pembayaran wajib dipilih.',

            'method.max' =>
                'Metode pembayaran maksimal 50 karakter.',
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

            'gateway' =>
                'gateway pembayaran',

            'method' =>
                'metode pembayaran',
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
                | Checkout Session
                |--------------------------------------------------------------------------
                */

                $checkoutSession = $this->route(
                    'checkout_session'
                );

                if (
                    ! $checkoutSession instanceof CheckoutSession
                ) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Ownership Validation
                |--------------------------------------------------------------------------
                */

                $customerProfileId =
                    $this->user()?->customerProfile?->id;

                if (

                    filled($customerProfileId)

                    &&

                    $checkoutSession->customer_profile_id
                        !== $customerProfileId

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'checkout_session',
                            'Checkout bukan milik Anda.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Checkout Status Validation
                |--------------------------------------------------------------------------
                */

                if (
                    $checkoutSession->isCheckedOut()
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'checkout_session',
                            'Checkout telah selesai diproses.'
                        );

                    return;
                }

                if (
                    $checkoutSession->isExpired()
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'checkout_session',
                            'Checkout telah kedaluwarsa.'
                        );

                    return;
                }

                if (

                    $checkoutSession->status
                    === CheckoutSession::STATUS_CANCELLED

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'checkout_session',
                            'Checkout telah dibatalkan.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Checkout Readiness
                |--------------------------------------------------------------------------
                */

                if (
                    ! $checkoutSession->canCheckout()
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'checkout_session',
                            'Checkout belum siap diproses.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Payment Method Validation
                |--------------------------------------------------------------------------
                */

                $gateway = $this->gateway;
                $method = $this->method;

                $allowedMethods = [

                    'midtrans' => [

                        'qris',

                        'bank_transfer',

                        'gopay',

                        'shopeepay',

                        'credit_card',
                    ],

                    'manual' => [

                        'bank_transfer',
                    ],
                ];

                if (

                    isset($allowedMethods[$gateway])

                    &&

                    ! in_array(
                        $method,
                        $allowedMethods[$gateway],
                        true
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'method',

                            'Metode pembayaran tidak tersedia untuk gateway yang dipilih.'
                        );
                }
            }
        );
    }
}