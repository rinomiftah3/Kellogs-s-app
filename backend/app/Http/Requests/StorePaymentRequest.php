<?php

namespace App\Http\Requests;

use App\Models\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * StorePaymentRequest
 *
 * Validasi pembuatan data pembayaran untuk pesanan.
 *
 * Aturan bisnis:
 * - Hanya admin/operator dengan permission manage payments.
 * - Satu order hanya boleh memiliki satu payment.
 * - Nominal pembayaran tidak boleh melebihi grand total order.
 * - Gateway dan metode pembayaran harus menggunakan nilai yang didukung sistem.
 *
 * Laravel 13
 * PHP 8.4
 */
class StorePaymentRequest extends FormRequest
{
    /**
     * Hentikan validasi pada kegagalan pertama.
     */
    protected $stopOnFirstFailure = true;

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        return $this->user()?->can('manage payments') ?? false;
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

            'gateway_transaction_id' => filled($this->gateway_transaction_id)
                ? trim((string) $this->gateway_transaction_id)
                : null,

            'gateway_order_id' => filled($this->gateway_order_id)
                ? trim((string) $this->gateway_order_id)
                : null,

            'payment_url' => filled($this->payment_url)
                ? trim((string) $this->payment_url)
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

            'order_id' => [

                'required',

                'integer',

                'exists:orders,id',
            ],

            'gateway' => [

                'required',

                'string',

                Rule::in([
                    'midtrans',
                    'xendit',
                    'tripay',
                ]),
            ],

            'method' => [

                'required',

                'string',

                Rule::in([
                    'bank_transfer',
                    'virtual_account',
                    'ewallet',
                    'qris',
                    'credit_card',
                ]),
            ],

            'amount' => [

                'required',

                'numeric',

                'min:0.01',

                'max:99999999999.99',
            ],

            'gateway_transaction_id' => [

                'sometimes',

                'nullable',

                'string',

                'max:255',
            ],

            'gateway_order_id' => [

                'sometimes',

                'nullable',

                'string',

                'max:255',
            ],

            'payment_url' => [

                'sometimes',

                'nullable',

                'url',

                'max:2048',
            ],

            'expired_at' => [

                'sometimes',

                'nullable',

                'date',

                'after:now',
            ],

            'metadata' => [

                'sometimes',

                'nullable',

                'array',
            ],

            'notes' => [

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

            'order_id.required' =>
                'Pesanan wajib dipilih.',

            'order_id.exists' =>
                'Pesanan yang dipilih tidak ditemukan.',

            'gateway.in' =>
                'Gateway pembayaran tidak didukung.',

            'method.in' =>
                'Metode pembayaran tidak didukung.',

            'amount.min' =>
                'Nominal pembayaran harus lebih besar dari nol.',

            'expired_at.after' =>
                'Tanggal kedaluwarsa pembayaran harus di masa depan.',

            'payment_url.url' =>
                'URL pembayaran tidak valid.',
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

            'order_id' =>
                'pesanan',

            'gateway' =>
                'gateway pembayaran',

            'method' =>
                'metode pembayaran',

            'amount' =>
                'nominal pembayaran',

            'gateway_transaction_id' =>
                'ID transaksi gateway',

            'gateway_order_id' =>
                'ID order gateway',

            'payment_url' =>
                'URL pembayaran',

            'expired_at' =>
                'masa berlaku pembayaran',

            'notes' =>
                'catatan pembayaran',
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

                $order = Order::find(
                    $this->integer('order_id')
                );

                if (! $order) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | One Order = One Payment
                |--------------------------------------------------------------------------
                */

                if ($order->payment()->exists()) {

                    $validator
                        ->errors()
                        ->add(
                            'order_id',
                            'Pesanan ini sudah memiliki pembayaran.'
                        );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Amount Validation
                |--------------------------------------------------------------------------
                */

                if (

                    (float) $this->amount
                    >
                    (float) $order->grand_total

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'amount',
                            'Nominal pembayaran tidak boleh melebihi total pesanan.'
                        );
                }
            }
        );
    }
}