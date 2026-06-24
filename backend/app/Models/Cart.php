<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * Cart Model
 *
 * Shopping Cart Container
 *
 * Laravel 13
 * PHP 8.4
 *
 * @property int $id
 * @property int $customer_profile_id
 * @property int $total_items
 * @property numeric $subtotal
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\CustomerProfile|null $customerProfile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items
 * @property-read int|null $items_count
 * @method static Builder<static>|Cart abandoned(int $minutes = 60)
 * @method static Builder<static>|Cart active()
 * @method static Builder<static>|Cart byCustomer(?int $customerProfileId)
 * @method static Builder<static>|Cart empty()
 * @method static Builder<static>|Cart expired()
 * @method static \Database\Factories\CartFactory factory($count = null, $state = [])
 * @method static Builder<static>|Cart inactive()
 * @method static Builder<static>|Cart latestActivity()
 * @method static Builder<static>|Cart newModelQuery()
 * @method static Builder<static>|Cart newQuery()
 * @method static Builder<static>|Cart notEmpty()
 * @method static Builder<static>|Cart notExpired()
 * @method static Builder<static>|Cart onlyTrashed()
 * @method static Builder<static>|Cart query()
 * @method static Builder<static>|Cart whereCreatedAt($value)
 * @method static Builder<static>|Cart whereCustomerProfileId($value)
 * @method static Builder<static>|Cart whereDeletedAt($value)
 * @method static Builder<static>|Cart whereExpiresAt($value)
 * @method static Builder<static>|Cart whereId($value)
 * @method static Builder<static>|Cart whereIsActive($value)
 * @method static Builder<static>|Cart whereLastActivityAt($value)
 * @method static Builder<static>|Cart whereSubtotal($value)
 * @method static Builder<static>|Cart whereTotalItems($value)
 * @method static Builder<static>|Cart whereUpdatedAt($value)
 * @method static Builder<static>|Cart withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Cart withoutTrashed()
 * @mixin \Eloquent
 */
class Cart extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'customer_profile_id',

        'total_items',

        'subtotal',

        'is_active',

        'last_activity_at',

        'expires_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'total_items' => 'integer',

            'subtotal' => 'decimal:2',

            'is_active' => 'boolean',

            'last_activity_at' => 'datetime',

            'expires_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()

            ->useLogName('cart')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
                'last_activity_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Cart {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(
            CustomerProfile::class
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            CartItem::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(
        Builder $query
    ): Builder {

        return $query->where(
            'is_active',
            true
        );
    }

    public function scopeInactive(
        Builder $query
    ): Builder {

        return $query->where(
            'is_active',
            false
        );
    }

    public function scopeExpired(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'expires_at'
        )->where(
            'expires_at',
            '<',
            now()
        );
    }

    public function scopeNotExpired(
        Builder $query
    ): Builder {

        return $query->where(
            function ($query) {

                $query->whereNull(
                    'expires_at'
                )

                ->orWhere(
                    'expires_at',
                    '>=',
                    now()
                );
            }
        );
    }

    public function scopeEmpty(
        Builder $query
    ): Builder {

        return $query->where(
            'total_items',
            '<=',
            0
        );
    }

    public function scopeNotEmpty(
        Builder $query
    ): Builder {

        return $query->where(
            'total_items',
            '>',
            0
        );
    }

    public function scopeAbandoned(
        Builder $query,
        int $minutes = 60
    ): Builder {

        return $query->where(
            'last_activity_at',
            '<',
            now()->subMinutes($minutes)
        );
    }

    public function scopeByCustomer(
        Builder $query,
        ?int $customerProfileId
    ): Builder {

        return $query->when(
            filled($customerProfileId),

            fn (Builder $query)

                => $query->where(
                    'customer_profile_id',
                    $customerProfileId
                )
        );
    }

    public function scopeLatestActivity(
        Builder $query
    ): Builder {

        return $query->orderByDesc(
            'last_activity_at'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return (bool)
            $this->is_active;
    }

    public function isExpired(): bool
    {
        return $this->expires_at
            ? $this->expires_at->isPast()
            : false;
    }

    public function isEmpty(): bool
    {
        return $this->total_items <= 0;
    }

    public function hasItems(): bool
    {
        return $this->total_items > 0;
    }

    public function isAbandoned(
        int $minutes = 60
    ): bool {

        if (!$this->last_activity_at) {
            return false;
        }

        return $this->last_activity_at
            ->lt(now()->subMinutes($minutes));
    }

    public function customerName(): ?string
    {
        return $this->customerProfile?->full_name;
    }

    public function itemCount(): int
    {
        return (int) $this->items()
            ->sum('quantity');
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function refreshSummary(): void
    {
        $totalItems = (int) $this->items()
            ->sum('quantity');

        $subtotal = (float) $this->items()
            ->sum('subtotal');

        $this->update([
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'last_activity_at' => now(),
        ]);
    }

    public function touchActivity(): void
    {
        $this->update([
            'last_activity_at' => now(),
        ]);
    }

    public function clearCart(): void
    {
        $this->items()->delete();

        $this->update([
            'total_items' => 0,
            'subtotal' => 0,
            'last_activity_at' => now(),
        ]);
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