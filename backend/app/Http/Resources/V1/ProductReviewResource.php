<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
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

            'customer_profile_id' =>
                $this->customer_profile_id,

            /*
            |--------------------------------------------------------------------------
            | Review Content
            |--------------------------------------------------------------------------
            */

            'title' =>
                $this->title,

            'review' =>
                $this->review,

            'has_review_text' =>
                $this->hasReviewText(),

            /*
            |--------------------------------------------------------------------------
            | Rating Information
            |--------------------------------------------------------------------------
            */

            'rating' =>
                (int) $this->rating,

            'rating_stars' =>
                $this->ratingStars(),

            'rating_percentage' =>
                $this->ratingPercentage(),

            /*
            |--------------------------------------------------------------------------
            | Verified Purchase
            |--------------------------------------------------------------------------
            */

            'is_verified_purchase' =>
                $this->isVerifiedPurchase(),

            /*
            |--------------------------------------------------------------------------
            | Helpful Statistics
            |--------------------------------------------------------------------------
            */

            'helpful_count' =>
                (int) $this->helpful_count,

            /*
            |--------------------------------------------------------------------------
            | Images
            |--------------------------------------------------------------------------
            */

            'images_count' =>
                $this->imagesCount(),

            'has_images' =>
                $this->hasImages(),

            'images' =>
                $this->whenLoaded(
                    'images',
                    fn () => $this->images
                        ->map(fn ($image) => [

                            'id' =>
                                $image->id,

                            'image_url' =>
                                $image->image_url,
                        ])
                        ->values()
                ),

            /*
            |--------------------------------------------------------------------------
            | Status & Moderation
            |--------------------------------------------------------------------------
            */

            'status' =>
                $this->status,

            'is_approved' =>
                $this->isApproved(),

            'is_pending' =>
                $this->isPending(),

            'is_rejected' =>
                $this->isRejected(),

            'status_label' =>
                match ($this->status) {

                    'approved'
                        => 'Approved',

                    'pending'
                        => 'Pending',

                    'rejected'
                        => 'Rejected',

                    default
                        => ucfirst(
                            (string) $this->status
                        ),
                },

            'status_color' =>
                match ($this->status) {

                    'approved'
                        => 'green',

                    'pending'
                        => 'yellow',

                    'rejected'
                        => 'red',

                    default
                        => 'gray',
                },

            'moderation_notes' =>
                $this->moderation_notes,

            /*
            |--------------------------------------------------------------------------
            | Product Information
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
            | Customer Information
            |--------------------------------------------------------------------------
            */

            'customer' =>
                $this->whenLoaded(
                    'customer',
                    fn () => [

                        'id' =>
                            $this->customer->id,

                        'full_name' =>
                            $this->customer->full_name,
                    ]
                ),

            'customer_name' =>
                $this->customerName(),

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