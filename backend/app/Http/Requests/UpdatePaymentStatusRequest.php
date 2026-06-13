<?php

namespace App\Http\Requests;

use App\Models\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * UpdatePaymentStatusRequest
 *
 * Validasi perubahan status pembayaran.
 *
 * Aturan bisnis:
 * - Hanya pengguna dengan permission manage payments.
 * - Transisi status harus mengikuti alur yang diizinkan.
 * - paid_amount wajib saat status menjadi paid.
 * - refund_amount wajib saat refund.
 * - Refund tidak boleh melebihi paid amount.
 *
 * Laravel 13
 * PHP 8.4
 */
class UpdatePaymentStatusRequest extends FormRequest
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
        return $this->user()?->can('manage payments')
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

            'gateway_transaction_id' =>
                filled($this->gateway_transaction_id)
                    ? trim((string) $this->gateway_transaction_id)
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
                    Payment::STATUS_PENDING,
                    Payment::STATUS_PAID,
                    Payment::STATUS_FAILED,
                    Payment::STATUS_EXPIRED,
                    Payment::STATUS_CANCELLED,
                    Payment::STATUS_REFUNDED,
                    Payment::STATUS_PARTIAL_REFUND,
                ]),
            ],

            'paid_amount' => [

                'sometimes',

                'nullable',

                'numeric',

                'min:0.01',

                'max:99999999999.99',
            ],

            'refund_amount' => [

                'sometimes',

                'nullable',

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

            'notes' => [

                'sometimes',

                'nullable',

                'string',

                'max:1000',
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
                'Status pembayaran wajib diisi.',

            'status.in' =>
                'Status pembayaran tidak valid.',

            'paid_amount.min' =>
                'Nominal pembayaran harus lebih besar dari nol.',

            'refund_amount.min' =>
                'Nominal refund harus lebih besar dari nol.',
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
                'status pembayaran',

            'paid_amount' =>
                'nominal pembayaran',

            'refund_amount' =>
                'nominal refund',

            'gateway_transaction_id' =>
                'ID transaksi gateway',

            'notes' =>
                'catatan pembayaran',

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

                $payment = $this->route('payment');

                if (! $payment instanceof Payment) {
                    return;
                }

                $currentStatus = $payment->status;

                $newStatus = $this->input('status');

                /*
                |--------------------------------------------------------------------------
                | Allowed Status Transitions
                |--------------------------------------------------------------------------
                */

                $allowedTransitions = [

                    Payment::STATUS_PENDING => [

                        Payment::STATUS_PAID,

                        Payment::STATUS_FAILED,

                        Payment::STATUS_EXPIRED,

                        Payment::STATUS_CANCELLED,
                    ],

                    Payment::STATUS_PAID => [

                        Payment::STATUS_REFUNDED,

                        Payment::STATUS_PARTIAL_REFUND,
                    ],

                    Payment::STATUS_PARTIAL_REFUND => [

                        Payment::STATUS_REFUNDED,
                    ],

                    Payment::STATUS_FAILED => [],

                    Payment::STATUS_EXPIRED => [],

                    Payment::STATUS_CANCELLED => [],

                    Payment::STATUS_REFUNDED => [],
                ];

                if (

                    $currentStatus !== $newStatus
                    &&
                    ! in_array(
                        $newStatus,
                        $allowedTransitions[$currentStatus] ?? [],
                        true
                    )

                ) {

                    $validator
                        ->errors()
                        ->add(
                            'status',
                            "Status pembayaran tidak dapat diubah dari {$currentStatus} menjadi {$newStatus}."
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Paid Validation
                |--------------------------------------------------------------------------
                */

                if (
                    $newStatus === Payment::STATUS_PAID
                ) {

                    if (
                        ! $this->filled('paid_amount')
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'paid_amount',
                                'Nominal pembayaran wajib diisi ketika pembayaran dinyatakan lunas.'
                            );
                    }

                    if (

                        $this->filled('paid_amount')
                        &&
                        (float) $this->paid_amount
                            > (float) $payment->amount

                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'paid_amount',
                                'Nominal pembayaran tidak boleh melebihi total tagihan.'
                            );
                    }

                    if (
                        ! $this->filled('gateway_transaction_id')
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'gateway_transaction_id',
                                'ID transaksi gateway wajib diisi ketika pembayaran dinyatakan lunas.'
                            );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Refund Validation
                |--------------------------------------------------------------------------
                */

                if (
                    in_array(
                        $newStatus,
                        [
                            Payment::STATUS_REFUNDED,
                            Payment::STATUS_PARTIAL_REFUND,
                        ],
                        true
                    )
                ) {

                    if (
                        ! $this->filled('refund_amount')
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'refund_amount',
                                'Nominal refund wajib diisi.'
                            );

                        return;
                    }

                    $refundAmount =
                        (float) $this->refund_amount;

                    $paidAmount =
                        (float) $payment->paid_amount;

                    if (
                        $refundAmount > $paidAmount
                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'refund_amount',
                                'Nominal refund tidak boleh melebihi nominal pembayaran.'
                            );
                    }

                    if (

                        $newStatus === Payment::STATUS_PARTIAL_REFUND
                        &&
                        $refundAmount >= $paidAmount

                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'refund_amount',
                                'Refund parsial harus lebih kecil dari nominal pembayaran.'
                            );
                    }

                    if (

                        $newStatus === Payment::STATUS_REFUNDED
                        &&
                        $refundAmount !== $paidAmount

                    ) {

                        $validator
                            ->errors()
                            ->add(
                                'refund_amount',
                                'Refund penuh harus sama dengan nominal pembayaran.'
                            );
                    }
                }
            }
        );
    }
}