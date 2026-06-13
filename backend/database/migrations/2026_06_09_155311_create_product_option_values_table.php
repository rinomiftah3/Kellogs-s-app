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
            'product_option_values',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('product_option_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Value Information
                |--------------------------------------------------------------------------
                |
                | Examples:
                | 60g
                | 250g
                | 500g
                |
                | Original
                | Chocolate
                |
                */

                $table->string(
                    'value',
                    100
                );

                /*
                |--------------------------------------------------------------------------
                | Optional Code
                |--------------------------------------------------------------------------
                |
                | Examples:
                |
                | 250G
                | 500G
                | ORI
                | CHO
                |
                */

                $table->string(
                    'code',
                    50
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Display
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'sort_order'
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
                    'product_option_id'
                );

                $table->index([
                    'product_option_id',
                    'is_active',
                ]);

                $table->index([
                    'product_option_id',
                    'sort_order',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Unique Constraints
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'product_option_id',
                    'value',
                ]);

                $table->unique([
                    'product_option_id',
                    'code',
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
            'product_option_values'
        );
    }
};