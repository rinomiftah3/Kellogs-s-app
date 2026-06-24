<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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

            'order_number' =>
                $this->order_number,

            /*
            |--------------------------------------------------------------------------
            | Customer
            |--------------------------------------------------------------------------
            */

            'customer_profile_id' =>
                $this->customer_profile_id,

            'customer_name' =>
                $this->customer_name,

            'customer_email' =>
                $this->customer_email,

            'customer_phone' =>
                $this->customer_phone,

            'customer_display' =>
                $this->customerDisplay(),

            'customer_profile' =>
                $this->whenLoaded(
                    'customerProfile',
                    fn () => [
                        'id' =>
                            $this->customerProfile->id,

                        'customer_code' =>
                            $this->customerProfile->customer_code,

                        'full_name' =>
                            $this->customerProfile->full_name,
                    ]
                ),

            /*
            |--------------------------------------------------------------------------
            | Recipient
            |--------------------------------------------------------------------------
            */

            'recipient_name' =>
                $this->recipient_name,

            'recipient_phone' =>
                $this->recipient_phone,

            /*
            |--------------------------------------------------------------------------
            | Shipping Address
            |--------------------------------------------------------------------------
            */

            'shipping_address_id' =>
                $this->shipping_address_id,

            'shipping_address' =>
                $this->shipping_address,

            'province' =>
                $this->province,

            'city' =>
                $this->city,

            'district' =>
                $this->district,

            'postal_code' =>
                $this->postal_code,

            /*
            |--------------------------------------------------------------------------
            | Order Status
            |--------------------------------------------------------------------------
            */

            'status' =>
                $this->status,

            'is_pending' =>
                $this->isPending(),

            'is_completed' =>
                $this->isCompleted(),

            'is_cancelled' =>
                $this->isCancelled(),

            'status_label' =>
                ucfirst(
                    (string) $this->status
                ),

            'status_color' =>
                match ($this->status) {

                    'pending'
                        => 'yellow',

                    'confirmed'
                        => 'blue',

                    'processing'
                        => 'indigo',

                    'shipped'
                        => 'cyan',

                    'completed'
                        => 'green',

                    'cancelled'
                        => 'red',

                    default
                        => 'gray',
                },

            /*
            |--------------------------------------------------------------------------
            | Payment Status
            |--------------------------------------------------------------------------
            */

            'payment_status' =>
                $this->payment_status,

            'is_payment_pending'
                => $this->payment_status
                    === 'pending',

            'is_payment_paid'
                => $this->payment_status
                    === 'paid',

            'is_payment_failed'
                => $this->payment_status
                    === 'failed',

            'is_payment_refunded'
                => $this->payment_status
                    === 'refunded',

            'is_paid' =>
                $this->isPaid(),

            'payment_status_label' =>
                ucfirst(
                    (string) $this->payment_status
                ),

            /*
            |--------------------------------------------------------------------------
            | Fulfillment Status
            |--------------------------------------------------------------------------
            */

            'fulfillment_status' =>
                $this->fulfillment_status,

            'fulfillment_status_label' =>
                ucfirst(
                    (string) str_replace(
                        '_',
                        ' ',
                        $this->fulfillment_status
                    )
                ),

            /*
            |--------------------------------------------------------------------------
            | Financial Information
            |--------------------------------------------------------------------------
            */

            'subtotal' =>
                (float) $this->subtotal,

            'shipping_cost' =>
                (float) $this->shipping_cost,

            'discount_amount' =>
                (float) $this->discount_amount,

            'tax_amount' =>
                (float) $this->tax_amount,

            'grand_total' =>
                (float) $this->grand_total,

            'formatted_total' =>
                'Rp ' .
                $this->formatted_total,

            'grand_total_amount' =>
                $this->grandTotalAmount(),

            /*
            |--------------------------------------------------------------------------
            | Voucher
            |--------------------------------------------------------------------------
            */

            'voucher_code' =>
                $this->voucher_code,

            'voucher_discount' =>
                (float) $this->voucher_discount,

            /*
            |--------------------------------------------------------------------------
            | Shipping
            |--------------------------------------------------------------------------
            */

            'courier_code' =>
                $this->courier_code,

            'courier_service' =>
                $this->courier_service,

            'tracking_number' =>
                $this->tracking_number,

            'total_weight' =>
                (int) $this->total_weight,

            'has_shipment' =>
                $this->hasShipment(),

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */

            'has_payment' =>
                $this->hasPayment(),

            /*
            |--------------------------------------------------------------------------
            | Items Statistics
            |--------------------------------------------------------------------------
            */

            'item_count' =>
                $this->item_count,

            'items_count' =>
                $this->whenCounted(
                    'items'
                ),

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'customer_notes' =>
                $this->customer_notes,

            'admin_notes' =>
                $this->admin_notes,

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'metadata' =>
                $this->metadata,

            /*
            |--------------------------------------------------------------------------
            | Timeline
            |--------------------------------------------------------------------------
            */

            'ordered_at' =>
                $this->ordered_at?->toISOString(),

            'ordered_at_human' =>
                $this->ordered_at?->diffForHumans(),

            'paid_at' =>
                $this->paid_at?->toISOString(),

            'paid_at_human' =>
                $this->paid_at?->diffForHumans(),

            'shipped_at' =>
                $this->shipped_at?->toISOString(),

            'shipped_at_human' =>
                $this->shipped_at?->diffForHumans(),

            'completed_at' =>
                $this->completed_at?->toISOString(),

            'completed_at_human' =>
                $this->completed_at?->diffForHumans(),

            'cancelled_at' =>
                $this->cancelled_at?->toISOString(),

            'cancelled_at_human' =>
                $this->cancelled_at?->diffForHumans(),
'items' => OrderItemResource::collection(
    $this->whenLoaded('items')
),

'payment' => new PaymentResource(
    $this->whenLoaded('payment')
),
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