<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutItemResource extends JsonResource
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

            'checkout_session_id' =>
                $this->checkout_session_id,

            'product_sku_id' =>
                $this->product_sku_id,

            /*
            |--------------------------------------------------------------------------
            | Product Information
            |--------------------------------------------------------------------------
            */

            'product_name' =>
                $this->productName(),

            'product_id' =>
                $this->productId(),

            'sku' =>
                $this->sku,

            'sku_code' =>
                $this->skuCode(),

            /*
            |--------------------------------------------------------------------------
            | Thumbnail
            |--------------------------------------------------------------------------
            */

            'thumbnail' =>
                $this->thumbnail,

            'thumbnail_url' =>
                $this->thumbnail_url,

            'has_thumbnail' =>
                $this->hasThumbnail(),

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            'quantity' =>
                (int) $this->quantity,

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */

            'price' =>
                (float) $this->price,

            'price_formatted' =>
                'Rp ' .
                $this->formatted_price,

            'subtotal' =>
                (float) $this->subtotal,

            'subtotal_formatted' =>
                'Rp ' .
                $this->formatted_subtotal,

            'discount_amount' =>
                (float) $this->discount_amount,

            'discount_formatted' =>
                'Rp ' .
                $this->formatted_discount,

            'has_discount' =>
                $this->hasDiscount(),

            'final_price' =>
                (float) $this->final_price,

            'final_price_formatted' =>
                'Rp ' .
                $this->formatted_final_price,

            'final_unit_price' =>
                $this->finalUnitPrice(),

            /*
            |--------------------------------------------------------------------------
            | Validation Status
            |--------------------------------------------------------------------------
            */

            'is_available' =>
                $this->isAvailable(),

            'is_valid_price' =>
                $this->hasValidPrice(),

            'is_valid_stock' =>
                $this->hasValidStock(),

            'is_valid' =>
                $this->hasValidPrice()
                && $this->hasValidStock(),

            /*
            |--------------------------------------------------------------------------
            | Status Labels
            |--------------------------------------------------------------------------
            */

            'status_label' =>
                match (true) {

                    !$this->isAvailable()
                        => 'Unavailable',

                    !$this->hasValidStock()
                        => 'Invalid Stock',

                    !$this->hasValidPrice()
                        => 'Invalid Price',

                    default
                        => 'Valid',
                },

            'status_color' =>
                match (true) {

                    !$this->isAvailable()
                        => 'red',

                    !$this->hasValidStock()
                        => 'orange',

                    !$this->hasValidPrice()
                        => 'yellow',

                    default
                        => 'green',
                },

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'notes' =>
                $this->notes,

            'has_notes' =>
                $this->hasNotes(),

            /*
            |--------------------------------------------------------------------------
            | Checkout Session
            |--------------------------------------------------------------------------
            */

            'checkout_session' =>
                $this->whenLoaded(
                    'checkoutSession',
                    fn () => [

                        'id' =>
                            $this->checkoutSession->id,

                        'session_code' =>
                            $this->checkoutSession->session_code,

                        'status' =>
                            $this->checkoutSession->status,
                    ]
                ),

            /*
            |--------------------------------------------------------------------------
            | Product SKU
            |--------------------------------------------------------------------------
            */

            'product_sku' =>
                $this->whenLoaded(
                    'productSku',
                    fn () => [

                        'id' =>
                            $this->productSku->id,

                        'sku' =>
                            $this->productSku->sku,

                        'price' =>
                            (float) $this->productSku->price,

                        'is_active' =>
                            $this->productSku->isActive(),
                    ]
                ),

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'added_at' =>
                $this->added_at?->toISOString(),

            'added_at_human' =>
                $this->added_at?->diffForHumans(),

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