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
            'customer_devices',
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

                /*
                |--------------------------------------------------------------------------
                | Device Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'device_id',
                    255
                )->unique();

                $table->string(
                    'device_name'
                )->nullable();

                $table->string(
                    'device_type',
                    30
                );

                /*
                |--------------------------------------------------------------------------
                | android
                | ios
                | web
                | desktop
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'platform'
                )->nullable();

                $table->string(
                    'platform_version'
                )->nullable();

                $table->string(
                    'app_version'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Push Notification
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'fcm_token'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Security
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'ip_address',
                    45
                )->nullable();

                $table->text(
                    'user_agent'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Device Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_active'
                )->default(true);

                $table->boolean(
                    'is_trusted'
                )->default(false);

                /*
                |--------------------------------------------------------------------------
                | Activity
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'last_login_at'
                )->nullable();

                $table->timestamp(
                    'last_active_at'
                )->nullable();

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
                    'device_id'
                );

                $table->index(
                    'device_type'
                );

                $table->index(
                    'is_active'
                );

                $table->index(
                    'is_trusted'
                );

                $table->index(
                    'last_login_at'
                );

                $table->index(
                    'last_active_at'
                );

                $table->index([
                    'customer_profile_id',
                    'is_active',
                ]);

                $table->index([
                    'customer_profile_id',
                    'last_active_at',
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
            'customer_devices'
        );
    }
};