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
            'vouchers',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Voucher Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'name'
                );

                $table->string(
                    'code',
                    100
                )->unique();

                $table->text(
                    'description'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Voucher Type
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'type',
                    [
                        'fixed',
                        'percentage',
                        'free_shipping',
                    ]
                )->default('fixed');

                /*
                |--------------------------------------------------------------------------
                | Discount Rules
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'discount_value',
                    15,
                    2
                )->default(0);

                $table->decimal(
                    'maximum_discount',
                    15,
                    2
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Purchase Requirement
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'minimum_purchase',
                    15,
                    2
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Usage Rules
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger('usage_limit')
                    ->nullable();

                $table->unsignedInteger('usage_per_user')
                    ->default(1);

                $table->unsignedInteger('used_count')
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_active'
                )->default(true);

                $table->boolean(
                    'is_public'
                )->default(true);

                $table->boolean(
                    'is_stackable'
                )->default(false);

                /*
                |--------------------------------------------------------------------------
                | Validity Period
                |--------------------------------------------------------------------------
                */

                $table->dateTime(
                    'start_at'
                );

                $table->dateTime(
                    'end_at'
                );

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
                    'code'
                );

                $table->index(
                    'type'
                );

                $table->index(
                    'is_active'
                );

                $table->index(
                    'is_public'
                );

                $table->index(
                    'start_at'
                );

                $table->index(
                    'end_at'
                );

                $table->index([
                    'is_active',
                    'start_at',
                    'end_at',
                ]);

                $table->index([
                    'type',
                    'is_active',
                ]);

                $table->index([
                    'is_public',
                    'is_active',
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
            'vouchers'
        );
    }
};
