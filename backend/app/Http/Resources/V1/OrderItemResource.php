<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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

            /*
            |--------------------------------------------------------------------------
            | Product Snapshot
            |--------------------------------------------------------------------------
            */

            'product_id' =>
                $this->product_id,

            'category_id' =>
                $this->category_id,

            'product_sku_id' =>
                $this->product_sku_id,

            'product_name' =>
                $this->product_name,

            'product_slug' =>
                $this->product_slug,

            'product_display_name' =>
                $this->productDisplayName(),

            /*
            |--------------------------------------------------------------------------
            | SKU Information
            |--------------------------------------------------------------------------
            */

            'sku' =>
                $this->sku,

            'sku_code' =>
                $this->skuCode(),

            'barcode' =>
                $this->barcode,

            'has_barcode' =>
                $this->hasBarcode(),

            /*
            |--------------------------------------------------------------------------
            | Variant
            |--------------------------------------------------------------------------
            */

            'variant_name' =>
                $this->variant_name,

            'has_variant' =>
                $this->hasVariant(),

            /*
            |--------------------------------------------------------------------------
            | Media
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
            | Quantity & Weight
            |--------------------------------------------------------------------------
            */

            'quantity' =>
                (int) $this->quantity,

            'weight' =>
                (int) $this->weight,

            'total_weight' =>
                $this->totalWeight(),

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */

            'unit_price' =>
                (float) $this->unit_price,

            'formatted_unit_price' =>
                'Rp ' .
                $this->formatted_unit_price,

            'discount_amount' =>
                (float) $this->discount_amount,

            'formatted_discount' =>
                'Rp ' .
                $this->formatted_discount,

            'unit_discount' =>
                $this->unitDiscount(),

            'final_price' =>
                (float) $this->final_price,

            'formatted_final_price' =>
                'Rp ' .
                $this->formatted_final_price,

            'subtotal' =>
                (float) $this->subtotal,

            'formatted_subtotal' =>
                'Rp ' .
                $this->formatted_subtotal,

            'total_discount' =>
                (float) $this->total_discount,

            'total_discount_amount' =>
                $this->totalDiscountAmount(),

            /*
            |--------------------------------------------------------------------------
            | Promotion
            |--------------------------------------------------------------------------
            */

            'promotion_name' =>
                $this->promotion_name,

            'promotion_code' =>
                $this->promotion_code,

            'has_promotion' =>
                $this->hasPromotion(),

            /*
            |--------------------------------------------------------------------------
            | Analytics
            |--------------------------------------------------------------------------
            */

            'revenue' =>
                $this->revenue(),

            'total_revenue' =>
                $this->totalRevenue(),

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

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

            'product' =>
                $this->whenLoaded(
                    'product',
                    fn () => [

                        'id' =>
                            $this->product->id,

                        'name' =>
                            $this->product->name,

                        'slug' =>
                            $this->product->slug,
                    ]
                ),

            'product_sku' =>
                $this->whenLoaded(
                    'productSku',
                    fn () => [

                        'id' =>
                            $this->productSku->id,

                        'sku' =>
                            $this->productSku->sku,
                    ]
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