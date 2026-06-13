<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
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

            'name' =>
                $this->name,

            'code' =>
                $this->code,

            'description' =>
                $this->description,

            /*
            |--------------------------------------------------------------------------
            | Promotion Type
            |--------------------------------------------------------------------------
            */

            'type' =>
                $this->type,

            'type_label' =>
                match ($this->type) {

                    'fixed_discount'
                        => 'Fixed Discount',

                    'percentage_discount'
                        => 'Percentage Discount',

                    'flash_sale'
                        => 'Flash Sale',

                    'buy_x_get_y'
                        => 'Buy X Get Y',

                    'free_shipping'
                        => 'Free Shipping',

                    default
                        => ucfirst(
                            str_replace(
                                '_',
                                ' ',
                                (string) $this->type
                            )
                        ),
                },

            'is_fixed_discount' =>
                $this->isFixedDiscount(),

            'is_percentage_discount' =>
                $this->isPercentageDiscount(),

            'is_flash_sale' =>
                $this->isFlashSale(),

            'is_buy_x_get_y' =>
                $this->isBuyXGetY(),

            'is_free_shipping' =>
                $this->isFreeShipping(),

            /*
            |--------------------------------------------------------------------------
            | Discount Information
            |--------------------------------------------------------------------------
            */

            'discount_value' =>
                (float) $this->discount_value,

            'discount_value_formatted' =>
                (
                    $this->isPercentageDiscount()
                    || $this->isFlashSale()
                )
                    ? number_format(
                        (float) $this->discount_value,
                        0,
                        ',',
                        '.'
                    ) . '%'
                    : 'Rp ' . number_format(
                        (float) $this->discount_value,
                        0,
                        ',',
                        '.'
                    ),

            'maximum_discount' =>
                $this->maximum_discount !== null
                    ? (float) $this->maximum_discount
                    : null,

            'maximum_discount_formatted' =>
                $this->maximum_discount !== null
                    ? 'Rp ' . number_format(
                        (float) $this->maximum_discount,
                        0,
                        ',',
                        '.'
                    )
                    : null,

            'minimum_purchase' =>
                (float) $this->minimum_purchase,

            'minimum_purchase_formatted' =>
                'Rp ' . number_format(
                    (float) $this->minimum_purchase,
                    0,
                    ',',
                    '.'
                ),

            /*
            |--------------------------------------------------------------------------
            | Buy X Get Y
            |--------------------------------------------------------------------------
            */

            'buy_quantity' =>
                $this->buy_quantity,

            'free_quantity' =>
                $this->free_quantity,

            /*
            |--------------------------------------------------------------------------
            | Usage Information
            |--------------------------------------------------------------------------
            */

            'usage_limit' =>
                $this->usage_limit,

            'used_count' =>
                (int) $this->used_count,

            'remaining_usage' =>
                $this->remainingUsage(),

            'has_usage_limit' =>
                $this->hasUsageLimit(),

            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */

            'banner_image' =>
                $this->banner_image,

            'banner_url' =>
                $this->banner_url,

            'has_banner' =>
                $this->hasBanner(),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active' =>
                (bool) $this->is_active,

            'is_featured' =>
                (bool) $this->is_featured,

            'is_stackable' =>
                $this->isStackable(),

            'is_running' =>
                $this->isRunning(),

            'is_started' =>
                $this->isStarted(),

            'is_expired' =>
                $this->isExpired(),

            /*
            |--------------------------------------------------------------------------
            | Status Label
            |--------------------------------------------------------------------------
            */

            'status_label' =>
                match (true) {

                    !$this->is_active
                        => 'Inactive',

                    $this->isExpired()
                        => 'Expired',

                    !$this->isStarted()
                        => 'Upcoming',

                    !$this->isRunning()
                        => 'Unavailable',

                    default
                        => 'Running',
                },

            'status_color' =>
                match (true) {

                    !$this->is_active
                        => 'gray',

                    $this->isExpired()
                        => 'red',

                    !$this->isStarted()
                        => 'blue',

                    !$this->isRunning()
                        => 'yellow',

                    default
                        => 'green',
                },

            /*
            |--------------------------------------------------------------------------
            | Ordering
            |--------------------------------------------------------------------------
            */

            'sort_order' =>
                (int) $this->sort_order,

            /*
            |--------------------------------------------------------------------------
            | Period
            |--------------------------------------------------------------------------
            */

            'start_at' =>
                $this->start_at?->toISOString(),

            'start_at_human' =>
                $this->start_at?->diffForHumans(),

            'end_at' =>
                $this->end_at?->toISOString(),

            'end_at_human' =>
                $this->end_at?->diffForHumans(),

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'metadata' =>
                $this->metadata,

            /*
            |--------------------------------------------------------------------------
            | Relationship Statistics
            |--------------------------------------------------------------------------
            */

            'promo_products_count' =>
                $this->whenCounted(
                    'promoProducts'
                ),

            'promo_categories_count' =>
                $this->whenCounted(
                    'promoCategories'
                ),

            'promo_skus_count' =>
                $this->whenCounted(
                    'promoSkus'
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