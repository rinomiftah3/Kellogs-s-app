<?php

namespace App\Http\Resources\V1;

use App\Models\ProductSku;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSkuResource extends JsonResource
{
    /**
     * Transform resource into array.
     */
    public function toArray(
        Request $request
    ): array {

        $price = (float) $this->price;

        $compareAtPrice = (float) (
            $this->compare_at_price ?? 0
        );

        $discountAmount =
            $this->hasDiscount()
                ? ($compareAtPrice - $price)
                : 0;

        $discountPercentage =
            (
                $this->hasDiscount()
                &&
                $compareAtPrice > 0
            )
                ? round(
                    (
                        $discountAmount
                        / $compareAtPrice
                    ) * 100,
                    2
                )
                : 0;

        $stock =
            $this->availableStock();

        return [

            /*
            |--------------------------------------------------------------------------
            | Identity
            |--------------------------------------------------------------------------
            */

            'id' =>
                $this->id,

            'sku' =>
                $this->sku,

            'barcode' =>
                $this->barcode,

            'has_barcode' =>
                $this->hasBarcode(),

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */

            'price' =>
                $price,

            'price_formatted' =>
                'Rp ' .
                number_format(
                    $price,
                    0,
                    ',',
                    '.'
                ),

            'compare_at_price' =>
                $this->compare_at_price,

            'compare_at_price_formatted' =>

                $this->compare_at_price
                    ? 'Rp ' .
                    number_format(
                        $compareAtPrice,
                        0,
                        ',',
                        '.'
                    )
                    : null,

            'cost_price' =>
                $this->cost_price,

            'cost_price_formatted' =>

                $this->cost_price
                    ? 'Rp ' .
                    number_format(
                        (float) $this->cost_price,
                        0,
                        ',',
                        '.'
                    )
                    : null,

            /*
            |--------------------------------------------------------------------------
            | Discount
            |--------------------------------------------------------------------------
            */

            'has_discount' =>
                $this->hasDiscount(),

            'discount_amount' =>
                $discountAmount,

            'discount_amount_formatted' =>

                $discountAmount > 0
                    ? 'Rp ' .
                    number_format(
                        $discountAmount,
                        0,
                        ',',
                        '.'
                    )
                    : null,

            'discount_percentage' =>
                $discountPercentage,

            /*
            |--------------------------------------------------------------------------
            | Profit Analysis
            |--------------------------------------------------------------------------
            */

            'profit' =>
                $this->profit,

            'profit_formatted' =>
                'Rp ' .
                number_format(
                    $this->profit,
                    0,
                    ',',
                    '.'
                ),

            'margin_percentage' =>
                $this->margin_percentage,

            /*
            |--------------------------------------------------------------------------
            | Inventory
            |--------------------------------------------------------------------------
            */

            'stock' =>
                $stock,

            'is_in_stock' =>
                $this->isInStock(),

            'is_out_of_stock' =>
                ! $this->isInStock(),

            'is_low_stock' =>
                $this->isLowStock(),

            'stock_status' =>
                match (true) {

                    ! $this->isInStock()
                        => 'out_of_stock',

                    $this->isLowStock()
                        => 'low_stock',

                    default
                        => 'in_stock',
                },

            'stock_label' =>
                match (true) {

                    ! $this->isInStock()
                        => 'Out Of Stock',

                    $this->isLowStock()
                        => 'Low Stock',

                    default
                        => 'Available',
                },

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */

            'product_id' =>
                $this->product_id,

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

            /*
            |--------------------------------------------------------------------------
            | Option Values
            |--------------------------------------------------------------------------
            */

            'option_values' =>

                $this->whenLoaded(
                    'optionValues',

                    fn () =>

                    $this->optionValues
                        ->map(
                            fn ($value) => [

                                'id' =>
                                    $value->id,

                                'name' =>
                                    $value->value,

                                'option' =>
                                    $value
                                        ->relationLoaded(
                                            'option'
                                        )
                                        ? $value
                                            ->option
                                            ?->name
                                        : null,
                            ]
                        )
                        ->values()
                ),

            /*
            |--------------------------------------------------------------------------
            | Dimensions
            |--------------------------------------------------------------------------
            */

            'weight' =>
                $this->weight,

            'length' =>
                $this->length,

            'width' =>
                $this->width,

            'height' =>
                $this->height,

            /*
            |--------------------------------------------------------------------------
            | Purchase Rules
            |--------------------------------------------------------------------------
            */

            'minimum_order_quantity' =>

                $this->minimum_order_quantity,

            'maximum_order_quantity' =>

                $this->maximum_order_quantity,

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' =>
                $this->status,

            'status_label' =>
                match ($this->status) {

                    ProductSku::STATUS_DRAFT =>
                        'Draft',

                    ProductSku::STATUS_ACTIVE =>
                        'Active',

                    ProductSku::STATUS_INACTIVE =>
                        'Inactive',

                    ProductSku::STATUS_ARCHIVED =>
                        'Archived',

                    default =>
                        ucfirst(
                            (string) $this->status
                        ),
                },

            'is_active' =>
                (bool) $this->is_active,

            'is_default' =>
                (bool) $this->is_default,

            'is_published' =>
                $this->isPublished(),

            /*
            |--------------------------------------------------------------------------
            | Business Rules
            |--------------------------------------------------------------------------
            */

            'can_be_purchased' =>

                $this->isActive()
                &&
                $this->isPublished()
                &&
                $this->isInStock(),

            /*
            |--------------------------------------------------------------------------
            | Publish Information
            |--------------------------------------------------------------------------
            */

            'published_at' =>
                $this->published_at?->toISOString(),

            'published_at_human' =>
                $this->published_at?->diffForHumans(),

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