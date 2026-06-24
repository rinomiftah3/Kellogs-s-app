<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $provider
 * @property string|null $description
 * @property string|null $logo
 * @property string|null $website
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property string|null $tracking_url_template
 * @property bool $supports_tracking
 * @property bool $supports_cod
 * @property bool $supports_insurance
 * @property int $sort_order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read string $display_name
 * @property-read string|null $logo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Shipment> $shipments
 * @property-read int|null $shipments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShippingMethod> $shippingMethods
 * @property-read int|null $shipping_methods_count
 * @method static Builder<static>|Courier active()
 * @method static \Database\Factories\CourierFactory factory($count = null, $state = [])
 * @method static Builder<static>|Courier inactive()
 * @method static Builder<static>|Courier newModelQuery()
 * @method static Builder<static>|Courier newQuery()
 * @method static Builder<static>|Courier onlyTrashed()
 * @method static Builder<static>|Courier ordered()
 * @method static Builder<static>|Courier published()
 * @method static Builder<static>|Courier query()
 * @method static Builder<static>|Courier search(?string $keyword)
 * @method static Builder<static>|Courier supportsCod()
 * @method static Builder<static>|Courier supportsInsurance()
 * @method static Builder<static>|Courier supportsTracking()
 * @method static Builder<static>|Courier whereCode($value)
 * @method static Builder<static>|Courier whereContactEmail($value)
 * @method static Builder<static>|Courier whereContactPhone($value)
 * @method static Builder<static>|Courier whereCreatedAt($value)
 * @method static Builder<static>|Courier whereDeletedAt($value)
 * @method static Builder<static>|Courier whereDescription($value)
 * @method static Builder<static>|Courier whereId($value)
 * @method static Builder<static>|Courier whereIsActive($value)
 * @method static Builder<static>|Courier whereLogo($value)
 * @method static Builder<static>|Courier whereMetadata($value)
 * @method static Builder<static>|Courier whereName($value)
 * @method static Builder<static>|Courier whereProvider($value)
 * @method static Builder<static>|Courier wherePublishedAt($value)
 * @method static Builder<static>|Courier whereSortOrder($value)
 * @method static Builder<static>|Courier whereSupportsCod($value)
 * @method static Builder<static>|Courier whereSupportsInsurance($value)
 * @method static Builder<static>|Courier whereSupportsTracking($value)
 * @method static Builder<static>|Courier whereTrackingUrlTemplate($value)
 * @method static Builder<static>|Courier whereUpdatedAt($value)
 * @method static Builder<static>|Courier whereWebsite($value)
 * @method static Builder<static>|Courier withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Courier withoutTrashed()
 * @mixin \Eloquent
 */
class Courier extends Model
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

        'name',

        'code',

        'provider',

        'description',

        'logo',

        'website',

        'contact_email',

        'contact_phone',

        'tracking_url_template',

        'supports_tracking',

        'supports_cod',

        'supports_insurance',

        'sort_order',

        'is_active',

        'published_at',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appended Attributes
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'display_name',

        'logo_url',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'supports_tracking' => 'boolean',

            'supports_cod' => 'boolean',

            'supports_insurance' => 'boolean',

            'sort_order' => 'integer',

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

            ->useLogName('courier')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Courier {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function shippingMethods(): HasMany
    {
        return $this->hasMany(
            ShippingMethod::class
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
        return filled($this->provider)
            ? "{$this->name} ({$this->provider})"
            : $this->name;
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (blank($this->logo)) {
            return null;
        }

        return asset(
            'storage/' . $this->logo
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
                            'name',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'code',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'provider',
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

    public function hasLogo(): bool
    {
        return filled(
            $this->logo
        );
    }

    public function hasWebsite(): bool
    {
        return filled(
            $this->website
        );
    }

    public function hasTrackingTemplate(): bool
    {
        return filled(
            $this->tracking_url_template
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

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

    public function generateTrackingUrl(
        string $trackingNumber
    ): ?string {

        if (
            blank(
                $this->tracking_url_template
            )
        ) {
            return null;
        }

        return str_replace(
            '{tracking_number}',
            $trackingNumber,
            $this->tracking_url_template
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'code';
    }
}