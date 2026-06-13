<?php

namespace App\Http\Resources\V1;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform resource into array.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            /*
            |--------------------------------------------------------------------------
            | Identity
            |--------------------------------------------------------------------------
            */

            'id' =>
                $this->id,

            'order_id' =>
                $this->order_id,

            'payment_number' =>
                $this->payment_number,

            /*
            |--------------------------------------------------------------------------
            | Gateway Information
            |--------------------------------------------------------------------------
            */

            'gateway' =>
                $this->gateway,

            'method' =>
                $this->method,

            'gateway_transaction_id' =>
                $this->gateway_transaction_id,

            'gateway_order_id' =>
                $this->gateway_order_id,

            'has_gateway_transaction' =>
                $this->hasGatewayTransaction(),

            /*
            |--------------------------------------------------------------------------
            | Payment URL
            |--------------------------------------------------------------------------
            */

            'payment_url' =>
                $this->payment_url,

            'has_payment_url' =>
                $this->hasPaymentUrl(),

            /*
            |--------------------------------------------------------------------------
            | Financial Information
            |--------------------------------------------------------------------------
            */

            'amount' =>
                (float) $this->amount,

            'amount_formatted' =>
                'Rp ' .
                number_format(
                    $this->amount,
                    0,
                    ',',
                    '.'
                ),

            'paid_amount' =>
                (float) $this->paid_amount,

            'paid_amount_formatted' =>
                'Rp ' .
                number_format(
                    $this->paid_amount,
                    0,
                    ',',
                    '.'
                ),

            'fee_amount' =>
                (float) $this->fee_amount,

            'fee_amount_formatted' =>
                'Rp ' .
                number_format(
                    $this->fee_amount,
                    0,
                    ',',
                    '.'
                ),

            'refund_amount' =>
                (float) $this->refund_amount,

            'refund_amount_formatted' =>
                'Rp ' .
                number_format(
                    $this->refund_amount,
                    0,
                    ',',
                    '.'
                ),

            /*
            |--------------------------------------------------------------------------
            | Calculated Values
            |--------------------------------------------------------------------------
            */

            'net_amount' =>
                (float) $this->net_amount,

            'net_amount_formatted' =>
                'Rp ' .
                number_format(
                    $this->net_amount,
                    0,
                    ',',
                    '.'
                ),

            'remaining_amount' =>
                (float) $this->remaining_amount,

            'remaining_amount_formatted' =>
                'Rp ' .
                number_format(
                    $this->remaining_amount,
                    0,
                    ',',
                    '.'
                ),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' =>
                $this->status,

            'is_paid' =>
                $this->isPaid(),

            'is_pending' =>
                $this->isPending(),

            'is_failed' =>
                $this->isFailed(),

            'is_cancelled' =>
                $this->isCancelled(),

            'is_expired' =>
                $this->isExpired(),

            'is_refunded' =>
                $this->isRefunded(),

            'is_partial_refund' =>
                $this->isPartialRefund(),

            'is_successful' =>
                $this->is_successful,

            'is_fully_paid' =>
                $this->isFullyPaid(),

            'is_over_paid' =>
                $this->isOverPaid(),

            'has_refund' =>
                $this->hasRefund(),

            'status_label' =>
                match ($this->status) {

                    Payment::STATUS_PENDING
                        => 'Pending',

                    Payment::STATUS_PAID
                        => 'Paid',

                    Payment::STATUS_FAILED
                        => 'Failed',

                    Payment::STATUS_EXPIRED
                        => 'Expired',

                    Payment::STATUS_CANCELLED
                        => 'Cancelled',

                    Payment::STATUS_REFUNDED
                        => 'Refunded',

                    Payment::STATUS_PARTIAL_REFUND
                        => 'Partial Refund',

                    default
                        => ucfirst(
                            (string) $this->status
                        ),
                },

            'status_color' =>
                match ($this->status) {

                    Payment::STATUS_PAID
                        => 'green',

                    Payment::STATUS_PENDING
                        => 'yellow',

                    Payment::STATUS_FAILED,
                    Payment::STATUS_CANCELLED,
                    Payment::STATUS_EXPIRED
                        => 'red',

                    Payment::STATUS_REFUNDED,
                    Payment::STATUS_PARTIAL_REFUND
                        => 'blue',

                    default
                        => 'gray',
                },

            /*
            |--------------------------------------------------------------------------
            | Notes & Metadata
            |--------------------------------------------------------------------------
            */

            'notes' =>
                $this->notes,

            'metadata' =>
                $this->metadata,

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            'order' =>
                $this->whenLoaded(
                    'order',
                    fn () => [

                        'id' =>
                            $this->order->id,

                        'order_number' =>
                            $this->order->order_number,
                    ]
                ),

            'transactions_count' =>
                $this->relationLoaded('transactions')
                    ? $this->transactions->count()
                    : null,

            'callbacks_count' =>
                $this->relationLoaded('callbacks')
                    ? $this->callbacks->count()
                    : null,

            /*
            |--------------------------------------------------------------------------
            | Payment Dates
            |--------------------------------------------------------------------------
            */

            'paid_at' =>
                $this->paid_at?->toISOString(),

            'paid_at_human' =>
                $this->paid_at?->diffForHumans(),

            'expired_at' =>
                $this->expired_at?->toISOString(),

            'expired_at_human' =>
                $this->expired_at?->diffForHumans(),

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            'created_at' =>
                $this->created_at?->toISOString(),

            'created_at_human' =>
                $this->created_at?->diffForHumans(),

            'updated_at' =>
                $this->updated_at?->toISOString(),

            'updated_at_human' =>
                $this->updated_at?->diffForHumans(),
        ];
    }
}