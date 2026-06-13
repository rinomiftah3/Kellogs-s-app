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
            'stock_movements',
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
                | Movement Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'type',
                    50
                );

                /*
                |--------------------------------------------------------------------------
                | Types:
                |
                | stock_in
                | stock_out
                | sale
                | return
                | adjustment
                | transfer
                | damaged
                | expired
                |--------------------------------------------------------------------------
                */

                $table->integer(
                    'quantity'
                );

                /*
                |--------------------------------------------------------------------------
                | Stock Snapshot
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'stock_before'
                );

                $table->unsignedInteger(
                    'stock_after'
                );

                /*
                |--------------------------------------------------------------------------
                | Reference
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'reference_type',
                    100
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Examples:
                |
                | order
                | purchase
                | adjustment
                | return
                | transfer
                |--------------------------------------------------------------------------
                */

                $table->unsignedBigInteger(
                    'reference_id'
                )
                    ->nullable();

                $table->string(
                    'reference_number',
                    100
                )
                    ->nullable();

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
                | User Tracking
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'created_by'
                )
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Event Time
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'movement_date'
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
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'product_sku_id'
                );

                $table->index(
                    'type'
                );

                $table->index([
                    'product_sku_id',
                    'type',
                ]);

                $table->index([
                    'product_sku_id',
                    'movement_date',
                ]);

                $table->index([
                    'reference_type',
                    'reference_id',
                ]);

                $table->index(
                    'movement_date'
                );

                $table->index(
                    'created_by'
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
            'stock_movements'
        );
    }
};