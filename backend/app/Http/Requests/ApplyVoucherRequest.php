<?php

namespace App\Http\Requests;

use App\Models\CheckoutSession;
use App\Models\Voucher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * ApplyVoucherRequest
 *
 * Validates applying a voucher
 * to an active checkout session.
 *
 * Laravel 13
 * PHP 8.4
 */
class ApplyVoucherRequest extends FormRequest
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

            'session_code' => filled($this->session_code)
                ? strtoupper(trim((string) $this->session_code))
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

            'voucher_code' => [

                'required',

                'string',

                'max:100',

                'exists:vouchers,code',
            ],

            'session_code' => [

                'required',

                'string',

                'max:100',

                'exists:checkout_sessions,session_code',
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

            'voucher_code.required' =>
                'Kode voucher wajib diisi.',

            'voucher_code.exists' =>
                'Voucher tidak ditemukan.',

            'session_code.required' =>
                'Sesi checkout wajib dipilih.',

            'session_code.exists' =>
                'Sesi checkout tidak ditemukan.',
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

            'voucher_code' =>
                'kode voucher',

            'session_code' =>
                'sesi checkout',
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

                $customerProfileId =
                    $this->user()?->customerProfile?->id;

                /*
                |--------------------------------------------------------------------------
                | Checkout Session Validation
                |--------------------------------------------------------------------------
                */

                $session = CheckoutSession::query()

                    ->where(
                        'session_code',
                        $this->session_code
                    )

                    ->first();

                if (!$session) {
                    return;
                }

                if (

                    filled($customerProfileId)
                    &&
                    $session->customer_profile_id
                        !== $customerProfileId

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'session_code',
                            'Sesi checkout bukan milik Anda.'
                        );

                    return;
                }

                if ($session->isExpired()) {

                    $validator
                        ->errors()
                        ->add(
                            'session_code',
                            'Sesi checkout telah kedaluwarsa.'
                        );

                    return;
                }

                if ($session->isCheckedOut()) {

                    $validator
                        ->errors()
                        ->add(
                            'session_code',
                            'Checkout telah selesai diproses.'
                        );

                    return;
                }

                if (
                    $session->status
                    === CheckoutSession::STATUS_CANCELLED
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'session_code',
                            'Checkout telah dibatalkan.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Voucher Validation
                |--------------------------------------------------------------------------
                */

                $voucher = Voucher::query()

                    ->where(
                        'code',
                        $this->voucher_code
                    )

                    ->first();

                if (!$voucher) {
                    return;
                }

                if (!$voucher->isValid()) {

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
                | Minimum Purchase
                |--------------------------------------------------------------------------
                */

                if (

                    !$voucher->meetsMinimumPurchase(
                        (float) $session->subtotal
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'voucher_code',
                            'Minimum pembelian belum terpenuhi.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Usage Per User
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

                        return;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Stackable Validation
                |--------------------------------------------------------------------------
                */

                if (

                    filled($session->voucher_code)
                    &&
                    !$voucher->isStackable()

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'voucher_code',
                            'Voucher tidak dapat digabungkan dengan voucher lain.'
                        );
                }
            }
        );
    }
}