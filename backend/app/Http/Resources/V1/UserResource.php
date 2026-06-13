<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform resource into array.
     */
    public function toArray(
        Request $request
    ): array {

        $roles = collect();

        if (
            $this->resource->relationLoaded(
                'roles'
            )
        ) {
            $roles = collect(
                $this->resource->roles
            );
        }

        return [

            /*
            |--------------------------------------------------------------------------
            | Identity
            |--------------------------------------------------------------------------
            */

            'id' => $this->id,

            'name' => $this->name,

            'display_name' => $this->display_name,

            'email' => $this->email,

            /*
            |--------------------------------------------------------------------------
            | Avatar
            |--------------------------------------------------------------------------
            */

            'avatar' => $this->avatar,

            'avatar_url' => $this->avatar_url,

            'has_avatar' => $this->hasAvatar(),

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */

            'roles' => $roles
                ->map(fn ($role) => [

                    'id' => $role->id,

                    'name' => $role->name,
                ])
                ->values(),

            'primary_role' => $roles
                ->first()?->name,

            'roles_count' => $roles
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Permissions
            |--------------------------------------------------------------------------
            */

            'permissions_count' => $this->resource
                ->relationLoaded('permissions')
                    ? $this->permissions->count()
                    : null,

            /*
            |--------------------------------------------------------------------------
            | Customer
            |--------------------------------------------------------------------------
            */

            'customer_profile_id' => $this->whenLoaded(
                'customerProfile',
                fn () => $this->customerProfile?->id
            ),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active' => $this->isActive(),

            'is_verified' => $this->isVerified(),

            'is_super_admin' => $this->isSuperAdmin(),

            'is_online' => $this->isOnline(),

            /*
            |--------------------------------------------------------------------------
            | Verification
            |--------------------------------------------------------------------------
            */

            'email_verified_at' =>
                $this->email_verified_at?->toISOString(),

            /*
            |--------------------------------------------------------------------------
            | Login Information
            |--------------------------------------------------------------------------
            */

            'last_login_at' =>
                $this->last_login_at?->toISOString(),

            'last_login_human' =>
                $this->last_login_at?->diffForHumans(),

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