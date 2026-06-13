<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressResource extends JsonResource
{
    /**
     * Transform resource into array.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            /*
            |--------------------------------------------------------------------------
            | Identity
            |--------------------------------------------------------------------------
            */

            'id' =>
                $this->id,

            'customer_profile_id' =>
                $this->customer_profile_id,

            'label' =>
                $this->label,

            /*
            |--------------------------------------------------------------------------
            | Recipient
            |--------------------------------------------------------------------------
            */

            'recipient_name' =>
                $this->recipient_name,

            'recipient_phone' =>
                $this->recipient_phone,

            'recipient' =>
                $this->recipient(),

            /*
            |--------------------------------------------------------------------------
            | Address Information
            |--------------------------------------------------------------------------
            */

            'address' =>
                $this->address,

            'province' =>
                $this->province,

            'city' =>
                $this->city,

            'district' =>
                $this->district,

            'subdistrict' =>
                $this->subdistrict,

            'postal_code' =>
                $this->postal_code,

            'province_city' =>
                $this->provinceCity(),

            'short_address' =>
                $this->shortAddress(),

            'full_address' =>
                $this->fullAddress(),

            /*
            |--------------------------------------------------------------------------
            | Coordinates
            |--------------------------------------------------------------------------
            */

            'latitude' =>
                $this->latitude !== null
                    ? (float) $this->latitude
                    : null,

            'longitude' =>
                $this->longitude !== null
                    ? (float) $this->longitude
                    : null,

            'has_coordinates' =>
                $this->hasCoordinates(),

            'coordinate' =>
                $this->coordinate(),

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'notes' =>
                $this->notes,

            'has_notes' =>
                $this->hasNotes(),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_default' =>
                $this->isDefault(),

            'is_active' =>
                $this->isActive(),

            /*
            |--------------------------------------------------------------------------
            | Customer Information
            |--------------------------------------------------------------------------
            */

            'customer_name' =>
                $this->customerName(),

            'customer' =>
                $this->whenLoaded(
                    'customer',
                    fn () => [

                        'id' =>
                            $this->customer->id,

                        'customer_code' =>
                            $this->customer->customer_code,

                        'full_name' =>
                            $this->customer->full_name,
                    ]
                ),

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            'created_at' =>
                $this->created_at?->toISOString(),

            'created_at_human' =>
                $this->created_at?->diffForHumans(),

            'updated_at' =>
                $this->updated_at?->toISOString(),

            'updated_at_human' =>
                $this->updated_at?->diffForHumans(),
        ];
    }
}