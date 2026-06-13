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
            'product_options',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('product_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Option Information
                |--------------------------------------------------------------------------
                |
                | Examples:
                | Size
                | Flavor
                | Package Type
                |
                */

                $table->string(
                    'name',
                    100
                );

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
                    'is_required'
                )
                    ->default(true);

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
                    'product_id'
                );

                $table->index([
                    'product_id',
                    'is_active',
                ]);

                $table->index([
                    'product_id',
                    'sort_order',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Unique Constraint
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'product_id',
                    'name',
                ]);

                $table->unique([
                    'product_id',
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
            'product_options'
        );
    }
};