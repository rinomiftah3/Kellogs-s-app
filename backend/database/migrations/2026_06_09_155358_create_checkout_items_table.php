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
            'checkout_items',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('checkout_session_id')
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

                $table->string(
                    'product_name'
                );

                $table->string(
                    'sku',
                    100
                );

                $table->string(
                    'thumbnail',
                    2048
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Pricing Snapshot
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'price',
                    15,
                    2
                );

                $table->integer(
                    'quantity'
                );

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

                $table->decimal(
                    'discount_amount',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'final_price',
                    15,
                    2
                );

                /*
                |--------------------------------------------------------------------------
                | Validation Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_available'
                )
                    ->default(true);

                $table->boolean(
                    'is_valid_price'
                )
                    ->default(true);

                $table->boolean(
                    'is_valid_stock'
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
                | Activity
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'added_at'
                )
                    ->useCurrent();

                /*
                |--------------------------------------------------------------------------
                | Timestamps
                |--------------------------------------------------------------------------
                */

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Soft Deletes
                |--------------------------------------------------------------------------
                */

                $table->softDeletes();

                /*
                |--------------------------------------------------------------------------
                | Constraints
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'checkout_session_id',
                    'product_sku_id',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'checkout_session_id'
                );

                $table->index(
                    'product_sku_id'
                );

                $table->index(
                    'sku'
                );

                $table->index(
                    'is_available'
                );

                $table->index(
                    'is_valid_price'
                );

                $table->index(
                    'is_valid_stock'
                );

                $table->index(
                    'added_at'
                );

                $table->index([
                    'checkout_session_id',
                    'added_at',
                ]);

                $table->index([
                    'checkout_session_id',
                    'is_available',
                ]);

                $table->index([
                    'product_sku_id',
                    'added_at',
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
            'checkout_items'
        );
    }
};