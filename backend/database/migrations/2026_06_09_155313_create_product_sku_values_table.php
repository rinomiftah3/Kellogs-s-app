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
            'product_sku_values',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('product_sku_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('product_option_value_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Timestamps
                |--------------------------------------------------------------------------
                |
                | Berguna untuk audit dan debugging.
                |
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
                    'product_option_value_id'
                );

                /*
                |--------------------------------------------------------------------------
                | Composite Unique
                |--------------------------------------------------------------------------
                |
                | Mencegah:
                |
                | CF-ORI-250
                | ↳ Original
                |
                | tersimpan dua kali.
                |
                */

                $table->unique([
                    'product_sku_id',
                    'product_option_value_id',
                ], 'sku_value_unique');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'product_sku_values'
        );
    }
};