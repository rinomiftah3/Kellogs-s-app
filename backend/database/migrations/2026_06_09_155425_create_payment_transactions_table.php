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
            'payment_transactions',
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
                | Transaction Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'transaction_id'
                )->unique();

                $table->string(
                    'gateway_transaction_id'
                )->nullable();

                $table->string(
                    'gateway_order_id'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Gateway Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'gateway'
                );

                $table->string(
                    'method'
                );

                /*
                |--------------------------------------------------------------------------
                | Transaction Type
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'type',
                    [
                        'payment',
                        'capture',
                        'settlement',
                        'refund',
                        'partial_refund',
                        'chargeback',
                        'void',
                    ]
                )->default('payment');

                /*
                |--------------------------------------------------------------------------
                | Amount
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'amount',
                    15,
                    2
                );

                $table->decimal(
                    'fee_amount',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'net_amount',
                    15,
                    2
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'status',
                    [
                        'pending',
                        'success',
                        'failed',
                        'cancelled',
                        'expired',
                    ]
                )->default('pending');

                /*
                |--------------------------------------------------------------------------
                | Reference
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'reference_number'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Gateway Response
                |--------------------------------------------------------------------------
                */

                $table->json(
                    'request_payload'
                )->nullable();

                $table->json(
                    'response_payload'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'notes'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Transaction Time
                |--------------------------------------------------------------------------
                */

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
                    'transaction_id'
                );

                $table->index(
                    'gateway_transaction_id'
                );

                $table->index(
                    'gateway_order_id'
                );

                $table->index(
                    'gateway'
                );

                $table->index(
                    'method'
                );

                $table->index(
                    'type'
                );

                $table->index(
                    'status'
                );

                $table->index(
                    'processed_at'
                );

                $table->index([
                    'payment_id',
                    'status',
                ]);

                $table->index([
                    'gateway',
                    'status',
                ]);

                $table->index([
                    'type',
                    'status',
                ]);

                $table->index([
                    'status',
                    'processed_at',
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
            'payment_transactions'
        );
    }
};