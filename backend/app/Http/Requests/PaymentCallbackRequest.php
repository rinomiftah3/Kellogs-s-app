<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * PaymentCallbackRequest
 *
 * Validasi callback/webhook dari payment gateway.
 *
 * Endpoint callback bersifat publik dan tidak memerlukan autentikasi.
 * Validasi signature, IP whitelist, serta proses bisnis callback
 * dilakukan pada PaymentCallbackService.
 *
 * Laravel 13
 * PHP 8.4
 */
class PaymentCallbackRequest extends FormRequest
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
        /*
        |----------------------------------------------------------------------
        | Endpoint callback dapat diakses tanpa login.
        | Verifikasi signature dilakukan pada service layer.
        |----------------------------------------------------------------------
        */

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([

            'gateway' =>
                filled($this->gateway)
                    ? trim((string) $this->gateway)
                    : null,

            'event_type' =>
                filled($this->event_type)
                    ? trim((string) $this->event_type)
                    : null,

            'gateway_transaction_id' =>
                filled($this->gateway_transaction_id)
                    ? trim((string) $this->gateway_transaction_id)
                    : null,

            'gateway_order_id' =>
                filled($this->gateway_order_id)
                    ? trim((string) $this->gateway_order_id)
                    : null,

            'signature' =>
                filled($this->signature)
                    ? trim((string) $this->signature)
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
                    'xendit',
                    'tripay',
                ]),
            ],

            'event_type' => [

                'required',

                'string',

                'max:100',
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

            'signature' => [

                'sometimes',

                'nullable',

                'string',

                'max:512',
            ],

            'payload' => [

                'required',

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

            'gateway.required' =>
                'Gateway pembayaran wajib diisi.',

            'gateway.in' =>
                'Gateway pembayaran tidak valid.',

            'event_type.required' =>
                'Jenis event callback wajib diisi.',

            'payload.required' =>
                'Payload callback wajib diisi.',

            'payload.array' =>
                'Payload callback harus berupa array.',
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

            'event_type' =>
                'jenis event',

            'gateway_transaction_id' =>
                'ID transaksi gateway',

            'gateway_order_id' =>
                'ID order gateway',

            'signature' =>
                'signature callback',

            'payload' =>
                'payload callback',
        ];
    }
}