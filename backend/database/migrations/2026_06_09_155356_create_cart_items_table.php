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
            'cart_items',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('cart_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('product_sku_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Product Snapshot
                |--------------------------------------------------------------------------
                |
                | Snapshot diperlukan agar UI cart
                | tetap stabil walaupun produk berubah.
                |
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
                )
                    ->default(1);

                $table->decimal(
                    'subtotal',
                    15,
                    2
                );

                /*
                |--------------------------------------------------------------------------
                | Cart Validation
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_available'
                )
                    ->default(true);

                $table->boolean(
                    'is_selected'
                )
                    ->default(true);

                /*
                |--------------------------------------------------------------------------
                | Future:
                | Stock validation
                | Promotion validation
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
                | Soft Delete
                |--------------------------------------------------------------------------
                */

                $table->softDeletes();

                /*
                |--------------------------------------------------------------------------
                | Constraints
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'cart_id',
                    'product_sku_id',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'cart_id'
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
                    'is_selected'
                );

                $table->index(
                    'added_at'
                );

                $table->index([
                    'cart_id',
                    'is_selected',
                ]);

                $table->index([
                    'cart_id',
                    'added_at',
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
            'cart_items'
        );
    }
};