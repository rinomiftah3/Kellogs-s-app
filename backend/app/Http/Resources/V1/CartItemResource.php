<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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

            'cart_id' =>
                $this->cart_id,

            'product_sku_id' =>
                $this->product_sku_id,

            /*
            |--------------------------------------------------------------------------
            | Product Information
            |--------------------------------------------------------------------------
            */

            'product_name' =>
                $this->product_name,

            'sku' =>
                $this->sku,

            'sku_code' =>
                $this->skuCode(),

            'product_id' =>
                $this->productId(),

            /*
            |--------------------------------------------------------------------------
            | Thumbnail
            |--------------------------------------------------------------------------
            */

            'thumbnail' =>
                $this->thumbnail,

            'thumbnail_url' =>
                $this->thumbnailUrl(),

            'has_thumbnail' =>
                $this->hasThumbnail(),

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */

            'price' =>
                (float) $this->price,

            'price_formatted' =>
                'Rp ' . number_format(
                    (float) $this->price,
                    0,
                    ',',
                    '.'
                ),

            'quantity' =>
                (int) $this->quantity,

            'subtotal' =>
                (float) $this->subtotal,

            'subtotal_formatted' =>
                'Rp ' . number_format(
                    (float) $this->subtotal,
                    0,
                    ',',
                    '.'
                ),

            'line_total' =>
                $this->lineTotal(),

            /*
            |--------------------------------------------------------------------------
            | Availability
            |--------------------------------------------------------------------------
            */

            'is_available' =>
                $this->isAvailable(),

            'is_selected' =>
                $this->isSelected(),

            'status_label' =>
                match (true) {

                    !$this->isAvailable()
                        => 'Unavailable',

                    $this->isSelected()
                        => 'Selected',

                    default
                        => 'Available',
                },

            'status_color' =>
                match (true) {

                    !$this->isAvailable()
                        => 'red',

                    $this->isSelected()
                        => 'green',

                    default
                        => 'blue',
                },

            /*
            |--------------------------------------------------------------------------
            | Additional Information
            |--------------------------------------------------------------------------
            */

            'notes' =>
                $this->notes,

            /*
            |--------------------------------------------------------------------------
            | Related Cart
            |--------------------------------------------------------------------------
            */

            'cart' =>
                $this->whenLoaded(
                    'cart',
                    fn () => [

                        'id' =>
                            $this->cart->id,

                        'total_items' =>
                            $this->cart->total_items,

                        'subtotal' =>
                            (float) $this->cart->subtotal,
                    ]
                ),

            /*
            |--------------------------------------------------------------------------
            | Related SKU
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
            | Inventory Information
            |--------------------------------------------------------------------------
            */

            'available_stock' =>
                $this->inventory()?->available_stock,

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