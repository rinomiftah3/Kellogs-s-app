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
            'shipment_trackings',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('shipment_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Tracking Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'tracking_code',
                    100
                )->nullable();

                $table->string(
                    'tracking_event_code',
                    100
                )->nullable();

                $table->string(
                    'status',
                    100
                );

                $table->string(
                    'location'
                )->nullable();

                $table->string(
                    'city'
                )->nullable();

                $table->string(
                    'province'
                )->nullable();

                $table->text(
                    'description'
                );

                /*
                |--------------------------------------------------------------------------
                | Courier Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'courier_status',
                    100
                )->nullable();

                $table->string(
                    'courier_code',
                    50
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | GPS Tracking
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'latitude',
                    10,
                    7
                )->nullable();

                $table->decimal(
                    'longitude',
                    10,
                    7
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Event Sequence
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'event_sequence'
                )->default(1);

                /*
                |--------------------------------------------------------------------------
                | Tracking Time
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'tracked_at'
                );

                $table->timestamp(
                    'processed_at'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Flags
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_latest'
                )->default(false);

                $table->boolean(
                    'is_customer_visible'
                )->default(true);

                /*
                |--------------------------------------------------------------------------
                | Source
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'source',
                    [
                        'system',
                        'courier_api',
                        'admin',
                    ]
                )->default('courier_api');

                /*
                |--------------------------------------------------------------------------
                | Additional Data
                |--------------------------------------------------------------------------
                */

                $table->json(
                    'payload'
                )->nullable();

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
                | Constraints
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'shipment_id',
                    'event_sequence',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'shipment_id'
                );

                $table->index(
                    'tracking_code'
                );

                $table->index(
                    'tracking_event_code'
                );

                $table->index(
                    'status'
                );

                $table->index(
                    'tracked_at'
                );

                $table->index(
                    'processed_at'
                );

                $table->index(
                    'is_latest'
                );

                $table->index(
                    'is_customer_visible'
                );

                $table->index(
                    'source'
                );

                $table->index(
                    'courier_code'
                );

                $table->index([
                    'shipment_id',
                    'tracked_at',
                ]);

                $table->index([
                    'shipment_id',
                    'is_latest',
                ]);

                $table->index([
                    'status',
                    'tracked_at',
                ]);

                $table->index([
                    'shipment_id',
                    'event_sequence',
                ]);

                $table->index([
                    'courier_code',
                    'tracked_at',
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
            'shipment_trackings'
        );
    }
};