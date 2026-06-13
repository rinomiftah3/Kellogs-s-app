<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourierResource extends JsonResource
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

            'name' =>
                $this->name,

            'code' =>
                $this->code,

            'provider' =>
                $this->provider,

            'display_name' =>
                $this->display_name,

            'description' =>
                $this->description,

            /*
            |--------------------------------------------------------------------------
            | Branding
            |--------------------------------------------------------------------------
            */

            'logo' =>
                $this->logo,

            'logo_url' =>
                $this->logo_url,

            'has_logo' =>
                $this->hasLogo(),

            /*
            |--------------------------------------------------------------------------
            | Contact Information
            |--------------------------------------------------------------------------
            */

            'website' =>
                $this->website,

            'has_website' =>
                $this->hasWebsite(),

            'contact_email' =>
                $this->contact_email,

            'contact_phone' =>
                $this->contact_phone,

            /*
            |--------------------------------------------------------------------------
            | Tracking
            |--------------------------------------------------------------------------
            */

            'tracking_url_template' =>
                $this->tracking_url_template,

            'has_tracking_template' =>
                $this->hasTrackingTemplate(),

            'supports_tracking' =>
                $this->supportsTracking(),

            /*
            |--------------------------------------------------------------------------
            | Features
            |--------------------------------------------------------------------------
            */

            'supports_cod' =>
                $this->supportsCod(),

            'supports_insurance' =>
                $this->supportsInsurance(),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active' =>
                $this->isActive(),

            'is_published' =>
                $this->isPublished(),

            'status_label' =>
                $this->isActive()
                    ? 'Active'
                    : 'Inactive',

            'status_color' =>
                $this->isActive()
                    ? 'green'
                    : 'red',

            /*
            |--------------------------------------------------------------------------
            | Sorting
            |--------------------------------------------------------------------------
            */

            'sort_order' =>
                (int) $this->sort_order,

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'metadata' =>
                $this->metadata,

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            'shipping_methods_count' =>
                $this->relationLoaded(
                    'shippingMethods'
                )
                    ? $this->shippingMethods->count()
                    : null,

            'shipments_count' =>
                $this->relationLoaded(
                    'shipments'
                )
                    ? $this->shipments->count()
                    : null,

            /*
            |--------------------------------------------------------------------------
            | Publication
            |--------------------------------------------------------------------------
            */

            'published_at' =>
                $this->published_at?->toISOString(),

            'published_at_human' =>
                $this->published_at?->diffForHumans(),

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