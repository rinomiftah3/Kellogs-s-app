<?php

namespace App\Http\Requests;

use App\Models\CustomerAddress;
use App\Models\Voucher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * CreateCheckoutRequest
 *
 * Validates creating a CheckoutSession
 * from the authenticated customer's cart.
 *
 * Laravel 13
 * PHP 8.4
 */
class CreateCheckoutRequest extends FormRequest
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

            'voucher_code' => filled($this->voucher_code)
                ? strtoupper(trim((string) $this->voucher_code))
                : null,

            'courier_code' => filled($this->courier_code)
                ? strtolower(trim((string) $this->courier_code))
                : null,

            'courier_service' => filled($this->courier_service)
                ? strtoupper(trim((string) $this->courier_service))
                : null,

            'notes' => filled($this->notes)
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

            'shipping_address_id' => [

                'required',

                'integer',

                'exists:customer_addresses,id',
            ],

            'courier_code' => [

                'nullable',

                'string',

                'max:50',

                'required_with:courier_service',
            ],

            'courier_service' => [

                'nullable',

                'string',

                'max:100',

                'required_with:courier_code',
            ],

            'voucher_code' => [

                'nullable',

                'string',

                'max:100',

                'exists:vouchers,code',
            ],

            'notes' => [

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

            'shipping_address_id.required' =>
                'Alamat pengiriman wajib dipilih.',

            'shipping_address_id.exists' =>
                'Alamat pengiriman tidak ditemukan.',

            'courier_code.required_with' =>
                'Kode kurir wajib diisi.',

            'courier_service.required_with' =>
                'Layanan kurir wajib diisi.',

            'voucher_code.exists' =>
                'Kode voucher tidak ditemukan.',

            'notes.max' =>
                'Catatan maksimal 1.000 karakter.',
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

            'voucher_code' =>
                'kode voucher',

            'notes' =>
                'catatan',
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
                | Shipping Address Ownership
                |--------------------------------------------------------------------------
                */

                $customerProfileId =
                    $this->user()?->customerProfile?->id;

                if (
                    filled($customerProfileId)
                ) {

                    $address =
                        CustomerAddress::query()
                            ->find(
                                $this->shipping_address_id
                            );

                    if (

                        $address
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
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Voucher Validation
                |--------------------------------------------------------------------------
                */

                if (
                    blank($this->voucher_code)
                ) {
                    return;
                }

                $voucher =
                    Voucher::query()
                        ->where(
                            'code',
                            $this->voucher_code
                        )
                        ->first();

                if (!$voucher) {
                    return;
                }

                if (
                    !$voucher->isValid()
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'voucher_code',
                            'Voucher sudah tidak berlaku.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Voucher Per User Limit
                |--------------------------------------------------------------------------
                */

                if (
                    filled($customerProfileId)
                    &&
                    $voucher->usage_per_user > 0
                ) {

                    $usedCount =
                        $voucher
                            ->usages()
                            ->where(
                                'customer_profile_id',
                                $customerProfileId
                            )
                            ->whereIn(
                                'status',
                                [
                                    'reserved',
                                    'used',
                                ]
                            )
                            ->count();

                    if (

                        $usedCount >=
                        $voucher->usage_per_user

                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'voucher_code',
                                'Batas penggunaan voucher telah tercapai.'
                            );
                    }
                }
            }
        );
    }
}