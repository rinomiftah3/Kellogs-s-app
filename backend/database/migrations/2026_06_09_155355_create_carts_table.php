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
            'carts',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('customer_profile_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Cart Summary
                |--------------------------------------------------------------------------
                */

                $table->integer(
                    'total_items'
                )
                    ->default(0);

                $table->decimal(
                    'subtotal',
                    15,
                    2
                )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Cart Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_active'
                )
                    ->default(true);

                /*
                |--------------------------------------------------------------------------
                | Activity
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'last_activity_at'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Expiration
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'expires_at'
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
                    'customer_profile_id'
                );

                $table->index(
                    'is_active'
                );

                $table->index(
                    'last_activity_at'
                );

                $table->index(
                    'expires_at'
                );

                $table->index([
                    'is_active',
                    'expires_at',
                ]);

                $table->index([
                    'customer_profile_id',
                    'is_active',
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
            'carts'
        );
    }
};