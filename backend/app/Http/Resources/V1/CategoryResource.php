<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform resource into array.
     */
    public function toArray(
        Request $request
    ): array {

        $productsCount = $this->whenCounted(
            'products'
        );

        $childrenCount = $this->whenCounted(
            'children'
        );

        return [

            /*
            |--------------------------------------------------------------------------
            | Identity
            |--------------------------------------------------------------------------
            */

            'id' => $this->id,

            'parent_id' => $this->parent_id,

            /*
            |--------------------------------------------------------------------------
            | Parent Category
            |--------------------------------------------------------------------------
            */

            'parent' => $this->whenLoaded(
                'parent',
                fn () => $this->parent
                    ? [
                        'id' => $this->parent->id,
                        'name' => $this->parent->name,
                    ]
                    : null
            ),

            /*
            |--------------------------------------------------------------------------
            | Backward Compatibility
            |--------------------------------------------------------------------------
            */

            'parent_name' => $this->whenLoaded(
                'parent',
                fn () => $this->parent?->name
            ),

            'name' => $this->name,

            'slug' => $this->slug,

            'description' => $this->description,

            /*
            |--------------------------------------------------------------------------
            | Image
            |--------------------------------------------------------------------------
            */

            'image' => $this->image,

            'image_url' => $this->image_url,

            'has_image' => $this->hasImage(),

            /*
            |--------------------------------------------------------------------------
            | Sorting
            |--------------------------------------------------------------------------
            */

            'sort_order' => $this->sort_order,

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active' => $this->isActive(),

            'status_label' => $this->isActive()
                ? 'Active'
                : 'Inactive',

            'status_color' => $this->isActive()
                ? 'green'
                : 'red',

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'products_count' => $productsCount,

            'children_count' => $childrenCount,

            'has_products' => ($productsCount ?? 0) > 0,

            'has_children' => ($childrenCount ?? 0) > 0,

            /*
            |--------------------------------------------------------------------------
            | Business Helpers
            |--------------------------------------------------------------------------
            */

            'can_be_deleted' =>
                ($productsCount ?? 0) === 0
                &&
                ($childrenCount ?? 0) === 0,

            /*
            |--------------------------------------------------------------------------
            | Dates
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