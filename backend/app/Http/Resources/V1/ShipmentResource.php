<?php

namespace App\Http\Resources\V1;


use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
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

            'order_id' =>
                $this->order_id,

            'shipping_method_id' =>
                $this->shipping_method_id,

            'shipment_number' =>
                $this->shipment_number,

            /*
            |--------------------------------------------------------------------------
            | Tracking
            |--------------------------------------------------------------------------
            */

            'tracking_number' =>
                $this->tracking_number,

            'has_tracking_number' =>
                $this->hasTrackingNumber(),

            'tracking_url' =>
                $this->tracking_url,

            'tracking_link' =>
                $this->tracking_link,

            /*
            |--------------------------------------------------------------------------
            | Courier Information
            |--------------------------------------------------------------------------
            */

            'courier_name' =>
                $this->courier_name,

            'courier_code' =>
                $this->courier_code,

            'service_name' =>
                $this->service_name,

            'service_code' =>
                $this->service_code,

            'courier_display' =>
                $this->courierDisplay(),

            /*
            |--------------------------------------------------------------------------
            | Shipping Cost
            |--------------------------------------------------------------------------
            */

            'shipping_cost' =>
                (float) $this->shipping_cost,

            'shipping_cost_formatted' =>
                'Rp ' .
                number_format(
                    $this->shipping_cost,
                    0,
                    ',',
                    '.'
                ),

            'insurance_cost' =>
                (float) $this->insurance_cost,

            'insurance_cost_formatted' =>
                'Rp ' .
                number_format(
                    $this->insurance_cost,
                    0,
                    ',',
                    '.'
                ),

            'is_insured' =>
                (bool) $this->is_insured,

            /*
            |--------------------------------------------------------------------------
            | Package Information
            |--------------------------------------------------------------------------
            */

            'weight' =>
                (float) $this->weight,

            'item_count' =>
                (int) $this->item_count,

            /*
            |--------------------------------------------------------------------------
            | Recipient Information
            |--------------------------------------------------------------------------
            */

            'recipient_name' =>
                $this->recipient_name,

            'recipient_phone' =>
                $this->recipient_phone,

            'recipient_address' =>
                $this->recipient_address,

            'recipient_city' =>
                $this->recipient_city,

            'recipient_province' =>
                $this->recipient_province,

            'recipient_postal_code' =>
                $this->recipient_postal_code,

            /*
            |--------------------------------------------------------------------------
            | Delivery Status
            |--------------------------------------------------------------------------
            */

            'status' =>
                $this->status,

            'is_pending' =>
                $this->isPending(),

            'is_delivered' =>
                $this->isDelivered(),

            'is_in_transit' =>
                $this->isInTransit(),

            'is_returned' =>
                $this->isReturned(),

            'is_cancelled' =>
                $this->isCancelled(),

            'status_label' =>
                match ($this->status) {

                    Shipment::STATUS_PENDING
                        => 'Pending',

                    Shipment::STATUS_READY_TO_SHIP
                        => 'Ready To Ship',

                    Shipment::STATUS_PICKED_UP
                        => 'Picked Up',

                    Shipment::STATUS_IN_TRANSIT
                        => 'In Transit',

                    Shipment::STATUS_OUT_FOR_DELIVERY
                        => 'Out For Delivery',

                    Shipment::STATUS_DELIVERED
                        => 'Delivered',

                    Shipment::STATUS_FAILED_DELIVERY
                        => 'Failed Delivery',

                    Shipment::STATUS_RETURNED
                        => 'Returned',

                    Shipment::STATUS_CANCELLED
                        => 'Cancelled',

                    default
                        => ucfirst(
                            str_replace(
                                '_',
                                ' ',
                                (string) $this->status
                            )
                        ),
                },

            'status_color' =>
                match ($this->status) {

                    Shipment::STATUS_DELIVERED
                        => 'green',

                    Shipment::STATUS_PENDING,
                    Shipment::STATUS_READY_TO_SHIP
                        => 'yellow',

                    Shipment::STATUS_PICKED_UP,
                    Shipment::STATUS_IN_TRANSIT,
                    Shipment::STATUS_OUT_FOR_DELIVERY
                        => 'blue',

                    Shipment::STATUS_FAILED_DELIVERY,
                    Shipment::STATUS_RETURNED,
                    Shipment::STATUS_CANCELLED
                        => 'red',

                    default
                        => 'gray',
                },

            /*
            |--------------------------------------------------------------------------
            | Delivery Information
            |--------------------------------------------------------------------------
            */

            'delivery_attempts' =>
                (int) $this->delivery_attempts,

            'delivery_duration_hours' =>
                $this->delivery_duration_hours !== null
                    ? (int) $this->delivery_duration_hours
                    : null,

            'received_by' =>
                $this->received_by,

            'signed_proof' =>
                $this->signed_proof,

            'has_proof_of_delivery' =>
                $this->hasProofOfDelivery(),

            'failed_reason' =>
                $this->failed_reason,

            'return_reason' =>
                $this->return_reason,

            /*
            |--------------------------------------------------------------------------
            | Additional Information
            |--------------------------------------------------------------------------
            */

            'notes' =>
                $this->notes,

            'metadata' =>
                $this->metadata,

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            'order' =>
                $this->whenLoaded(
                    'order',
                    fn () => [

                        'id' =>
                            $this->order->id,

                        'order_number' =>
                            $this->order->order_number,
                    ]
                ),

            'shipping_method' =>
                $this->whenLoaded(
                    'shippingMethod',
                    fn () => [

                        'id' =>
                            $this->shippingMethod->id,

                        'service_name' =>
                            $this->shippingMethod->service_name,

                        'service_code' =>
                            $this->shippingMethod->service_code,
                    ]
                ),

            /*
            |--------------------------------------------------------------------------
            | Shipment Timeline
            |--------------------------------------------------------------------------
            */

            'pickup_at' =>
                $this->pickup_at?->toISOString(),

            'pickup_at_human' =>
                $this->pickup_at?->diffForHumans(),

            'shipped_at' =>
                $this->shipped_at?->toISOString(),

            'shipped_at_human' =>
                $this->shipped_at?->diffForHumans(),

            'estimated_delivery_at' =>
                $this->estimated_delivery_at?->toISOString(),

            'estimated_delivery_at_human' =>
                $this->estimated_delivery_at?->diffForHumans(),

            'delivered_at' =>
                $this->delivered_at?->toISOString(),

            'delivered_at_human' =>
                $this->delivered_at?->diffForHumans(),

            'last_tracking_sync_at' =>
                $this->last_tracking_sync_at?->toISOString(),

            'last_tracking_sync_human' =>
                $this->last_tracking_sync_at?->diffForHumans(),

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