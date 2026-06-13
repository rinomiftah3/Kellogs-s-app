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
            'order_status_logs',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('order_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Status Transition
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'from_status'
                )->nullable();

                $table->string(
                    'to_status'
                );

                /*
                |--------------------------------------------------------------------------
                | Tracking Information
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'changed_at'
                );

                $table->unsignedInteger(
                    'duration_seconds'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Source
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'source',
                    [
                        'system',
                        'customer',
                        'admin',
                        'payment_gateway',
                        'courier',
                    ]
                )->default('system');

                /*
                |--------------------------------------------------------------------------
                | Reason
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'reason'
                )->nullable();

                $table->text(
                    'notes'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Metadata
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

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'order_id'
                );

                $table->index(
                    'user_id'
                );

                $table->index(
                    'to_status'
                );

                $table->index(
                    'source'
                );

                $table->index(
                    'changed_at'
                );

                $table->index([
                    'order_id',
                    'changed_at',
                ]);

                $table->index([
                    'order_id',
                    'to_status',
                ]);

                $table->index([
                    'to_status',
                    'changed_at',
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
            'order_status_logs'
        );
    }
};