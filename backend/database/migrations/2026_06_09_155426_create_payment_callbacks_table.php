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
            'payment_callbacks',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('payment_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Gateway Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'gateway'
                );

                $table->string(
                    'event_type'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Gateway References
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'gateway_transaction_id'
                )->nullable();

                $table->string(
                    'gateway_order_id'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Callback Status
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'status',
                    [
                        'received',
                        'processed',
                        'failed',
                        'ignored',
                    ]
                )->default('received');

                /*
                |--------------------------------------------------------------------------
                | HTTP Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'http_method',
                    10
                )->nullable();

                $table->integer(
                    'http_status'
                )->nullable();

                $table->string(
                    'ip_address',
                    45
                )->nullable();

                $table->text(
                    'user_agent'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Security
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'signature'
                )->nullable();

                $table->boolean(
                    'signature_valid'
                )->default(false);

                /*
                |--------------------------------------------------------------------------
                | Payload
                |--------------------------------------------------------------------------
                */

                $table->json(
                    'headers'
                )->nullable();

                $table->json(
                    'payload'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Processing Result
                |--------------------------------------------------------------------------
                */

                $table->json(
                    'processing_result'
                )->nullable();

                $table->text(
                    'error_message'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Callback Time
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'received_at'
                )->useCurrent();

                $table->timestamp(
                    'processed_at'
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
                    'payment_id'
                );

                $table->index(
                    'gateway'
                );

                $table->index(
                    'event_type'
                );

                $table->index(
                    'status'
                );

                $table->index(
                    'gateway_transaction_id'
                );

                $table->index(
                    'gateway_order_id'
                );

                $table->index(
                    'received_at'
                );

                $table->index(
                    'processed_at'
                );

                $table->index(
                    'signature_valid'
                );

                $table->index([
                    'gateway',
                    'status',
                ]);

                $table->index([
                    'payment_id',
                    'received_at',
                ]);

                $table->index([
                    'gateway',
                    'event_type',
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
            'payment_callbacks'
        );
    }
};