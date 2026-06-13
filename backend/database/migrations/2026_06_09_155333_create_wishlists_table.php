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
            'wishlists',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('customer_profile_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('product_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Wishlist Information
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'added_at'
                )
                    ->useCurrent();

                /*
                |--------------------------------------------------------------------------
                | Notification Preference
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'notify_price_drop'
                )
                    ->default(true);

                $table->boolean(
                    'notify_back_in_stock'
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
                    'customer_profile_id',
                    'product_id',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'customer_profile_id'
                );

                $table->index(
                    'product_id'
                );

                $table->index(
                    'added_at'
                );

                $table->index([
                    'customer_profile_id',
                    'added_at',
                ]);

                $table->index([
                    'product_id',
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
            'wishlists'
        );
    }
};