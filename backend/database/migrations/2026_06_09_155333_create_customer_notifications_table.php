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
            'customer_notifications',
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
                | Notification Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'type',
                    50
                );

                /*
                |--------------------------------------------------------------------------
                | order
                | payment
                | shipment
                | promotion
                | loyalty
                | system
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'title'
                );

                $table->text(
                    'message'
                );

                /*
                |--------------------------------------------------------------------------
                | Notification Channel
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'send_in_app'
                )
                    ->default(true);

                $table->boolean(
                    'send_push'
                )
                    ->default(false);

                $table->boolean(
                    'send_email'
                )
                    ->default(false);

                $table->boolean(
                    'send_sms'
                )
                    ->default(false);

                /*
                |--------------------------------------------------------------------------
                | Action
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'action_url',
                    2048
                )
                    ->nullable();

                $table->string(
                    'action_label'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Metadata
                |--------------------------------------------------------------------------
                */

                $table->json(
                    'data'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Read Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_read'
                )
                    ->default(false);

                $table->timestamp(
                    'read_at'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Delivery Status
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'sent_at'
                )
                    ->nullable();

                $table->timestamp(
                    'scheduled_at'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'status',
                    30
                )
                    ->default('pending');

                /*
                |--------------------------------------------------------------------------
                | pending
                | sent
                | failed
                | cancelled
                |--------------------------------------------------------------------------
                */

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
                    'type'
                );

                $table->index(
                    'is_read'
                );

                $table->index(
                    'status'
                );

                $table->index(
                    'read_at'
                );

                $table->index(
                    'sent_at'
                );

                $table->index(
                    'scheduled_at'
                );

                $table->index([
                    'customer_profile_id',
                    'is_read',
                ]);

                $table->index([
                    'customer_profile_id',
                    'type',
                ]);

                $table->index([
                    'status',
                    'scheduled_at',
                ]);

                $table->index([
                    'customer_profile_id',
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
            'customer_notifications'
        );
    }
};