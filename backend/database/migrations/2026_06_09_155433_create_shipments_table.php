<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'shipments',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('order_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->foreignId('shipping_method_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Shipment Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'shipment_number',
                    100
                )->unique();

                $table->string(
                    'tracking_number',
                    100
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Courier Snapshot
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'courier_name'
                );

                $table->string(
                    'courier_code',
                    50
                );

                $table->string(
                    'service_name'
                );

                $table->string(
                    'service_code',
                    50
                );

                $table->string(
                    'tracking_url',
                    2048
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Shipping Cost
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'shipping_cost',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'insurance_cost',
                    15,
                    2
                )->default(0);

                $table->boolean(
                    'is_insured'
                )->default(false);

                /*
                |--------------------------------------------------------------------------
                | Package Information
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'weight',
                    10,
                    2
                )->default(0);

                $table->unsignedInteger(
                    'item_count'
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Shipment Status
                |--------------------------------------------------------------------------
                |
                | pending
                | ready_to_ship
                | picked_up
                | in_transit
                | out_for_delivery
                | delivered
                | failed_delivery
                | returned
                | cancelled
                |
                */

                $table->enum(
                    'status',
                    [
                        'pending',
                        'ready_to_ship',
                        'picked_up',
                        'in_transit',
                        'out_for_delivery',
                        'delivered',
                        'failed_delivery',
                        'returned',
                        'cancelled',
                    ]
                )->default('pending');

                /*
                |--------------------------------------------------------------------------
                | Receiver Snapshot
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'recipient_name'
                );

                $table->string(
                    'recipient_phone',
                    30
                );

                $table->text(
                    'recipient_address'
                );

                $table->string(
                    'recipient_city'
                );

                $table->string(
                    'recipient_province'
                );

                $table->string(
                    'recipient_postal_code',
                    20
                );

                /*
                |--------------------------------------------------------------------------
                | Timeline
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'pickup_at'
                )->nullable();

                $table->timestamp(
                    'shipped_at'
                )->nullable();

                $table->timestamp(
                    'estimated_delivery_at'
                )->nullable();

                $table->timestamp(
                    'delivered_at'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Delivery Analytics
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'delivery_attempts'
                )->default(0);

                $table->unsignedInteger(
                    'delivery_duration_hours'
                )->nullable();

                $table->timestamp(
                    'last_tracking_sync_at'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Delivery Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'received_by'
                )->nullable();

                $table->string(
                    'signed_proof',
                    2048
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Failure / Return
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'failed_reason'
                )->nullable();

                $table->text(
                    'return_reason'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'notes'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Metadata
                |--------------------------------------------------------------------------
                */

                $table->json(
                    'metadata'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Timestamps
                |--------------------------------------------------------------------------
                */

                $table->timestamps();

                $table->softDeletes();

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'order_id'
                );

                $table->index(
                    'shipping_method_id'
                );

                $table->index(
                    'shipment_number'
                );

                $table->index(
                    'tracking_number'
                );

                $table->index(
                    'status'
                );

                $table->index(
                    'pickup_at'
                );

                $table->index(
                    'shipped_at'
                );

                $table->index(
                    'estimated_delivery_at'
                );

                $table->index(
                    'delivered_at'
                );

                $table->index(
                    'last_tracking_sync_at'
                );

                $table->index([
                    'order_id',
                    'status',
                ]);

                $table->index([
                    'order_id',
                    'tracking_number',
                ]);

                $table->index([
                    'status',
                    'shipped_at',
                ]);

                $table->index([
                    'status',
                    'estimated_delivery_at',
                ]);

                $table->index([
                    'status',
                    'delivered_at',
                ]);

                $table->index([
                    'shipping_method_id',
                    'status',
                ]);

                $table->index([
                    'tracking_number',
                    'status',
                ]);

                $table->index([
                    'status',
                    'last_tracking_sync_at',
                ]);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'shipments'
        );
    }
};