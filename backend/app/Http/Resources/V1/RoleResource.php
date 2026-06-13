<?php

namespace App\Http\Resources\V1;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform resource into array.
     */
    public function toArray(
        Request $request
    ): array {

        $permissions = $this->resource->relationLoaded(
            'permissions'
        )
            ? $this->permissions
            : collect();

        $systemRoles = [

            User::ROLE_SUPER_ADMIN,

            User::ROLE_ADMIN,

            User::ROLE_STAFF,

            User::ROLE_CUSTOMER,
        ];

        return [

            /*
            |--------------------------------------------------------------------------
            | Identity
            |--------------------------------------------------------------------------
            */

            'id' => $this->id,

            'name' => $this->name,

            'guard_name' => $this->guard_name,

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'is_super_admin' =>
                $this->name ===
                User::ROLE_SUPER_ADMIN,

            'is_system_role' =>
                in_array(
                    $this->name,
                    $systemRoles,
                    true
                ),

            'can_be_deleted' =>
                ! in_array(
                    $this->name,
                    $systemRoles,
                    true
                ),

            /*
            |--------------------------------------------------------------------------
            | Permissions
            |--------------------------------------------------------------------------
            */

            'permissions' =>

                $permissions
                    ->pluck('name')
                    ->values(),

            'permissions_detail' =>

                $permissions
                    ->map(
                        fn ($permission) => [

                            'id' =>
                                $permission->id,

                            'name' =>
                                $permission->name,

                            'guard_name' =>
                                $permission->guard_name,
                        ]
                    )
                    ->values(),

            'permissions_count' =>
                $permissions->count(),

            /*
            |--------------------------------------------------------------------------
            | User Statistics
            |--------------------------------------------------------------------------
            */

            'users_count' =>
                $this->whenCounted(
                    'users'
                ),

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