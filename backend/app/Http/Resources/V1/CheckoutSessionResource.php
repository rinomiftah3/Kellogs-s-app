<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutSessionResource extends JsonResource
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

            'session_code' =>
                $this->session_code,

            'customer_profile_id' =>
                $this->customer_profile_id,

            'shipping_address_id' =>
                $this->shipping_address_id,

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' =>
                $this->status,

            'is_ready' =>
                $this->isReady(),

            'is_checked_out' =>
                $this->isCheckedOut(),

            'is_expired' =>
                $this->isExpired(),

            'can_checkout' =>
                $this->canCheckout(),

            'status_label' =>
                match ($this->status) {

                    'draft'
                        => 'Draft',

                    'ready'
                        => 'Ready',

                    'checked_out'
                        => 'Checked Out',

                    'expired'
                        => 'Expired',

                    'cancelled'
                        => 'Cancelled',

                    default
                        => ucfirst(
                            (string) $this->status
                        ),
                },

            'status_color' =>
                match ($this->status) {

                    'draft'
                        => 'gray',

                    'ready'
                        => 'blue',

                    'checked_out'
                        => 'green',

                    'expired'
                        => 'red',

                    'cancelled'
                        => 'orange',

                    default
                        => 'gray',
                },

            /*
            |--------------------------------------------------------------------------
            | Customer Information
            |--------------------------------------------------------------------------
            */

            'customer_name' =>
                $this->customerName(),

            'customer_email' =>
                $this->customerEmail(),

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
            | Shipping Information
            |--------------------------------------------------------------------------
            */

            'has_address' =>
                $this->hasAddress(),

            'has_courier' =>
                $this->hasCourier(),

            'courier_code' =>
                $this->courier_code,

            'courier_service' =>
                $this->courier_service,

            'shipping_address' =>
                $this->whenLoaded(
                    'shippingAddress',
                    fn () => [

                        'id' =>
                            $this->shippingAddress->id,

                        'recipient_name' =>
                            $this->shippingAddress->recipient_name,

                        'recipient_phone' =>
                            $this->shippingAddress->recipient_phone,

                        'address' =>
                            $this->shippingAddress->fullAddress(),
                    ]
                ),

            /*
            |--------------------------------------------------------------------------
            | Voucher & Promotion
            |--------------------------------------------------------------------------
            */

            'voucher_code' =>
                $this->voucher_code,

            'voucher_discount' =>
                (float) $this->voucher_discount,

            'promotion_discount' =>
                (float) $this->promotion_discount,

            'total_discount' =>
                (float) $this->total_discount,

            /*
            |--------------------------------------------------------------------------
            | Validation Status
            |--------------------------------------------------------------------------
            */

            'is_price_valid' =>
                (bool) $this->is_price_valid,

            'is_stock_valid' =>
                (bool) $this->is_stock_valid,

            'is_voucher_valid' =>
                (bool) $this->is_voucher_valid,

            /*
            |--------------------------------------------------------------------------
            | Financial Summary
            |--------------------------------------------------------------------------
            */

            'subtotal' =>
                (float) $this->subtotal,

            'subtotal_formatted' =>
                'Rp ' . number_format(
                    (float) $this->subtotal,
                    0,
                    ',',
                    '.'
                ),

            'shipping_cost' =>
                (float) $this->shipping_cost,

            'shipping_cost_formatted' =>
                'Rp ' . number_format(
                    (float) $this->shipping_cost,
                    0,
                    ',',
                    '.'
                ),

            'grand_total' =>
                (float) $this->grand_total,

            'grand_total_formatted' =>
                'Rp ' . number_format(
                    (float) $this->grand_total,
                    0,
                    ',',
                    '.'
                ),

            /*
            |--------------------------------------------------------------------------
            | Checkout Statistics
            |--------------------------------------------------------------------------
            */

            'total_items' =>
                $this->totalItems(),

            'total_weight' =>
                (int) $this->total_weight,

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'notes' =>
                $this->notes,

            /*
            |--------------------------------------------------------------------------
            | Checkout Items
            |--------------------------------------------------------------------------
            */

            'items_count' =>
                $this->relationLoaded('items')
                    ? $this->items->count()
                    : null,

            'items' =>
                $this->whenLoaded(
                    'items',
                    fn () => CheckoutItemResource::collection(
                        $this->items
                    )
                ),

            /*
            |--------------------------------------------------------------------------
            | Important Dates
            |--------------------------------------------------------------------------
            */

            'expired_at' =>
                $this->expired_at?->toISOString(),

            'expired_at_human' =>
                $this->expired_at?->diffForHumans(),

            'checked_out_at' =>
                $this->checked_out_at?->toISOString(),

            'checked_out_at_human' =>
                $this->checked_out_at?->diffForHumans(),

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