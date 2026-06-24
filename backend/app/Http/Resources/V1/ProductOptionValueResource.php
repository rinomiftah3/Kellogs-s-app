<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionValueResource extends JsonResource
{
    /**
     * Transform resource into array.
     */
    public function toArray(
        Request $request
    ): array {

        $skuCount = $this->whenCounted(
            'skuValues'
        );

        return [

            /*
            |--------------------------------------------------------------------------
            | Identity
            |--------------------------------------------------------------------------
            */

            'id' =>
                $this->id,

            'product_option_id' =>
                $this->product_option_id,

            'value' =>
                $this->value,

            'code' =>
                $this->code,

            'has_code' =>
                $this->hasCode(),

            /*
            |--------------------------------------------------------------------------
            | Option Information
            |--------------------------------------------------------------------------
            */

            'option' =>
                $this->whenLoaded(
                    'option',
                    fn () => [

                        'id' =>
                            $this->option->id,

                        'name' =>
                            $this->option->name,

                        'code' =>
                            $this->option->code,

                        'product_id' =>
                            $this->option->product_id,
                    ]
                ),

            /*
            |--------------------------------------------------------------------------
            | Product Information
            |--------------------------------------------------------------------------
            */

            'product' =>
                $this->whenLoaded(
                    'option',
                    fn () => $this->option->relationLoaded('product')
                        ? [

                            'id' =>
                                $this->option->product->id,

                            'name' =>
                                $this->option->product->name,

                            'slug' =>
                                $this->option->product->slug,
                        ]
                        : null
                ),

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

            'sku_count' =>
                $skuCount,

            'has_sku' =>
                ($skuCount ?? 0) > 0,

            'is_used' =>
                ($skuCount ?? 0) > 0,

            /*
            |--------------------------------------------------------------------------
            | Business Rules
            |--------------------------------------------------------------------------
            */

            'can_be_deleted' =>
                ($skuCount ?? 0) === 0,

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