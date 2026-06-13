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
            'shipping_methods',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('courier_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Service Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'service_code',
                    50
                );

                $table->string(
                    'service_name'
                );

                $table->text(
                    'description'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Delivery Estimation
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'estimated_min_days'
                )->default(1);

                $table->unsignedInteger(
                    'estimated_max_days'
                )->default(1);

                /*
                |--------------------------------------------------------------------------
                | Shipping Features
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'supports_tracking'
                )->default(true);

                $table->boolean(
                    'supports_cod'
                )->default(false);

                $table->boolean(
                    'supports_insurance'
                )->default(false);

                /*
                |--------------------------------------------------------------------------
                | Pricing
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'base_cost',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'cost_per_kg',
                    15,
                    2
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Weight Rules
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'minimum_weight'
                )->default(0);

                /*
                | gram
                */

                $table->unsignedInteger(
                    'maximum_weight'
                )->nullable();

                /*
                | gram
                */

                /*
                |--------------------------------------------------------------------------
                | Free Shipping
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'free_shipping_threshold',
                    15,
                    2
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | SLA
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'sla_hours'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Display
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'sort_order'
                )->default(0);

                $table->boolean(
                    'is_featured'
                )->default(false);

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_active'
                )->default(true);

                /*
                |--------------------------------------------------------------------------
                | Publish
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'published_at'
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
                | Constraints
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'courier_id',
                    'service_code',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'courier_id'
                );

                $table->index(
                    'service_code'
                );

                $table->index(
                    'service_name'
                );

                $table->index(
                    'is_active'
                );

                $table->index(
                    'is_featured'
                );

                $table->index(
                    'sort_order'
                );

                $table->index(
                    'published_at'
                );

                $table->index([
                    'courier_id',
                    'is_active',
                ]);

                $table->index([
                    'is_active',
                    'service_name',
                ]);

                $table->index([
                    'is_active',
                    'sort_order',
                ]);

                $table->index([
                    'is_featured',
                    'is_active',
                ]);

                $table->index([
                    'estimated_min_days',
                    'estimated_max_days',
                ]);

                $table->index([
                    'is_active',
                    'published_at',
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
            'shipping_methods'
        );
    }
};