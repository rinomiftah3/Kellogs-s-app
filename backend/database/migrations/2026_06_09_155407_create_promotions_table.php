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
            'promotions',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Promotion Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'name'
                );

                $table->string(
                    'code',
                    100
                )
                    ->unique();

                $table->text(
                    'description'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Promotion Type
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'type',
                    [
                        'fixed_discount',
                        'percentage_discount',
                        'flash_sale',
                        'buy_x_get_y',
                        'free_shipping',
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | Discount Configuration
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'discount_value',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'maximum_discount',
                    15,
                    2
                )
                    ->nullable();

                $table->decimal(
                    'minimum_purchase',
                    15,
                    2
                )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Buy X Get Y Configuration
                |--------------------------------------------------------------------------
                */

                $table->integer(
                    'buy_quantity'
                )
                    ->nullable();

                $table->integer(
                    'free_quantity'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Usage Limitation
                |--------------------------------------------------------------------------
                */

                $table->integer(
                    'usage_limit'
                )
                    ->nullable();

                $table->integer(
                    'used_count'
                )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Promotion Flags
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_active'
                )
                    ->default(true);

                $table->boolean(
                    'is_featured'
                )
                    ->default(false);

                $table->boolean(
                    'is_stackable'
                )
                    ->default(false);

                /*
                |--------------------------------------------------------------------------
                | Schedule
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
                | Display
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'banner_image',
                    2048
                )
                    ->nullable();

                $table->integer(
                    'sort_order'
                )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Metadata
                |--------------------------------------------------------------------------
                */

                $table->json(
                    'metadata'
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
                    'code'
                );

                $table->index(
                    'type'
                );

                $table->index(
                    'is_active'
                );

                $table->index(
                    'is_featured'
                );

                $table->index(
                    'start_at'
                );

                $table->index(
                    'end_at'
                );

                $table->index(
                    'sort_order'
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
                    'is_featured',
                    'is_active',
                ]);

                $table->index([
                    'is_active',
                    'sort_order',
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
            'promotions'
        );
    }
};