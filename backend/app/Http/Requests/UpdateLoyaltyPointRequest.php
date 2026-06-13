<?php

namespace App\Http\Requests;

use App\Models\LoyaltyPoint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateLoyaltyPointRequest
 *
 * Validates admin updates to a customer's LoyaltyPoint record,
 * such as tier upgrades, toggling active status, or adjusting metadata.
 * Direct point manipulation is handled via AddPointRequest / RedeemPointRequest.
 *
 * Laravel 13 | PHP 8.4
 */
class UpdateLoyaltyPointRequest extends FormRequest
{
    /**
     * Stop validation on first failure.
     */
    protected $stopOnFirstFailure = true;

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        return $this->user()?->can('manage loyalty')
            ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        if ($this->has('metadata') && $this->metadata === '') {
            $this->merge([
                'metadata' => null,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [

            'tier' => [
                'sometimes',
                'string',
                Rule::in([
                    LoyaltyPoint::TIER_BRONZE,
                    LoyaltyPoint::TIER_SILVER,
                    LoyaltyPoint::TIER_GOLD,
                    LoyaltyPoint::TIER_PLATINUM,
                ]),
            ],

            'tier_expires_at' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'published_at' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'metadata' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'metadata.*' => [
                'nullable',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Messages
    |--------------------------------------------------------------------------
    */

    public function messages(): array
    {
        return [

            'tier.in' =>
                'Tier loyalty yang dipilih tidak valid.',

            'tier_expires_at.after_or_equal' =>
                'Tanggal berakhir tier tidak boleh sebelum hari ini.',

            'published_at.after_or_equal' =>
                'Tanggal publish tidak boleh sebelum hari ini.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Friendly Attributes
    |--------------------------------------------------------------------------
    */

    public function attributes(): array
    {
        return [

            'tier' =>
                'tier loyalty',

            'tier_expires_at' =>
                'tanggal berakhir tier',

            'is_active' =>
                'status aktif',

            'published_at' =>
                'tanggal publish',

            'metadata' =>
                'metadata',
        ];
    }
}