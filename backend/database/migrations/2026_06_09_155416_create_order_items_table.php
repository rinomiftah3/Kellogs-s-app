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
            'order_items',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('order_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('product_sku_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Product Snapshot
                |--------------------------------------------------------------------------
                */

                $table->unsignedBigInteger(
                    'product_id'
                );

                $table->unsignedBigInteger(
                    'category_id'
                )->nullable();

                $table->string(
                    'product_name'
                );

                $table->string(
                    'product_slug'
                )->nullable();

                $table->string(
                    'sku',
                    100
                );

                $table->string(
                    'barcode',
                    100
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Variant Snapshot
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'variant_name'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Product Information Snapshot
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'thumbnail',
                    2048
                )->nullable();

                $table->integer(
                    'weight'
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Pricing Snapshot
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'unit_price',
                    15,
                    2
                );

                $table->decimal(
                    'discount_amount',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'final_price',
                    15,
                    2
                );

                /*
                |--------------------------------------------------------------------------
                | Quantity
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'quantity'
                );

                /*
                |--------------------------------------------------------------------------
                | Totals
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'subtotal',
                    15,
                    2
                );

                /*
                |--------------------------------------------------------------------------
                | Promotion Snapshot
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'promotion_name'
                )->nullable();

                $table->string(
                    'promotion_code'
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

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'order_id'
                );

                $table->index(
                    'product_sku_id'
                );

                $table->index(
                    'product_id'
                );

                $table->index(
                    'category_id'
                );

                $table->index(
                    'sku'
                );

                $table->index([
                    'order_id',
                    'product_sku_id',
                ]);

                $table->index([
                    'product_sku_id',
                    'created_at',
                ]);

                $table->index([
                    'product_id',
                    'created_at',
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
            'order_items'
        );
    }
};