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