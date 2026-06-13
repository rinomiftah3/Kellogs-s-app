<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingMethodResource extends JsonResource
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

            'courier_id' =>
                $this->courier_id,

            'service_code' =>
                $this->service_code,

            'service_name' =>
                $this->service_name,

            'display_name' =>
                $this->display_name,

            'description' =>
                $this->description,

            /*
            |--------------------------------------------------------------------------
            | Delivery Information
            |--------------------------------------------------------------------------
            */

            'estimated_min_days' =>
                (int) $this->estimated_min_days,

            'estimated_max_days' =>
                (int) $this->estimated_max_days,

            'delivery_estimation' =>
                $this->delivery_estimation,

            'sla_hours' =>
                (int) $this->sla_hours,

            /*
            |--------------------------------------------------------------------------
            | Features
            |--------------------------------------------------------------------------
            */

            'supports_tracking' =>
                $this->supportsTracking(),

            'supports_cod' =>
                $this->supportsCod(),

            'supports_insurance' =>
                $this->supportsInsurance(),

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */

            'base_cost' =>
                (float) $this->base_cost,

            'base_cost_formatted' =>
                'Rp ' .
                number_format(
                    $this->base_cost,
                    0,
                    ',',
                    '.'
                ),

            'cost_per_kg' =>
                (float) $this->cost_per_kg,

            'cost_per_kg_formatted' =>
                'Rp ' .
                number_format(
                    $this->cost_per_kg,
                    0,
                    ',',
                    '.'
                ),

            /*
            |--------------------------------------------------------------------------
            | Weight Rules
            |--------------------------------------------------------------------------
            */

            'minimum_weight' =>
                (int) $this->minimum_weight,

            'maximum_weight' =>
                $this->maximum_weight !== null
                    ? (int) $this->maximum_weight
                    : null,

            'has_weight_limit' =>
                $this->hasWeightLimit(),

            /*
            |--------------------------------------------------------------------------
            | Free Shipping
            |--------------------------------------------------------------------------
            */

            'free_shipping_threshold' =>
                $this->free_shipping_threshold !== null
                    ? (float) $this->free_shipping_threshold
                    : null,

            'free_shipping_threshold_formatted' =>
                $this->free_shipping_threshold !== null
                    ? 'Rp ' .
                        number_format(
                            $this->free_shipping_threshold,
                            0,
                            ',',
                            '.'
                        )
                    : null,

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_featured' =>
                $this->isFeatured(),

            'is_active' =>
                $this->isActive(),

            'is_published' =>
                $this->isPublished(),

            'status_label' =>
                $this->isActive()
                    ? 'Active'
                    : 'Inactive',

            'status_color' =>
                $this->isActive()
                    ? 'green'
                    : 'red',

            /*
            |--------------------------------------------------------------------------
            | Sorting
            |--------------------------------------------------------------------------
            */

            'sort_order' =>
                (int) $this->sort_order,

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

            'courier' =>
                $this->whenLoaded(
                    'courier',
                    fn () => [

                        'id' =>
                            $this->courier->id,

                        'name' =>
                            $this->courier->name,

                        'code' =>
                            $this->courier->code,
                    ]
                ),

            'courier_name' =>
                $this->courierName(),

            'shipments_count' =>
                $this->relationLoaded(
                    'shipments'
                )
                    ? $this->shipments->count()
                    : null,

            /*
            |--------------------------------------------------------------------------
            | Publication
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