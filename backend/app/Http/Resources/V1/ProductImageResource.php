<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
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
            | Image Information
            |--------------------------------------------------------------------------
            */

            'image_path' =>
                $this->image_url,

            'image_url' =>
                $this->full_image_url,

            'alt_text' =>
                $this->alt_text,

            'has_image' =>
                $this->hasImage(),

            'has_alt_text' =>
                $this->hasAltText(),
            /*
            |--------------------------------------------------------------------------
            | Display
            |--------------------------------------------------------------------------
            */

            'sort_order' =>
                $this->sort_order,

            'is_primary' =>
                $this->isPrimary(),

            'is_active' =>
                $this->isActive(),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status_label' =>
                $this->isActive()
                    ? 'Active'
                    : 'Inactive',

            'status_color' =>
                $this->isActive()
                    ? 'green'
                    : 'red',

            'image_type' =>
                $this->isPrimary()
                    ? 'Primary'
                    : 'Gallery',

            /*
            |--------------------------------------------------------------------------
            | Business Helpers
            |--------------------------------------------------------------------------
            */

            'can_be_deleted' =>
                true,

            'can_be_primary' =>
                ! $this->isPrimary(),

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