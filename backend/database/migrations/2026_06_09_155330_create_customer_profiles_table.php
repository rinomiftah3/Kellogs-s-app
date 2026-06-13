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
            'customer_profiles',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | User Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('user_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Customer Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'customer_code',
                    50
                )
                    ->unique();

                $table->string(
                    'full_name'
                );

                $table->string(
                    'phone',
                    30
                )
                    ->nullable();

                $table->enum(
                    'gender',
                    [
                        'male',
                        'female',
                    ]
                )
                    ->nullable();

                $table->date(
                    'birth_date'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Profile
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'avatar',
                    2048
                )
                    ->nullable();

                $table->text(
                    'bio'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Membership
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'membership_level',
                    50
                )
                    ->default('regular');

                /*
                |--------------------------------------------------------------------------
                | regular
                | silver
                | gold
                | platinum
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger('total_points')
                    ->default(0);

                $table->decimal(
                    'total_spent',
                    15,
                    2
                )
                    ->default(0);

                $table->unsignedInteger('total_orders')
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Customer Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_active'
                )
                    ->default(true);

                $table->timestamp(
                    'last_order_at'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Marketing
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'email_subscribed'
                )
                    ->default(true);

                $table->boolean(
                    'sms_subscribed'
                )
                    ->default(false);

                $table->boolean(
                    'push_subscribed'
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
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'customer_code'
                );

                $table->index(
                    'phone'
                );

                $table->index(
                    'membership_level'
                );

                $table->index(
                    'is_active'
                );

                $table->index(
                    'last_order_at'
                );

                $table->index([
                    'membership_level',
                    'is_active',
                ]);

                $table->index([
                    'is_active',
                    'created_at',
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
            'customer_profiles'
        );
    }
};