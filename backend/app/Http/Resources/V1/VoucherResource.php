<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
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
            | Voucher Type
            |--------------------------------------------------------------------------
            */

            'type' =>
                $this->type,

            'type_label' =>
                match ($this->type) {

                    'fixed'
                        => 'Fixed Discount',

                    'percentage'
                        => 'Percentage Discount',

                    'free_shipping'
                        => 'Free Shipping',

                    default
                        => ucfirst(
                            (string) $this->type
                        ),
                },

            'is_fixed' =>
                $this->isFixed(),

            'is_percentage' =>
                $this->isPercentage(),

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
                $this->isPercentage()
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
            | Usage Information
            |--------------------------------------------------------------------------
            */

            'usage_limit' =>
                $this->usage_limit,

            'usage_per_user' =>
                $this->usage_per_user,

            'used_count' =>
                (int) $this->used_count,

            'remaining_usage' =>
                $this->remainingUsage(),

            'has_usage_limit' =>
                $this->hasUsageLimit(),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active' =>
                (bool) $this->is_active,

            'is_public' =>
                (bool) $this->is_public,

            'is_stackable' =>
                $this->isStackable(),

            'is_started' =>
                $this->isStarted(),

            'is_expired' =>
                $this->isExpired(),

            'is_valid' =>
                $this->isValid(),

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

                    !$this->isValid()
                        => 'Unavailable',

                    default
                        => 'Active',
                },

            'status_color' =>
                match (true) {

                    !$this->is_active
                        => 'gray',

                    $this->isExpired()
                        => 'red',

                    !$this->isStarted()
                        => 'blue',

                    !$this->isValid()
                        => 'yellow',

                    default
                        => 'green',
                },

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
            | Usage Statistics
            |--------------------------------------------------------------------------
            */

            'usages_count' =>
                $this->whenCounted(
                    'usages'
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