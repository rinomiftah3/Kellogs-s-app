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
            'inventories',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('product_sku_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Stock Information
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'current_stock'
                )->default(0);

                $table->unsignedInteger(
                    'reserved_stock'
                )->default(0);

                $table->unsignedInteger(
                    'available_stock'
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Stock Control
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'minimum_stock'
                )->default(0);

                $table->unsignedInteger(
                    'maximum_stock'
                )->nullable();

                $table->unsignedInteger(
                    'reorder_point'
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Inventory Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'allow_backorder'
                )->default(false);

                $table->boolean(
                    'is_active'
                )->default(true);

                /*
                |--------------------------------------------------------------------------
                | Timestamps
                |--------------------------------------------------------------------------
                */

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Unique Constraint
                |--------------------------------------------------------------------------
                |
                | Satu SKU hanya boleh memiliki
                | satu inventory record.
                |
                */

                $table->unique(
                    'product_sku_id'
                );

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'product_sku_id'
                );

                $table->index([
                    'available_stock',
                    'current_stock',
                ]);

                $table->index([
                    'current_stock',
                    'reserved_stock',
                ]);

                $table->index([
                    'minimum_stock',
                    'reorder_point',
                ]);

                $table->index(
                    'is_active'
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
            'inventories'
        );
    }
};