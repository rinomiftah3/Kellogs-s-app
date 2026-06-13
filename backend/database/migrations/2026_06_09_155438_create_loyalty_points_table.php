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
            'loyalty_points',
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
                | Point Balance
                |--------------------------------------------------------------------------
                */

                $table->unsignedBigInteger(
                    'current_points'
                )->default(0);

                $table->unsignedBigInteger(
                    'available_points'
                )->default(0);

                $table->unsignedBigInteger(
                    'pending_points'
                )->default(0);

                $table->unsignedBigInteger(
                    'earned_points'
                )->default(0);

                $table->unsignedBigInteger(
                    'redeemed_points'
                )->default(0);

                $table->unsignedBigInteger(
                    'expired_points'
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Lifetime Statistics
                |--------------------------------------------------------------------------
                */

                $table->unsignedBigInteger(
                    'lifetime_points'
                )->default(0);

                $table->unsignedBigInteger(
                    'lifetime_orders'
                )->default(0);

                $table->decimal(
                    'lifetime_spending',
                    15,
                    2
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Membership Tier
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'tier',
                    [
                        'bronze',
                        'silver',
                        'gold',
                        'platinum',
                    ]
                )->default('bronze');

                /*
                |--------------------------------------------------------------------------
                | Tier Information
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'tier_upgraded_at'
                )->nullable();

                $table->timestamp(
                    'tier_expires_at'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Activity Information
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'last_earned_at'
                )->nullable();

                $table->timestamp(
                    'last_redeemed_at'
                )->nullable();

                $table->timestamp(
                    'last_activity_at'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Point Expiration
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'last_expired_at'
                )->nullable();

                $table->unsignedInteger(
                    'total_expiration_events'
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_active'
                )->default(true);

                /*
                |--------------------------------------------------------------------------
                | Publish
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'published_at'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Additional Data
                |--------------------------------------------------------------------------
                */

                $table->json(
                    'metadata'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Timestamps
                |--------------------------------------------------------------------------
                */

                $table->timestamps();

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
                    'tier'
                );

                $table->index(
                    'current_points'
                );

                $table->index(
                    'available_points'
                );

                $table->index(
                    'is_active'
                );

                $table->index(
                    'published_at'
                );

                $table->index(
                    'last_activity_at'
                );

                $table->index([
                    'tier',
                    'current_points',
                ]);

                $table->index([
                    'is_active',
                    'tier',
                ]);

                $table->index([
                    'lifetime_spending',
                    'tier',
                ]);

                $table->index([
                    'is_active',
                    'published_at',
                ]);

                $table->index([
                    'tier',
                    'available_points',
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
            'loyalty_points'
        );
    }
};