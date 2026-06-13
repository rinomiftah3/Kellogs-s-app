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
            'product_images',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('product_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Image Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'image_url',
                    2048
                );

                $table->string(
                    'alt_text',
                    255
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Display
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'sort_order'
                )
                    ->default(0);

                $table->boolean(
                    'is_primary'
                )
                    ->default(false);

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_active'
                )
                    ->default(true);

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
                    'product_id'
                );

                $table->index([
                    'product_id',
                    'sort_order',
                ]);

                $table->index([
                    'product_id',
                    'is_primary',
                ]);

                $table->index([
                    'product_id',
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
            'product_images'
        );
    }
};