<?php

namespace App\Http\Resources\V1;

use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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

            'slug' =>
                $this->slug,

            'short_description' =>
                $this->short_description,

            'description' =>
                $this->description,

            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */

            'thumbnail' =>
                $this->thumbnail,

            'thumbnail_url' =>
                $this->thumbnail_url,

            'primary_image_url' =>
                $this->primary_image_url,

            'has_image' =>
                $this->hasImage(),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' =>
                $this->status,

            'status_label' =>
                match ($this->status) {

                    Product::STATUS_DRAFT =>
                        'Draft',

                    Product::STATUS_ACTIVE =>
                        'Active',

                    Product::STATUS_INACTIVE =>
                        'Inactive',

                    Product::STATUS_ARCHIVED =>
                        'Archived',

                    default =>
                        ucfirst(
                            (string) $this->status
                        ),
                },

            'is_active' =>
                (bool) $this->is_active,

            'is_featured' =>
                (bool) $this->is_featured,

            'is_published' =>
                $this->isPublished(),

            /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */

            'category_id' =>
                $this->category_id,

            'category' =>
                $this->whenLoaded(
                    'category',
                    fn () => [

                        'id' =>
                            $this->category->id,

                        'name' =>
                            $this->category->name,

                        'slug' =>
                            $this->category->slug,
                    ]
                ),

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'review_count' =>
                $this->review_count,

            'sku_count' =>
                $this->sku_count,

            'has_reviews' =>
                $this->hasReviews(),

            'has_sku' =>
                $this->hasSku(),

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
                $this->hasSku(),

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