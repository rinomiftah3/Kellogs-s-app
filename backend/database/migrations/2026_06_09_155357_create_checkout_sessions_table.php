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
            'checkout_sessions',
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
                    ->nullable()
                    ->constrained('customer_addresses')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Session Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'session_code',
                    50
                )
                    ->unique();

                $table->string(
                    'status',
                    30
                )
                    ->default('draft');

                /*
                |--------------------------------------------------------------------------
                | Voucher & Promotion
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'voucher_code',
                    100
                )
                    ->nullable();

                $table->decimal(
                    'voucher_discount',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'promotion_discount',
                    15,
                    2
                )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Pricing Summary
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'subtotal',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'shipping_cost',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'total_discount',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'grand_total',
                    15,
                    2
                )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Shipping Snapshot
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'courier_code',
                    50
                )
                    ->nullable();

                $table->string(
                    'courier_service',
                    100
                )
                    ->nullable();

                $table->integer(
                    'total_weight'
                )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Validation Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_price_valid'
                )
                    ->default(true);

                $table->boolean(
                    'is_stock_valid'
                )
                    ->default(true);

                $table->boolean(
                    'is_voucher_valid'
                )
                    ->default(true);

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'notes'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Expiration
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'expired_at'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Activity
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'checked_out_at'
                )
                    ->nullable();

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
                    'session_code'
                );

                $table->index(
                    'status'
                );

                $table->index(
                    'expired_at'
                );

                $table->index(
                    'checked_out_at'
                );

                $table->index(
                    'voucher_code'
                );

                $table->index([
                    'customer_profile_id',
                    'status',
                ]);

                $table->index([
                    'status',
                    'expired_at',
                ]);

                $table->index([
                    'customer_profile_id',
                    'created_at',
                ]);

                $table->index([
                    'status',
                    'checked_out_at',
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
            'checkout_sessions'
        );
    }
};