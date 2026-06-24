<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

/**
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $event
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property \Illuminate\Support\Collection<array-key, mixed>|null $attribute_changes
 * @property \Illuminate\Support\Collection<array-key, mixed>|null $properties
 * @property string|null $batch_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|null $causer
 * @property-read string|null $causer_name
 * @property-read string|null $subject_type_name
 * @property-read \Illuminate\Database\Eloquent\Model|null $subject
 * @method static Builder<static>|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static Builder<static>|Activity causer(int $userId)
 * @method static Builder<static>|Activity event(string $event)
 * @method static \Database\Factories\ActivityFactory factory($count = null, $state = [])
 * @method static Builder<static>|Activity forEvent(\Spatie\Activitylog\Enums\ActivityEvent|string $event)
 * @method static Builder<static>|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder<static>|Activity inLog(array|string ...$logNames)
 * @method static Builder<static>|Activity latest()
 * @method static Builder<static>|Activity logName(string $logName)
 * @method static Builder<static>|Activity newModelQuery()
 * @method static Builder<static>|Activity newQuery()
 * @method static Builder<static>|Activity query()
 * @method static Builder<static>|Activity thisMonth()
 * @method static Builder<static>|Activity thisWeek()
 * @method static Builder<static>|Activity today()
 * @method static Builder<static>|Activity whereAttributeChanges($value)
 * @method static Builder<static>|Activity whereBatchUuid($value)
 * @method static Builder<static>|Activity whereCauserId($value)
 * @method static Builder<static>|Activity whereCauserType($value)
 * @method static Builder<static>|Activity whereCreatedAt($value)
 * @method static Builder<static>|Activity whereDescription($value)
 * @method static Builder<static>|Activity whereEvent($value)
 * @method static Builder<static>|Activity whereId($value)
 * @method static Builder<static>|Activity whereLogName($value)
 * @method static Builder<static>|Activity whereProperties($value)
 * @method static Builder<static>|Activity whereSubjectId($value)
 * @method static Builder<static>|Activity whereSubjectType($value)
 * @method static Builder<static>|Activity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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