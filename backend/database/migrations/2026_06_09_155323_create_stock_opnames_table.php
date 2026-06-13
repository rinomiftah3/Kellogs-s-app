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
            'stock_opnames',
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
                | Opname Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'opname_number',
                    100
                )->unique();

                $table->date(
                    'opname_date'
                );

                /*
                |--------------------------------------------------------------------------
                | Stock Comparison
                |--------------------------------------------------------------------------
                */

                $table->integer(
                    'system_stock'
                );

                $table->integer(
                    'physical_stock'
                );

                $table->integer(
                    'difference'
                );

                /*
                |--------------------------------------------------------------------------
                | Difference Analysis
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'status',
                    30
                );

                /*
                |--------------------------------------------------------------------------
                | match
                | shortage
                | excess
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'notes'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Audit Information
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'counted_by'
                )
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId(
                    'verified_by'
                )
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamp(
                    'verified_at'
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
                | Soft Delete
                |--------------------------------------------------------------------------
                */

                $table->softDeletes();

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'product_sku_id'
                );

                $table->index(
                    'opname_number'
                );

                $table->index(
                    'opname_date'
                );

                $table->index(
                    'status'
                );

                $table->index(
                    'counted_by'
                );

                $table->index(
                    'verified_by'
                );

                $table->index([
                    'product_sku_id',
                    'opname_date',
                ]);

                $table->index([
                    'status',
                    'opname_date',
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
            'stock_opnames'
        );
    }
};