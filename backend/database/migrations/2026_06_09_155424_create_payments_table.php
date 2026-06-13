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
            'payments',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('order_id')
                    ->unique()
                    ->constrained()
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Payment Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'payment_number'
                )->unique();

                $table->string(
                    'gateway'
                );

                $table->string(
                    'method'
                );

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
                    'paid_amount',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'fee_amount',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'refund_amount',
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
                        'paid',
                        'failed',
                        'expired',
                        'cancelled',
                        'refunded',
                        'partial_refund',
                    ]
                )->default('pending');

                /*
                |--------------------------------------------------------------------------
                | Gateway Reference
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
                | Payment URL
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'payment_url'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Timestamps
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'paid_at'
                )->nullable();

                $table->timestamp(
                    'expired_at'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Additional Information
                |--------------------------------------------------------------------------
                */

                $table->json(
                    'metadata'
                )->nullable();

                $table->text(
                    'notes'
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
                    'order_id'
                );

                $table->index(
                    'payment_number'
                );

                $table->index(
                    'gateway'
                );

                $table->index(
                    'method'
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
                    'paid_at'
                );

                $table->index(
                    'expired_at'
                );

                $table->index([
                    'status',
                    'paid_at',
                ]);

                $table->index([
                    'gateway',
                    'status',
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
            'payments'
        );
    }
};