<?php

namespace App\Http\Resources\V1;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform resource into array.
     */
    public function toArray(
        Request $request
    ): array {

        $properties = $this->properties ?? [];

        return [

            /*
            |--------------------------------------------------------------------------
            | Identity
            |--------------------------------------------------------------------------
            */

            'id' => $this->id,

            'log_name' =>
                $this->log_name,

            'event' =>
                $this->event,

            'event_label' =>
                Str::headline(
                    str_replace(
                        '_',
                        ' ',
                        (string) $this->event
                    )
                ),

            /*
            |--------------------------------------------------------------------------
            | Event Helpers
            |--------------------------------------------------------------------------
            */

            'is_created' =>
                $this->isCreated(),

            'is_updated' =>
                $this->isUpdated(),

            'is_deleted' =>
                $this->isDeleted(),

            'is_restored' =>
                $this->isRestored(),

            'is_login' =>
                $this->isLogin(),

            'is_logout' =>
                $this->isLogout(),

            'is_approved' =>
                $this->isApproved(),

            'is_rejected' =>
                $this->isRejected(),

            /*
            |--------------------------------------------------------------------------
            | UI Helpers
            |--------------------------------------------------------------------------
            */

            'event_color' =>
                $this->eventColor(),

            'event_icon' =>
                $this->eventIcon(),

            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

            'description' =>
                $this->description,

            /*
            |--------------------------------------------------------------------------
            | Subject
            |--------------------------------------------------------------------------
            */

            'subject' => [

                'type' =>
                    $this->subject_type,

                'type_name' =>
                    $this->subject_type_name,

                'id' =>
                    $this->subject_id,
            ],

            'has_subject' =>
                $this->hasSubject(),

            /*
            |--------------------------------------------------------------------------
            | Causer
            |--------------------------------------------------------------------------
            */

            'causer' => [

                'id' =>
                    $this->causer?->id,

                'name' =>
                    $this->causer_name,

                'email' =>
                    $this->causer?->email,
            ],

            'has_causer' =>
                $this->hasCauser(),

            'is_system_activity' =>
                $this->isSystemActivity(),

            'is_user_activity' =>
                $this->isUserActivity(),

            /*
            |--------------------------------------------------------------------------
            | Changes
            |--------------------------------------------------------------------------
            */

            'changes' => [

                'old' =>
                    data_get(
                        $properties,
                        'old',
                        []
                    ),

                'new' =>
                    data_get(
                        $properties,
                        'attributes',
                        []
                    ),
            ],

            /*
            |--------------------------------------------------------------------------
            | Properties
            |--------------------------------------------------------------------------
            */

            'properties' =>
                $properties,

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'metadata' => [

                'ip' =>
                    data_get(
                        $properties,
                        'ip'
                    ),

                'user_agent' =>
                    data_get(
                        $properties,
                        'user_agent'
                    ),

                'batch_uuid' =>
                    $this->batch_uuid,

                'is_batch_process' =>
                    $this->isBatchProcess(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'created_at' =>
                $this->created_at?->toISOString(),

            'created_at_human' =>
                $this->created_at?->diffForHumans(),

            'created_at_formatted' =>
                $this->created_at?->format(
                    'd M Y H:i:s'
                ),

            'updated_at' =>
                $this->updated_at?->toISOString(),

            'updated_at_human' =>
                $this->updated_at?->diffForHumans(),
        ];
    }

    /**
     * Event color helper.
     */
    private function eventColor(): string
    {
        return match ($this->event) {

            Activity::EVENT_CREATED,
            'user_created',
            'role_created',
            'product_created',
            'category_created'
                => 'green',

            Activity::EVENT_UPDATED,
            'user_updated',
            'role_updated',
            'product_updated',
            'category_updated'
                => 'yellow',

            Activity::EVENT_DELETED,
            'user_deleted',
            'role_deleted',
            'product_deleted',
            'category_deleted'
                => 'red',

            Activity::EVENT_LOGIN
                => 'blue',

            Activity::EVENT_LOGOUT
                => 'slate',

            Activity::EVENT_APPROVED
                => 'emerald',

            Activity::EVENT_REJECTED
                => 'rose',

            Activity::EVENT_RESTORED
                => 'cyan',

            Activity::EVENT_CANCELLED
                => 'orange',

            default
                => 'gray',
        };
    }

    /**
     * Event icon helper.
     */
    private function eventIcon(): string
    {
        return match ($this->event) {

            Activity::EVENT_CREATED,
            'user_created',
            'role_created',
            'product_created',
            'category_created'
                => 'plus',

            Activity::EVENT_UPDATED,
            'user_updated',
            'role_updated',
            'product_updated',
            'category_updated'
                => 'edit',

            Activity::EVENT_DELETED,
            'user_deleted',
            'role_deleted',
            'product_deleted',
            'category_deleted'
                => 'trash',

            Activity::EVENT_LOGIN
                => 'login',

            Activity::EVENT_LOGOUT
                => 'logout',

            Activity::EVENT_APPROVED
                => 'check-circle',

            Activity::EVENT_REJECTED
                => 'x-circle',

            Activity::EVENT_RESTORED
                => 'refresh-cw',

            Activity::EVENT_CANCELLED
                => 'ban',

            default
                => 'activity',
        };
    }
}