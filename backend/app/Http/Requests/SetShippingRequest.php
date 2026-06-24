<?php

namespace App\Http\Requests;

use App\Models\CheckoutSession;
use App\Models\CustomerAddress;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * SetShippingRequest
 *
 * Validates shipping information
 * for an existing checkout session.
 *
 * Laravel 13
 * PHP 8.4
 */
class SetShippingRequest extends FormRequest
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

            'courier_code' => filled($this->courier_code)
                ? strtolower(trim((string) $this->courier_code))
                : null,

            'courier_service' => filled($this->courier_service)
                ? strtoupper(trim((string) $this->courier_service))
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

            'shipping_address_id' => [

                'required',

                'integer',

                'exists:customer_addresses,id',
            ],

            'courier_code' => [

                'required',

                'string',

                'max:50',
            ],

            'courier_service' => [

                'required',

                'string',

                'max:100',
            ],

            'shipping_cost' => [

                'required',

                'numeric',

                'min:0',
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

            'shipping_address_id.required' =>
                'Alamat pengiriman wajib dipilih.',

            'shipping_address_id.exists' =>
                'Alamat pengiriman tidak ditemukan.',

            'courier_code.required' =>
                'Kode kurir wajib diisi.',

            'courier_service.required' =>
                'Layanan kurir wajib diisi.',

            'shipping_cost.required' =>
                'Biaya pengiriman wajib diisi.',

            'shipping_cost.numeric' =>
                'Biaya pengiriman harus berupa angka.',

            'shipping_cost.min' =>
                'Biaya pengiriman tidak boleh negatif.',
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

            'shipping_address_id' =>
                'alamat pengiriman',

            'courier_code' =>
                'kode kurir',

            'courier_service' =>
                'layanan kurir',

            'shipping_cost' =>
                'biaya pengiriman',
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
                | Checkout Status
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
                | Address Ownership Validation
                |--------------------------------------------------------------------------
                */

                $address = CustomerAddress::query()
                    ->find(
                        $this->shipping_address_id
                    );

                if (
                    ! $address
                ) {
                    return;
                }

                if (

                    filled($customerProfileId)

                    &&

                    $address->customer_profile_id
                        !== $customerProfileId

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'shipping_address_id',
                            'Alamat pengiriman bukan milik Anda.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Address Status Validation
                |--------------------------------------------------------------------------
                */

                if (
                    ! $address->isActive()
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'shipping_address_id',
                            'Alamat pengiriman sedang tidak aktif.'
                        );
                }
            }
        );
    }
}