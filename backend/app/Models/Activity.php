<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Event Constants
    |--------------------------------------------------------------------------
    */

    public const EVENT_CREATED = 'created';

    public const EVENT_UPDATED = 'updated';

    public const EVENT_DELETED = 'deleted';

    public const EVENT_RESTORED = 'restored';

    public const EVENT_LOGIN = 'login';

    public const EVENT_LOGOUT = 'logout';

    public const EVENT_APPROVED = 'approved';

    public const EVENT_REJECTED = 'rejected';

    public const EVENT_PUBLISHED = 'published';

    public const EVENT_CANCELLED = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'subject_type_name',
        'causer_name',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'attribute_changes' => 'array',

        'properties' => 'array',

        'created_at' => 'datetime',

        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getSubjectTypeNameAttribute(): ?string
    {
        if (! $this->subject_type) {
            return null;
        }

        return class_basename(
            $this->subject_type
        );
    }

    public function getCauserNameAttribute(): ?string
    {
        return $this->causer?->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Relations Helpers
    |--------------------------------------------------------------------------
    */

    public function causerUser(): ?User
    {
        return $this->causer instanceof User
            ? $this->causer
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Event Helpers
    |--------------------------------------------------------------------------
    */

    public function isCreated(): bool
    {
        return $this->event ===
            self::EVENT_CREATED;
    }

    public function isUpdated(): bool
    {
        return $this->event ===
            self::EVENT_UPDATED;
    }

    public function isDeleted(): bool
    {
        return $this->event ===
            self::EVENT_DELETED;
    }

    public function isRestored(): bool
    {
        return $this->event ===
            self::EVENT_RESTORED;
    }

    public function isLogin(): bool
    {
        return $this->event ===
            self::EVENT_LOGIN;
    }

    public function isLogout(): bool
    {
        return $this->event ===
            self::EVENT_LOGOUT;
    }

    public function isApproved(): bool
    {
        return $this->event ===
            self::EVENT_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->event ===
            self::EVENT_REJECTED;
    }

    /*
    |--------------------------------------------------------------------------
    | Subject Helpers
    |--------------------------------------------------------------------------
    */

    public function hasSubject(): bool
    {
        return ! is_null(
            $this->subject_id
        );
    }

    public function hasCauser(): bool
    {
        return ! is_null(
            $this->causer_id
        );
    }

    public function isBatchProcess(): bool
    {
        return ! empty(
            $this->batch_uuid
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeEvent(
        Builder $query,
        string $event
    ): Builder {

        return $query->where(
            'event',
            $event
        );
    }

    public function scopeLogName(
        Builder $query,
        string $logName
    ): Builder {

        return $query->where(
            'log_name',
            $logName
        );
    }

    public function scopeCauser(
        Builder $query,
        int $userId
    ): Builder {

        return $query->where(
            'causer_id',
            $userId
        );
    }

    public function scopeToday(
        Builder $query
    ): Builder {

        return $query->whereDate(
            'created_at',
            today()
        );
    }

    public function scopeThisWeek(
        Builder $query
    ): Builder {

        return $query->where(
            'created_at',
            '>=',
            now()->startOfWeek()
        );
    }

    public function scopeThisMonth(
        Builder $query
    ): Builder {

        return $query->where(
            'created_at',
            '>=',
            now()->startOfMonth()
        );
    }

   

    public function scopeLatest(
        Builder $query
    ): Builder {

        return $query->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | Analytics Helpers
    |--------------------------------------------------------------------------
    */

    public function isSystemActivity(): bool
    {
        return is_null(
            $this->causer_id
        );
    }

    public function isUserActivity(): bool
    {
        return ! is_null(
            $this->causer_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}