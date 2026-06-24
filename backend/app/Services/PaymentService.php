<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentCallback;
use App\Models\PaymentTransaction;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Default relationships.
     */
    protected array $relations = [
        'order',
        'transactions',
        'callbacks',
    ];

    /**
     * Get paginated payments.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return Payment::query()

            ->with($this->relations)

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            ->when(
                filled($filters['status'] ?? null),
                fn ($query) => $query->status(
                    $filters['status']
                )
            )

            ->when(
                filled($filters['gateway'] ?? null),
                fn ($query) => $query->gateway(
                    $filters['gateway']
                )
            )

            ->latest()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all payments.
     */
    public function all(
        array $filters = []
    ): Collection {

        return Payment::query()

            ->with($this->relations)

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            ->when(
                filled($filters['status'] ?? null),
                fn ($query) => $query->status(
                    $filters['status']
                )
            )

            ->when(
                filled($filters['gateway'] ?? null),
                fn ($query) => $query->gateway(
                    $filters['gateway']
                )
            )

            ->latest()

            ->get();
    }

    /**
     * Find payment by ID.
     */
    public function find(
        int $id
    ): ?Payment {

        return Payment::query()

            ->with($this->relations)

            ->find($id);
    }

    /**
     * Find payment or fail.
     */
    public function findOrFail(
        int $id
    ): Payment {

        return Payment::query()

            ->with($this->relations)

            ->findOrFail($id);
    }

    /**
     * Find payment by payment number.
     */
    public function findByPaymentNumber(
        string $paymentNumber
    ): ?Payment {

        return Payment::query()

            ->with($this->relations)

            ->where(
                'payment_number',
                trim($paymentNumber)
            )

            ->first();
    }

    /**
     * Find payment by order.
     */
    public function findByOrder(
        Order|int $order
    ): ?Payment {

        $orderId = $order instanceof Order
            ? $order->id
            : $order;

        return Payment::query()

            ->with($this->relations)

            ->where(
                'order_id',
                $orderId
            )

            ->first();
    }
    /**
     * Create payment.
     */
    public function create(
        array $data
    ): Payment {

        return DB::transaction(
            function () use ($data) {

                if (
                    Payment::query()
                        ->where(
                            'order_id',
                            $data['order_id']
                        )
                        ->exists()
                ) {
                    throw ValidationException::withMessages([

                        'order_id' => [
                            'Order sudah memiliki pembayaran.',
                        ],
                    ]);
                }

                $payment = Payment::create([

                    'order_id'
                        => $data['order_id'],

                    'payment_number'
                        => $data['payment_number'],

                    'gateway'
                        => $data['gateway'],

                    'method'
                        => $data['method'],

                    'amount'
                        => $data['amount'],

                    'paid_amount'
                        => $data['paid_amount']
                        ?? 0,

                    'fee_amount'
                        => $data['fee_amount']
                        ?? 0,

                    'refund_amount'
                        => $data['refund_amount']
                        ?? 0,

                    'status'
                        => $data['status']
                        ?? Payment::STATUS_PENDING,

                    'gateway_transaction_id'
                        => $data['gateway_transaction_id']
                        ?? null,

                    'gateway_order_id'
                        => $data['gateway_order_id']
                        ?? null,

                    'payment_url'
                        => $data['payment_url']
                        ?? null,

                    'paid_at'
                        => $data['paid_at']
                        ?? null,

                    'expired_at'
                        => $data['expired_at']
                        ?? null,

                    'metadata'
                        => $data['metadata']
                        ?? null,

                    'notes'
                        => $data['notes']
                        ?? null,
                ]);

                return $payment
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }

    /**
     * Update payment.
     */
    public function update(
        Payment|int $payment,
        array $data
    ): Payment {

        return DB::transaction(
            function () use (
                $payment,
                $data
            ) {

                $payment = $payment instanceof Payment
                    ? $payment
                    : $this->findOrFail(
                        $payment
                    );

                $payment->update([

                    'gateway'
                        => $data['gateway']
                        ?? $payment->gateway,

                    'method'
                        => $data['method']
                        ?? $payment->method,

                    'amount'
                        => $data['amount']
                        ?? $payment->amount,

                    'paid_amount'
                        => $data['paid_amount']
                        ?? $payment->paid_amount,

                    'fee_amount'
                        => $data['fee_amount']
                        ?? $payment->fee_amount,

                    'refund_amount'
                        => $data['refund_amount']
                        ?? $payment->refund_amount,

                    'status'
                        => $data['status']
                        ?? $payment->status,

                    'gateway_transaction_id'
                        => array_key_exists(
                            'gateway_transaction_id',
                            $data
                        )
                            ? $data['gateway_transaction_id']
                            : $payment->gateway_transaction_id,

                    'gateway_order_id'
                        => array_key_exists(
                            'gateway_order_id',
                            $data
                        )
                            ? $data['gateway_order_id']
                            : $payment->gateway_order_id,

                    'payment_url'
                        => array_key_exists(
                            'payment_url',
                            $data
                        )
                            ? $data['payment_url']
                            : $payment->payment_url,

                    'paid_at'
                        => array_key_exists(
                            'paid_at',
                            $data
                        )
                            ? $data['paid_at']
                            : $payment->paid_at,

                    'expired_at'
                        => array_key_exists(
                            'expired_at',
                            $data
                        )
                            ? $data['expired_at']
                            : $payment->expired_at,

                    'metadata'
                        => array_key_exists(
                            'metadata',
                            $data
                        )
                            ? $data['metadata']
                            : $payment->metadata,

                    'notes'
                        => array_key_exists(
                            'notes',
                            $data
                        )
                            ? $data['notes']
                            : $payment->notes,
                ]);

                return $payment
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }

    /**
     * Delete payment.
     */
    public function delete(
        Payment|int $payment
    ): bool {

        return DB::transaction(
            function () use ($payment) {

                $payment = $payment instanceof Payment
                    ? $payment
                    : $this->findOrFail(
                        $payment
                    );

                return (bool)
                    $payment->delete();
            }
        );
    }
    /**
     * Mark payment as paid.
     */
    public function markAsPaid(
        Payment|int $payment,
        ?float $paidAmount = null
    ): Payment {

        return DB::transaction(
            function () use (
                $payment,
                $paidAmount
            ) {

                $payment = $payment instanceof Payment
                    ? $payment
                    : $this->findOrFail(
                        $payment
                    );

                $payment->markAsPaid(
                    $paidAmount
                );

                return $payment
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(
        Payment|int $payment
    ): Payment {

        return DB::transaction(
            function () use ($payment) {

                $payment = $payment instanceof Payment
                    ? $payment
                    : $this->findOrFail(
                        $payment
                    );

                $payment->markAsFailed();

                return $payment
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }

    /**
     * Mark payment as expired.
     */
    public function markAsExpired(
        Payment|int $payment
    ): Payment {

        return DB::transaction(
            function () use ($payment) {

                $payment = $payment instanceof Payment
                    ? $payment
                    : $this->findOrFail(
                        $payment
                    );

                $payment->markAsExpired();

                return $payment
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }

    /**
     * Mark payment as cancelled.
     */
    public function markAsCancelled(
        Payment|int $payment
    ): Payment {

        return DB::transaction(
            function () use ($payment) {

                $payment = $payment instanceof Payment
                    ? $payment
                    : $this->findOrFail(
                        $payment
                    );

                $payment->markAsCancelled();

                return $payment
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }
    /**
     * Create payment transaction.
     */
    public function createTransaction(
        Payment|int $payment,
        array $data
    ): PaymentTransaction {

        return DB::transaction(
            function () use (
                $payment,
                $data
            ) {

                $payment = $payment instanceof Payment
                    ? $payment
                    : $this->findOrFail(
                        $payment
                    );

                $transaction = PaymentTransaction::create([

                    'payment_id'
                        => $payment->id,

                    'transaction_id'
                        => $data['transaction_id']
                        ?? $this->generateTransactionId(),

                    'gateway_transaction_id'
                        => $data['gateway_transaction_id']
                        ?? null,

                    'gateway_order_id'
                        => $data['gateway_order_id']
                        ?? null,

                    'gateway'
                        => $data['gateway']
                        ?? $payment->gateway,

                    'method'
                        => $data['method']
                        ?? $payment->method,

                    'type'
                        => $data['type']
                        ?? PaymentTransaction::TYPE_PAYMENT,

                    'amount'
                        => $data['amount'],

                    'fee_amount'
                        => $data['fee_amount']
                        ?? 0,

                    'net_amount'
                        => $data['net_amount']
                        ?? (
                            (float) $data['amount']
                            - (float) ($data['fee_amount'] ?? 0)
                        ),

                    'status'
                        => $data['status']
                        ?? PaymentTransaction::STATUS_PENDING,

                    'reference_number'
                        => $data['reference_number']
                        ?? null,

                    'request_payload'
                        => $data['request_payload']
                        ?? null,

                    'response_payload'
                        => $data['response_payload']
                        ?? null,

                    'notes'
                        => $data['notes']
                        ?? null,

                    'processed_at'
                        => $data['processed_at']
                        ?? null,

                    'metadata'
                        => $data['metadata']
                        ?? null,
                ]);

                return $transaction
                    ->fresh()
                    ->load('payment');
            }
        );
    }

    /**
     * Mark transaction as success.
     */
    public function markTransactionSuccess(
        PaymentTransaction|int $transaction
    ): PaymentTransaction {

        return DB::transaction(
            function () use ($transaction) {

                $transaction =
                    $this->findTransactionOrFail(
                        $transaction
                    );

                $transaction->markSuccess();

                return $transaction
                    ->fresh()
                    ->load('payment');
            }
        );
    }

    /**
     * Mark transaction as failed.
     */
    public function markTransactionFailed(
        PaymentTransaction|int $transaction
    ): PaymentTransaction {

        return DB::transaction(
            function () use ($transaction) {

                $transaction =
                    $this->findTransactionOrFail(
                        $transaction
                    );

                $transaction->markFailed();

                return $transaction
                    ->fresh()
                    ->load('payment');
            }
        );
    }

    /**
     * Mark transaction as cancelled.
     */
    public function markTransactionCancelled(
        PaymentTransaction|int $transaction
    ): PaymentTransaction {

        return DB::transaction(
            function () use ($transaction) {

                $transaction =
                    $this->findTransactionOrFail(
                        $transaction
                    );

                $transaction->markCancelled();

                return $transaction
                    ->fresh()
                    ->load('payment');
            }
        );
    }

    /**
     * Mark transaction as expired.
     */
    public function markTransactionExpired(
        PaymentTransaction|int $transaction
    ): PaymentTransaction {

        return DB::transaction(
            function () use ($transaction) {

                $transaction =
                    $this->findTransactionOrFail(
                        $transaction
                    );

                $transaction->markExpired();

                return $transaction
                    ->fresh()
                    ->load('payment');
            }
        );
    }
/**
 * Find transaction by gateway transaction ID.
 */
public function findTransactionByGatewayTransactionId(
    string $gatewayTransactionId
): ?PaymentTransaction {

    return PaymentTransaction::query()

        ->with('payment')

        ->where(
            'gateway_transaction_id',
            trim($gatewayTransactionId)
        )

        ->first();
}

/**
 * Check whether gateway transaction
 * has already been processed.
 */
public function hasProcessedGatewayTransaction(
    ?string $gatewayTransactionId
): bool {

    if (blank($gatewayTransactionId)) {
        return false;
    }

    return PaymentTransaction::query()

        ->where(
            'gateway_transaction_id',
            trim($gatewayTransactionId)
        )

        ->where(
            'status',
            PaymentTransaction::STATUS_SUCCESS
        )

        ->exists();
}
/**
 * Ensure gateway transaction
 * has not been processed.
 */
public function ensureTransactionNotProcessed(
    ?string $gatewayTransactionId
): void {

    if (
        $this->hasProcessedGatewayTransaction(
            $gatewayTransactionId
        )
    ) {

        throw ValidationException::withMessages([

            'transaction' => [

                'Transaksi gateway sudah pernah diproses.',
            ],
        ]);
    }
}
    /**
     * Find transaction or fail.
     */
    protected function findTransactionOrFail(
        PaymentTransaction|int $transaction
    ): PaymentTransaction {

        return $transaction instanceof PaymentTransaction

            ? $transaction

            : PaymentTransaction::query()

                ->with('payment')

                ->findOrFail(
                    $transaction
                );
    }
    /**
     * Create payment callback.
     */
    public function createCallback(
        Payment|int $payment,
        array $data
    ): PaymentCallback {

        return DB::transaction(
            function () use (
                $payment,
                $data
            ) {

                $payment = $payment instanceof Payment
                    ? $payment
                    : $this->findOrFail(
                        $payment
                    );

                $callback = PaymentCallback::create([

                    'payment_id'
                        => $payment->id,

                    'gateway'
                        => $data['gateway']
                        ?? $payment->gateway,

                    'event_type'
                        => $data['event_type']
                        ?? null,

                    'gateway_transaction_id'
                        => $data['gateway_transaction_id']
                        ?? null,

                    'gateway_order_id'
                        => $data['gateway_order_id']
                        ?? null,

                    'status'
                        => $data['status']
                        ?? PaymentCallback::STATUS_RECEIVED,

                    'http_method'
                        => $data['http_method']
                        ?? null,

                    'http_status'
                        => $data['http_status']
                        ?? null,

                    'ip_address'
                        => $data['ip_address']
                        ?? null,

                    'user_agent'
                        => $data['user_agent']
                        ?? null,

                    'signature'
                        => $data['signature']
                        ?? null,

                    'signature_valid'
                        => $data['signature_valid']
                        ?? false,

                    'headers'
                        => $data['headers']
                        ?? null,

                    'payload'
                        => $data['payload']
                        ?? null,

                    'processing_result'
                        => $data['processing_result']
                        ?? null,

                    'error_message'
                        => $data['error_message']
                        ?? null,

                    'received_at'
                        => $data['received_at']
                        ?? now(),

                    'processed_at'
                        => $data['processed_at']
                        ?? null,

                    'metadata'
                        => $data['metadata']
                        ?? null,
                ]);

                return $callback
                    ->fresh()
                    ->load('payment');
            }
        );
    }

    /**
     * Process callback.
     */
    public function processCallback(
        PaymentCallback|int $callback,
        array $result = []
    ): PaymentCallback {

        return DB::transaction(
            function () use (
                $callback,
                $result
            ) {

                $callback =
                    $this->findCallbackOrFail(
                        $callback
                    );

                $callback->markProcessed(
                    $result
                );

                return $callback
                    ->fresh()
                    ->load('payment');
            }
        );
    }

    /**
     * Mark callback as processed.
     */
    public function markCallbackProcessed(
        PaymentCallback|int $callback,
        array $result = []
    ): PaymentCallback {

        return $this->processCallback(
            $callback,
            $result
        );
    }

    /**
     * Mark callback as failed.
     */
    public function markCallbackFailed(
        PaymentCallback|int $callback,
        ?string $message = null
    ): PaymentCallback {

        return DB::transaction(
            function () use (
                $callback,
                $message
            ) {

                $callback =
                    $this->findCallbackOrFail(
                        $callback
                    );

                $callback->markFailed(
                    $message
                );

                return $callback
                    ->fresh()
                    ->load('payment');
            }
        );
    }

    /**
     * Mark callback as ignored.
     */
    public function markCallbackIgnored(
        PaymentCallback|int $callback
    ): PaymentCallback {

        return DB::transaction(
            function () use ($callback) {

                $callback =
                    $this->findCallbackOrFail(
                        $callback
                    );

                $callback->markIgnored();

                return $callback
                    ->fresh()
                    ->load('payment');
            }
        );
    }
    /**
     * Refund payment.
     */
    public function refund(
        Payment|int $payment,
        float $amount,
        array $transactionData = []
    ): Payment {

        return DB::transaction(
            function () use (
                $payment,
                $amount,
                $transactionData
            ) {

                $payment = $payment instanceof Payment
                    ? $payment
                    : $this->findOrFail(
                        $payment
                    );

                $this->validateRefundAmount(
                    $payment,
                    $amount
                );

                $remainingBeforeRefund =

                    (float) $payment->paid_amount

                    -

                    (float) $payment->refund_amount;

                $isFullRefund =
                    $amount >= $remainingBeforeRefund;

                $payment->markAsRefunded(
                    $payment->refund_amount
                    + $amount
                );

                $this->createTransaction(
                    $payment,
                    array_merge(
                        [

                            'transaction_id'
                                => $this->generateTransactionId(),

                            'gateway_transaction_id'
                                => $payment->gateway_transaction_id,

                            'gateway_order_id'
                                => $payment->gateway_order_id,

                            'gateway'
                                => $payment->gateway,

                            'method'
                                => $payment->method,

                            'type'
                                => $isFullRefund
                                    ? PaymentTransaction::TYPE_REFUND
                                    : PaymentTransaction::TYPE_PARTIAL_REFUND,

                            'amount'
                                => $amount,

                            'fee_amount'
                                => 0,

                            'net_amount'
                                => -$amount,

                            'status'
                                => PaymentTransaction::STATUS_SUCCESS,

                            'notes'
                                => 'Refund payment.',

                            'processed_at'
                                => now(),
                        ],
                        $transactionData
                    )
                );

                return $payment
                    ->fresh()
                    ->load($this->relations);
            }
        );
    }
    /**
     * Validate refund amount.
     */
    protected function validateRefundAmount(
        Payment $payment,
        float $amount
    ): void {

        if ($amount <= 0) {

            throw ValidationException::withMessages([

                'amount' => [
                    'Jumlah refund harus lebih dari 0.',
                ],
            ]);
        }

        if (! $payment->isPaid()) {

            throw ValidationException::withMessages([

                'payment' => [
                    'Pembayaran belum berhasil sehingga tidak dapat direfund.',
                ],
            ]);
        }

        $remainingRefund =

            (float) $payment->paid_amount

            -

            (float) $payment->refund_amount;

        if ($amount > $remainingRefund) {

            throw ValidationException::withMessages([

                'amount' => [
                    'Jumlah refund melebihi sisa dana yang dapat direfund.',
                ],
            ]);
        }
    }

    /**
     * Find callback or fail.
     */
    protected function findCallbackOrFail(
        PaymentCallback|int $callback
    ): PaymentCallback {

        return $callback instanceof PaymentCallback

            ? $callback

            : PaymentCallback::query()

                ->with('payment')

                ->findOrFail(
                    $callback
                );
    }

    /**
     * Generate transaction ID.
     */
    protected function generateTransactionId(): string
    {
        return 'TRX-'
            . now()->format('YmdHis')
            . '-'
            . strtoupper(
                substr(
                    uniqid(),
                    -6
                )
            );
    }
}