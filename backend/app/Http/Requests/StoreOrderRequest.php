<?php

namespace App\Http\Requests;

use App\Models\CheckoutSession;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * StoreOrderRequest
 *
 * Validates converting a CheckoutSession
 * into a confirmed Order.
 *
 * Laravel 13
 * PHP 8.4
 */
class StoreOrderRequest extends FormRequest
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

            'session_code' => filled($this->session_code)
                ? trim((string) $this->session_code)
                : null,

            'customer_notes' => filled($this->customer_notes)
                ? trim((string) $this->customer_notes)
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

            'session_code' => [

                'required',

                'string',

                'max:100',

                'exists:checkout_sessions,session_code',
            ],

            'customer_notes' => [

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

            'session_code.required' =>
                'Kode sesi checkout wajib diisi.',

            'session_code.exists' =>
                'Sesi checkout tidak ditemukan.',

            'customer_notes.max' =>
                'Catatan pesanan maksimal 1.000 karakter.',
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

            'session_code' =>
                'kode sesi checkout',

            'customer_notes' =>
                'catatan pesanan',
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

                $session = CheckoutSession::query()
                    ->withCount('items')
                    ->where(
                        'session_code',
                        $this->session_code
                    )
                    ->first();

                if (! $session) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Ownership
                |--------------------------------------------------------------------------
                */

                $customerProfileId =
                    $this->user()?->customerProfile?->id;

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

                /*
                |--------------------------------------------------------------------------
                | Expired Session
                |--------------------------------------------------------------------------
                */

                if ($session->isExpired()) {

                    $validator
                        ->errors()
                        ->add(
                            'session_code',
                            'Sesi checkout telah kedaluwarsa.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Already Checked Out
                |--------------------------------------------------------------------------
                */

                if ($session->isCheckedOut()) {

                    $validator
                        ->errors()
                        ->add(
                            'session_code',
                            'Sesi checkout telah digunakan untuk membuat pesanan.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Cancelled Session
                |--------------------------------------------------------------------------
                */

                if (

                    defined(
                        CheckoutSession::class .
                        '::STATUS_CANCELLED'
                    )
                    &&
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
                | Empty Checkout
                |--------------------------------------------------------------------------
                */

                if ($session->items_count <= 0) {

                    $validator
                        ->errors()
                        ->add(
                            'session_code',
                            'Checkout tidak memiliki item.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Shipping Address
                |--------------------------------------------------------------------------
                */

                if (
                    empty(
                        $session->shipping_address_id
                    )
                ) {

                    $validator
                        ->errors()
                        ->add(
                            'session_code',
                            'Alamat pengiriman belum dipilih.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Courier Validation
                |--------------------------------------------------------------------------
                */

                if (

                    empty($session->courier_code)
                    ||
                    empty($session->courier_service)

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'session_code',
                            'Metode pengiriman belum dipilih.'
                        );
                }
            }
        );
    }
}