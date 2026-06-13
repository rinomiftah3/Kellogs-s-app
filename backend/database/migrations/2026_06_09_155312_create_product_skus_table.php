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
            'product_skus',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('product_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | SKU Identity
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'sku',
                    100
                )->unique();

                $table->string(
                    'barcode',
                    100
                )->nullable()->unique();

                /*
                |--------------------------------------------------------------------------
                | Pricing
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'price',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'compare_at_price',
                    15,
                    2
                )->nullable();

                $table->decimal(
                    'cost_price',
                    15,
                    2
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Physical Information
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'weight',
                    10,
                    2
                )->default(0);

                $table->decimal(
                    'length',
                    10,
                    2
                )->nullable();

                $table->decimal(
                    'width',
                    10,
                    2
                )->nullable();

                $table->decimal(
                    'height',
                    10,
                    2
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Sales Configuration
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'minimum_order_quantity'
                )->default(1);

                $table->unsignedInteger(
                    'maximum_order_quantity'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Display
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_default'
                )->default(false);

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'status',
                    30
                )->default('draft');

                /*
                |--------------------------------------------------------------------------
                | draft
                | active
                | inactive
                | archived
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
                    'product_id'
                );

                $table->index([
                    'product_id',
                    'is_active',
                ]);

                $table->index([
                    'is_default',
                    'is_active',
                ]);

                $table->index([
                    'status',
                    'is_active',
                ]);

                $table->index([
                    'price',
                    'is_active',
                ]);

                $table->index([
                    'created_at',
                    'is_active',
                ]);

                $table->index(
                    'published_at'
                );
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'product_skus'
        );
    }
};