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
            'voucher_usages',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('voucher_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->foreignId('customer_profile_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->foreignId('order_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Voucher Snapshot
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'voucher_code',
                    100
                );

                $table->string(
                    'voucher_name'
                );

                /*
                |--------------------------------------------------------------------------
                | Discount Snapshot
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'discount_amount',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'order_subtotal',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'order_total',
                    15,
                    2
                )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Usage Status
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'status',
                    [
                        'reserved',
                        'used',
                        'cancelled',
                        'expired',
                    ]
                )
                    ->default('used');

                /*
                |--------------------------------------------------------------------------
                | Validation
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_valid'
                )
                    ->default(true);

                /*
                |--------------------------------------------------------------------------
                | Activity
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'used_at'
                )
                    ->useCurrent();

                /*
                |--------------------------------------------------------------------------
                | Additional Information
                |--------------------------------------------------------------------------
                */

                $table->json(
                    'metadata'
                )
                    ->nullable();

                $table->text(
                    'notes'
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
                    'voucher_id'
                );

                $table->index(
                    'customer_profile_id'
                );

                $table->index(
                    'order_id'
                );

                $table->index(
                    'voucher_code'
                );

                $table->index(
                    'status'
                );

                $table->index(
                    'used_at'
                );

                $table->index(
                    'is_valid'
                );

                $table->index([
                    'voucher_id',
                    'customer_profile_id',
                ]);

                $table->index([
                    'customer_profile_id',
                    'used_at',
                ]);

                $table->index([
                    'status',
                    'used_at',
                ]);

                $table->index([
                    'voucher_code',
                    'used_at',
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
            'voucher_usages'
        );
    }
};