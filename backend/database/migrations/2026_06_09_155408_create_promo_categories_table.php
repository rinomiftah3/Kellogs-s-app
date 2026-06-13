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
            'promo_categories',
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

                $table->foreignId('category_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Override Configuration
                |--------------------------------------------------------------------------
                |
                | Optional override dari promotion utama.
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
                    'category_id',
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
                    'category_id'
                );

                $table->index(
                    'is_active'
                );

                $table->index([
                    'promotion_id',
                    'is_active',
                ]);

                $table->index([
                    'category_id',
                    'is_active',
                ]);

                $table->index([
                    'promotion_id',
                    'category_id',
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
            'promo_categories'
        );
    }
};