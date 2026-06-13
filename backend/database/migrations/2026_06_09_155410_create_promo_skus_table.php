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
            'promo_skus',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('promotion_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('product_sku_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | SKU Specific Override
                |--------------------------------------------------------------------------
                |
                | Jika null maka mengikuti promotion utama.
                |
                */

                $table->decimal(
                    'discount_value',
                    15,
                    2
                )
                    ->nullable();

                $table->decimal(
                    'maximum_discount',
                    15,
                    2
                )
                    ->nullable();

                $table->decimal(
                    'minimum_purchase',
                    15,
                    2
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Flash Sale Override
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'promo_price',
                    15,
                    2
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Purchase Limitation
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'max_quantity_per_order'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Usage Tracking
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'usage_limit'
                )
                    ->nullable();

                $table->unsignedInteger(
                    'used_count'
                )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_active'
                )
                    ->default(true);

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'notes'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Timestamps
                |--------------------------------------------------------------------------
                */

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Constraints
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'promotion_id',
                    'product_sku_id',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'promotion_id'
                );

                $table->index(
                    'product_sku_id'
                );

                $table->index(
                    'is_active'
                );

                $table->index(
                    'promo_price'
                );

                $table->index([
                    'promotion_id',
                    'is_active',
                ]);

                $table->index([
                    'product_sku_id',
                    'is_active',
                ]);

                $table->index([
                    'promotion_id',
                    'product_sku_id',
                ]);

                $table->index([
                    'is_active',
                    'promo_price',
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
            'promo_skus'
        );
    }
};