<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerProfileResource extends JsonResource
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

            'customer_code' =>
                $this->customer_code,

            'full_name' =>
                $this->full_name,

            'full_identity' =>
                $this->fullIdentity(),

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            'user_id' =>
                $this->user_id,

            'user_email' =>
                $this->userEmail(),

            'user' =>
                $this->whenLoaded(
                    'user',
                    fn () => [

                        'id' =>
                            $this->user->id,

                        'name' =>
                            $this->user->name,

                        'email' =>
                            $this->user->email,
                    ]
                ),

            /*
            |--------------------------------------------------------------------------
            | Personal Information
            |--------------------------------------------------------------------------
            */

            'phone' =>
                $this->phone,

            'has_phone' =>
                $this->hasPhone(),

            'gender' =>
                $this->gender,

            'is_male' =>
                $this->isMale(),

            'is_female' =>
                $this->isFemale(),

            'birth_date' =>
                $this->birth_date?->toDateString(),

            'age' =>
                $this->age(),

            'bio' =>
                $this->bio,

            /*
            |--------------------------------------------------------------------------
            | Avatar
            |--------------------------------------------------------------------------
            */

            'avatar' =>
                $this->avatar,

            'avatar_url' =>
                $this->avatarUrl(),

            'has_avatar' =>
                $this->hasAvatar(),

            /*
            |--------------------------------------------------------------------------
            | Membership
            |--------------------------------------------------------------------------
            */

            'membership_level' =>
                $this->membership_level,

            'membership_badge' =>
                $this->membershipBadge(),

            'is_regular' =>
                $this->isRegular(),

            'is_silver' =>
                $this->isSilver(),

            'is_gold' =>
                $this->isGold(),

            'is_platinum' =>
                $this->isPlatinum(),

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'total_points' =>
                (int) $this->total_points,

            'has_points' =>
                $this->hasPoints(),

            'total_orders' =>
                (int) $this->total_orders,

            'has_orders' =>
                $this->hasOrders(),

            'total_spent' =>
                (float) $this->total_spent,

            'total_spent_formatted' =>
                'Rp ' .
                $this->formattedTotalSpent(),

            /*
            |--------------------------------------------------------------------------
            | Loyalty
            |--------------------------------------------------------------------------
            */

            'loyalty_point' =>
                $this->whenLoaded(
                    'loyaltyPoint',
                    fn () => [

                        'id' =>
                            $this->loyaltyPoint->id,

                        'total_points' =>
                            $this->loyaltyPoint->total_points
                                ?? null,
                    ]
                ),

            /*
            |--------------------------------------------------------------------------
            | Subscription Preferences
            |--------------------------------------------------------------------------
            */

            'email_subscribed' =>
                $this->isSubscribedEmail(),

            'sms_subscribed' =>
                $this->isSubscribedSms(),

            'push_subscribed' =>
                $this->isSubscribedPush(),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active' =>
                $this->isActive(),

            /*
            |--------------------------------------------------------------------------
            | Last Order
            |--------------------------------------------------------------------------
            */

            'last_order_at' =>
                $this->last_order_at?->toISOString(),

            'last_order_human' =>
                $this->last_order_at?->diffForHumans(),

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