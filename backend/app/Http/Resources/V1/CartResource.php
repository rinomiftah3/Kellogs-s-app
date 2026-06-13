<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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

            'customer_profile_id' =>
                $this->customer_profile_id,

            /*
            |--------------------------------------------------------------------------
            | Cart Summary
            |--------------------------------------------------------------------------
            */

            'total_items' =>
                (int) $this->total_items,

            'item_count' =>
                $this->itemCount(),

            'subtotal' =>
                (float) $this->subtotal,

            'subtotal_formatted' =>
                'Rp ' . number_format(
                    (float) $this->subtotal,
                    0,
                    ',',
                    '.'
                ),

            /*
            |--------------------------------------------------------------------------
            | Customer Information
            |--------------------------------------------------------------------------
            */

            'customer_name' =>
                $this->customerName(),

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
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active' =>
                $this->isActive(),

            'is_expired' =>
                $this->isExpired(),

            'is_empty' =>
                $this->isEmpty(),

            'has_items' =>
                $this->hasItems(),

            'is_abandoned' =>
                $this->isAbandoned(),

            /*
            |--------------------------------------------------------------------------
            | Status Labels
            |--------------------------------------------------------------------------
            */

            'status_label' =>
                match (true) {

                    $this->isExpired()
                        => 'Expired',

                    $this->isAbandoned()
                        => 'Abandoned',

                    !$this->isActive()
                        => 'Inactive',

                    $this->isEmpty()
                        => 'Empty',

                    default
                        => 'Active',
                },

            'status_color' =>
                match (true) {

                    $this->isExpired()
                        => 'red',

                    $this->isAbandoned()
                        => 'yellow',

                    !$this->isActive()
                        => 'gray',

                    $this->isEmpty()
                        => 'blue',

                    default
                        => 'green',
                },

            /*
            |--------------------------------------------------------------------------
            | Cart Items
            |--------------------------------------------------------------------------
            */

            'items_count' =>
                $this->relationLoaded('items')
                    ? $this->items->count()
                    : null,

            'items' =>
                $this->whenLoaded(
                    'items',
                    fn () => CartItemResource::collection(
                        $this->items
                    )
                ),

            /*
            |--------------------------------------------------------------------------
            | Activity Information
            |--------------------------------------------------------------------------
            */

            'last_activity_at' =>
                $this->last_activity_at?->toISOString(),

            'last_activity_human' =>
                $this->last_activity_at?->diffForHumans(),

            'expires_at' =>
                $this->expires_at?->toISOString(),

            'expires_at_human' =>
                $this->expires_at?->diffForHumans(),

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