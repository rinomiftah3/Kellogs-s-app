<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class ProductOptionValue extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'product_option_id',

        'value',

        'code',

        'sort_order',

        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'sort_order' => 'integer',

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

            ->useLogName('product_option_value')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Product Option Value {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function option(): BelongsTo
    {
        return $this->belongsTo(
            ProductOption::class,
            'product_option_id'
        );
    }

    public function skuValues(): HasMany
    {
        return $this->hasMany(
            ProductSkuValue::class
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

    public function scopeOrdered(
        Builder $query
    ): Builder {

        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeByOption(
        Builder $query,
        ?int $optionId
    ): Builder {

        return $query->when(
            filled($optionId),
            fn (Builder $query)

                => $query->where(
                    'product_option_id',
                    $optionId
                )
        );
    }

    public function scopeUsed(
        Builder $query
    ): Builder {

        return $query->has(
            'skuValues'
        );
    }

    public function scopeUnused(
        Builder $query
    ): Builder {

        return $query->doesntHave(
            'skuValues'
        );
    }

    public function scopeSearch(
        Builder $query,
        ?string $keyword
    ): Builder {

        return $query->when(
            filled($keyword),
            fn (Builder $query)

                => $query->where(function ($q) use ($keyword) {

                    $q->where(
                        'value',
                        'like',
                        "%{$keyword}%"
                    )

                    ->orWhere(
                        'code',
                        'like',
                        "%{$keyword}%"
                    );
                })
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function hasCode(): bool
    {
        return !empty(
            $this->code
        );
    }

    public function hasSku(): bool
    {
        return $this->skuValues()
            ->exists();
    }

    public function isUsed(): bool
    {
        return $this->hasSku();
    }

    public function skuCount(): int
    {
        return $this->skuValues()
            ->count();
    }

    public function optionName(): ?string
    {
        return $this->option?->name;
    }

    public function optionCode(): ?string
    {
        return $this->option?->code;
    }

    public function productName(): ?string
    {
        return $this->option?->product?->name;
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