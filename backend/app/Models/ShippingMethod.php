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

class ShippingMethod extends Model
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

        'courier_id',

        'service_code',

        'service_name',

        'description',

        'estimated_min_days',

        'estimated_max_days',

        'supports_tracking',

        'supports_cod',

        'supports_insurance',

        'base_cost',

        'cost_per_kg',

        'minimum_weight',

        'maximum_weight',

        'free_shipping_threshold',

        'sla_hours',

        'sort_order',

        'is_featured',

        'is_active',

        'published_at',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'display_name',

        'delivery_estimation',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'estimated_min_days' => 'integer',

            'estimated_max_days' => 'integer',

            'supports_tracking' => 'boolean',

            'supports_cod' => 'boolean',

            'supports_insurance' => 'boolean',

            'base_cost' => 'decimal:2',

            'cost_per_kg' => 'decimal:2',

            'minimum_weight' => 'integer',

            'maximum_weight' => 'integer',

            'free_shipping_threshold' => 'decimal:2',

            'sla_hours' => 'integer',

            'sort_order' => 'integer',

            'is_featured' => 'boolean',

            'is_active' => 'boolean',

            'published_at' => 'datetime',

            'metadata' => 'array',
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

            ->useLogName('shipping_method')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Shipping Method {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function courier(): BelongsTo
    {
        return $this->belongsTo(
            Courier::class
        );
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(
            Shipment::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return $this->courier
            ? "{$this->courier->name} - {$this->service_name}"
            : $this->service_name;
    }

    public function getDeliveryEstimationAttribute(): string
    {
        if (
            $this->estimated_min_days ===
            $this->estimated_max_days
        ) {
            return "{$this->estimated_min_days} Hari";
        }

        return "{$this->estimated_min_days}-{$this->estimated_max_days} Hari";
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

    public function scopePublished(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'published_at'
        )->where(
            'published_at',
            '<=',
            now()
        );
    }

    public function scopeFeatured(
        Builder $query
    ): Builder {

        return $query->where(
            'is_featured',
            true
        );
    }

    public function scopeSupportsTracking(
        Builder $query
    ): Builder {

        return $query->where(
            'supports_tracking',
            true
        );
    }

    public function scopeSupportsCod(
        Builder $query
    ): Builder {

        return $query->where(
            'supports_cod',
            true
        );
    }

    public function scopeSupportsInsurance(
        Builder $query
    ): Builder {

        return $query->where(
            'supports_insurance',
            true
        );
    }

    public function scopeOrdered(
        Builder $query
    ): Builder {

        return $query->orderBy(
            'sort_order'
        );
    }

    public function scopeSearch(
        Builder $query,
        ?string $keyword
    ): Builder {

        return $query->when(

            filled($keyword),

            fn (Builder $query)

                => $query->where(

                    fn ($q)

                        => $q->where(
                            'service_name',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'service_code',
                            'like',
                            "%{$keyword}%"
                        )
                )
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

    public function isPublished(): bool
    {
        return !is_null(
            $this->published_at
        ) && $this->published_at <= now();
    }

    public function isFeatured(): bool
    {
        return (bool)
            $this->is_featured;
    }

    public function supportsTracking(): bool
    {
        return (bool)
            $this->supports_tracking;
    }

    public function supportsCod(): bool
    {
        return (bool)
            $this->supports_cod;
    }

    public function supportsInsurance(): bool
    {
        return (bool)
            $this->supports_insurance;
    }

    public function hasWeightLimit(): bool
    {
        return !is_null(
            $this->maximum_weight
        );
    }

    public function courierName(): ?string
    {
        return $this->courier?->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function isWeightAllowed(
        int $weightInGram
    ): bool {

        if (
            $weightInGram <
            $this->minimum_weight
        ) {
            return false;
        }

        if (
            !is_null(
                $this->maximum_weight
            )
            &&
            $weightInGram >
            $this->maximum_weight
        ) {
            return false;
        }

        return true;
    }

    public function calculateShippingCost(
        int $weightInGram
    ): float {

        $chargeableWeight = max(
            $weightInGram,
            (int) $this->minimum_weight
        );

        $weightKg = ceil(
            $chargeableWeight / 1000
        );

        return round(
            (float) $this->base_cost +
            (
                $weightKg *
                (float) $this->cost_per_kg
            ),
            2
        );
    }

    public function isEligibleForFreeShipping(
        float $subtotal
    ): bool {

        if (
            empty(
                $this->free_shipping_threshold
            )
        ) {
            return false;
        }

        return $subtotal >=
            $this->free_shipping_threshold;
    }

    public function activate(): void
    {
        $this->update([
            'is_active' => true,
        ]);
    }

    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
        ]);
    }

    public function publish(): void
    {
        $this->update([
            'published_at' => now(),
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