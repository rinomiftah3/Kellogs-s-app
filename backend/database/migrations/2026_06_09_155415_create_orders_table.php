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
            'orders',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('customer_profile_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->foreignId('shipping_address_id')
                    ->constrained('customer_addresses')
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Order Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'order_number',
                    50
                )->unique();

                $table->string(
                    'status',
                    30
                )->default('pending');

                $table->string(
                    'payment_status',
                    30
                )->default('pending');

                $table->string(
                    'fulfillment_status',
                    30
                )->default('pending');

                /*
                |--------------------------------------------------------------------------
                | Customer Snapshot
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'customer_name'
                );

                $table->string(
                    'customer_email'
                );

                $table->string(
                    'customer_phone',
                    30
                );

                /*
                |--------------------------------------------------------------------------
                | Shipping Address Snapshot
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'recipient_name'
                );

                $table->string(
                    'recipient_phone',
                    30
                );

                $table->text(
                    'shipping_address'
                );

                $table->string(
                    'province'
                );

                $table->string(
                    'city'
                );

                $table->string(
                    'district'
                )->nullable();

                $table->string(
                    'postal_code',
                    20
                );

                /*
                |--------------------------------------------------------------------------
                | Pricing Summary
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'subtotal',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'shipping_cost',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'discount_amount',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'tax_amount',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'grand_total',
                    15,
                    2
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Voucher Snapshot
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'voucher_code',
                    100
                )->nullable();

                $table->decimal(
                    'voucher_discount',
                    15,
                    2
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Shipping Snapshot
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'courier_code',
                    50
                )->nullable();

                $table->string(
                    'courier_service',
                    100
                )->nullable();

                $table->string(
                    'tracking_number',
                    100
                )->nullable();

                $table->integer(
                    'total_weight'
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'customer_notes'
                )->nullable();

                $table->text(
                    'admin_notes'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Activity
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'ordered_at'
                )->useCurrent();

                $table->timestamp(
                    'paid_at'
                )->nullable();

                $table->timestamp(
                    'shipped_at'
                )->nullable();

                $table->timestamp(
                    'completed_at'
                )->nullable();

                $table->timestamp(
                    'cancelled_at'
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
                | Soft Deletes
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
                    'shipping_address_id'
                );

                $table->index(
                    'order_number'
                );

                $table->index(
                    'status'
                );

                $table->index(
                    'payment_status'
                );

                $table->index(
                    'fulfillment_status'
                );

                $table->index(
                    'tracking_number'
                );

                $table->index(
                    'ordered_at'
                );

                $table->index(
                    'paid_at'
                );

                $table->index([
                    'customer_profile_id',
                    'status',
                ]);

                $table->index([
                    'status',
                    'ordered_at',
                ]);

                $table->index([
                    'payment_status',
                    'ordered_at',
                ]);

                $table->index([
                    'fulfillment_status',
                    'ordered_at',
                ]);

                $table->index([
                    'created_at',
                    'grand_total',
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
            'orders'
        );
    }
};