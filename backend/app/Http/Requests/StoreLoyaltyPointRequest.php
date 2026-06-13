<?php

namespace App\Http\Requests;

use App\Models\LoyaltyPoint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoyaltyPointRequest extends FormRequest
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

            'customer_profile_id' => [
                'required',
                'integer',
                'exists:customer_profiles,id',
                'unique:loyalty_points,customer_profile_id',
            ],

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

            'customer_profile_id.required' =>
                'Customer wajib dipilih.',

            'customer_profile_id.exists' =>
                'Customer yang dipilih tidak ditemukan.',

            'customer_profile_id.unique' =>
                'Customer tersebut sudah memiliki akun loyalty.',

            'tier.in' =>
                'Tier loyalty tidak valid.',

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

            'customer_profile_id' => 'customer',

            'tier' => 'tier loyalty',

            'is_active' => 'status aktif',

            'published_at' => 'tanggal publish',

            'metadata' => 'metadata',
        ];
    }
}