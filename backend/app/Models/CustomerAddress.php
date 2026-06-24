<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * CustomerAddress Model
 *
 * Customer Address Book
 *
 * Enterprise Ready
 *
 * @property int $id
 * @property int $customer_profile_id
 * @property string $label
 * @property string $recipient_name
 * @property string $recipient_phone
 * @property string $address
 * @property string $province
 * @property string $city
 * @property string $district
 * @property string $subdistrict
 * @property string $postal_code
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property bool $is_default
 * @property bool $is_active
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\CustomerProfile|null $customer
 * @method static Builder<static>|CustomerAddress active()
 * @method static Builder<static>|CustomerAddress byCity(?string $city)
 * @method static Builder<static>|CustomerAddress byCustomer(?int $customerId)
 * @method static Builder<static>|CustomerAddress byPostalCode(?string $postalCode)
 * @method static Builder<static>|CustomerAddress byProvince(?string $province)
 * @method static Builder<static>|CustomerAddress default()
 * @method static \Database\Factories\CustomerAddressFactory factory($count = null, $state = [])
 * @method static Builder<static>|CustomerAddress inactive()
 * @method static Builder<static>|CustomerAddress newModelQuery()
 * @method static Builder<static>|CustomerAddress newQuery()
 * @method static Builder<static>|CustomerAddress onlyTrashed()
 * @method static Builder<static>|CustomerAddress query()
 * @method static Builder<static>|CustomerAddress search(?string $keyword)
 * @method static Builder<static>|CustomerAddress whereAddress($value)
 * @method static Builder<static>|CustomerAddress whereCity($value)
 * @method static Builder<static>|CustomerAddress whereCreatedAt($value)
 * @method static Builder<static>|CustomerAddress whereCustomerProfileId($value)
 * @method static Builder<static>|CustomerAddress whereDeletedAt($value)
 * @method static Builder<static>|CustomerAddress whereDistrict($value)
 * @method static Builder<static>|CustomerAddress whereId($value)
 * @method static Builder<static>|CustomerAddress whereIsActive($value)
 * @method static Builder<static>|CustomerAddress whereIsDefault($value)
 * @method static Builder<static>|CustomerAddress whereLabel($value)
 * @method static Builder<static>|CustomerAddress whereLatitude($value)
 * @method static Builder<static>|CustomerAddress whereLongitude($value)
 * @method static Builder<static>|CustomerAddress whereNotes($value)
 * @method static Builder<static>|CustomerAddress wherePostalCode($value)
 * @method static Builder<static>|CustomerAddress whereProvince($value)
 * @method static Builder<static>|CustomerAddress whereRecipientName($value)
 * @method static Builder<static>|CustomerAddress whereRecipientPhone($value)
 * @method static Builder<static>|CustomerAddress whereSubdistrict($value)
 * @method static Builder<static>|CustomerAddress whereUpdatedAt($value)
 * @method static Builder<static>|CustomerAddress withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|CustomerAddress withoutTrashed()
 * @mixin \Eloquent
 */
class CustomerAddress extends Model
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

        'label',

        'recipient_name',

        'recipient_phone',

        'address',

        'province',

        'city',

        'district',

        'subdistrict',

        'postal_code',

        'latitude',

        'longitude',

        'is_default',

        'is_active',

        'notes',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'latitude' => 'decimal:7',

            'longitude' => 'decimal:7',

            'is_default' => 'boolean',

            'is_active' => 'boolean',
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

            ->useLogName('customer_address')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn(string $eventName)
                    => "Customer Address {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            CustomerProfile::class,
            'customer_profile_id'
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

    public function scopeDefault(
        Builder $query
    ): Builder {

        return $query->where(
            'is_default',
            true
        );
    }

    public function scopeByCustomer(
        Builder $query,
        ?int $customerId
    ): Builder {

        return $query->when(
            filled($customerId),

            fn(Builder $query)

                => $query->where(
                    'customer_profile_id',
                    $customerId
                )
        );
    }

    public function scopeByProvince(
        Builder $query,
        ?string $province
    ): Builder {

        return $query->when(
            filled($province),

            fn(Builder $query)

                => $query->where(
                    'province',
                    $province
                )
        );
    }

    public function scopeByCity(
        Builder $query,
        ?string $city
    ): Builder {

        return $query->when(
            filled($city),

            fn(Builder $query)

                => $query->where(
                    'city',
                    $city
                )
        );
    }

    public function scopeByPostalCode(
        Builder $query,
        ?string $postalCode
    ): Builder {

        return $query->when(
            filled($postalCode),

            fn(Builder $query)

                => $query->where(
                    'postal_code',
                    $postalCode
                )
        );
    }

    public function scopeSearch(
        Builder $query,
        ?string $keyword
    ): Builder {

        return $query->when(
            filled($keyword),

            fn(Builder $query)

                => $query->where(
                    fn($q)

                        => $q->where(
                            'label',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'recipient_name',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'recipient_phone',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'city',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'province',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'postal_code',
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

    public function isDefault(): bool
    {
        return (bool)
            $this->is_default;
    }

    public function isActive(): bool
    {
        return (bool)
            $this->is_active;
    }

    public function hasCoordinates(): bool
    {
        return !is_null(
            $this->latitude
        )
        &&
        !is_null(
            $this->longitude
        );
    }

    public function hasNotes(): bool
    {
        return !empty(
            $this->notes
        );
    }

    public function customerName(): ?string
    {
        return $this->customer?->full_name;
    }

    public function fullAddress(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->subdistrict,
            $this->district,
            $this->city,
            $this->province,
            $this->postal_code,
        ]));
    }

    public function shortAddress(): string
    {
        return implode(', ', array_filter([
            $this->city,
            $this->province,
        ]));
    }

    public function provinceCity(): string
    {
        return implode(', ', array_filter([
            $this->city,
            $this->province,
        ]));
    }

    public function coordinate(): ?string
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        return "{$this->latitude},{$this->longitude}";
    }

    public function recipient(): string
    {
        return "{$this->recipient_name} ({$this->recipient_phone})";
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function markAsDefault(): bool
    {
        static::where(
            'customer_profile_id',
            $this->customer_profile_id
        )->update([
            'is_default' => false,
        ]);

        return $this->update([
            'is_default' => true,
        ]);
    }

    public function activate(): bool
    {
        return $this->update([
            'is_active' => true,
        ]);
    }

    public function deactivate(): bool
    {
        return $this->update([
            'is_active' => false,
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