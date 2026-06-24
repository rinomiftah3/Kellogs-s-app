<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionResource extends JsonResource
{
    /**
     * Transform resource into array.
     */
    public function toArray(
        Request $request
    ): array {

        $valuesCount = $this->whenCounted(
            'values'
        );

        return [

            /*
            |--------------------------------------------------------------------------
            | Identity
            |--------------------------------------------------------------------------
            */

            'id' =>
                $this->id,

            'product_id' =>
                $this->product_id,

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */

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
            | Option Information
            |--------------------------------------------------------------------------
            */

            'name' =>
                $this->name,

            'code' =>
                $this->code,

            /*
            |--------------------------------------------------------------------------
            | Display
            |--------------------------------------------------------------------------
            */

            'sort_order' =>
                $this->sort_order,

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_required' =>
                $this->isRequired(),

            'requirement_label' =>
                $this->isRequired()
                    ? 'Required'
                    : 'Optional',

            'is_active' =>
                $this->isActive(),

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
            | Statistics
            |--------------------------------------------------------------------------
            */

            'values_count' =>
                $valuesCount,

            'has_values' =>
                ($valuesCount ?? 0) > 0,

            /*
            |--------------------------------------------------------------------------
            | Business Helpers
            |--------------------------------------------------------------------------
            */

            'can_be_deleted' =>
                ($valuesCount ?? 0) === 0,

            'can_be_activated' =>
                ! $this->isActive(),

            'can_be_deactivated' =>
                $this->isActive(),

            'can_be_marked_required' =>
                ! $this->isRequired(),

            'can_be_marked_optional' =>
                $this->isRequired(),

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