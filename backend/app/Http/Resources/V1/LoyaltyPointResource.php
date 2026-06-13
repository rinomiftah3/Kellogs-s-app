<?php

namespace App\Http\Resources\V1;

use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyPointResource extends JsonResource
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
            | Point Information
            |--------------------------------------------------------------------------
            */

            'current_points' =>
                (int) $this->current_points,

            'available_points' =>
                (int) $this->available_points,

            'pending_points' =>
                (int) $this->pending_points,

            'earned_points' =>
                (int) $this->earned_points,

            'redeemed_points' =>
                (int) $this->redeemed_points,

            'expired_points' =>
                (int) $this->expired_points,

            'net_points' =>
                (int) $this->net_points,

            /*
            |--------------------------------------------------------------------------
            | Lifetime Statistics
            |--------------------------------------------------------------------------
            */

            'lifetime_points' =>
                (int) $this->lifetime_points,

            'lifetime_orders' =>
                (int) $this->lifetime_orders,

            'lifetime_spending' =>
                (float) $this->lifetime_spending,

            'lifetime_spending_formatted' =>
                'Rp ' .
                number_format(
                    $this->lifetime_spending,
                    0,
                    ',',
                    '.'
                ),

            /*
            |--------------------------------------------------------------------------
            | Tier Information
            |--------------------------------------------------------------------------
            */

            'tier' =>
                $this->tier,

            'tier_label' =>
                $this->tier_label,

            'tier_badge' =>
                $this->tier_badge,

            'is_bronze' =>
                $this->isBronze(),

            'is_silver' =>
                $this->isSilver(),

            'is_gold' =>
                $this->isGold(),

            'is_platinum' =>
                $this->isPlatinum(),

            'is_expired' =>
                $this->is_expired,

            'is_tier_expired' =>
                $this->isTierExpired(),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active' =>
                $this->isActive(),

            'is_published' =>
                $this->isPublished(),

            /*
            |--------------------------------------------------------------------------
            | Expiration Information
            |--------------------------------------------------------------------------
            */

            'total_expiration_events' =>
                (int) $this->total_expiration_events,

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            'customer' =>
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
            | Tier Timeline
            |--------------------------------------------------------------------------
            */

            'tier_upgraded_at' =>
                $this->tier_upgraded_at?->toISOString(),

            'tier_upgraded_at_human' =>
                $this->tier_upgraded_at?->diffForHumans(),

            'tier_expires_at' =>
                $this->tier_expires_at?->toISOString(),

            'tier_expires_at_human' =>
                $this->tier_expires_at?->diffForHumans(),

            /*
            |--------------------------------------------------------------------------
            | Activity Timeline
            |--------------------------------------------------------------------------
            */

            'last_earned_at' =>
                $this->last_earned_at?->toISOString(),

            'last_earned_at_human' =>
                $this->last_earned_at?->diffForHumans(),

            'last_redeemed_at' =>
                $this->last_redeemed_at?->toISOString(),

            'last_redeemed_at_human' =>
                $this->last_redeemed_at?->diffForHumans(),

            'last_expired_at' =>
                $this->last_expired_at?->toISOString(),

            'last_expired_at_human' =>
                $this->last_expired_at?->diffForHumans(),

            'last_activity_at' =>
                $this->last_activity_at?->toISOString(),

            'last_activity_at_human' =>
                $this->last_activity_at?->diffForHumans(),

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
            | Additional Information
            |--------------------------------------------------------------------------
            */

            'metadata' =>
                $this->metadata,

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