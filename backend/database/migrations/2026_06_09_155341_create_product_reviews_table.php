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
            'product_reviews',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('product_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->foreignId('customer_profile_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Rating
                |--------------------------------------------------------------------------
                */

                $table->unsignedTinyInteger(
                    'rating'
                );

                /*
                |--------------------------------------------------------------------------
                | Review Content
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'title',
                    255
                )
                    ->nullable();

                $table->text(
                    'review'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Verification
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_verified_purchase'
                )
                    ->default(false);

                /*
                |--------------------------------------------------------------------------
                | Moderation
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'status',
                    30
                )
                    ->default('pending');

                /*
                |--------------------------------------------------------------------------
                | pending
                | approved
                | rejected
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'moderation_notes'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Metadata
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'helpful_count'
                )
                    ->default(0);

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
                    'product_id'
                );

                $table->index(
                    'customer_profile_id'
                );

                $table->index([
                    'product_id',
                    'rating',
                ]);

                $table->index([
                    'product_id',
                    'status',
                ]);

                $table->index([
                    'product_id',
                    'created_at',
                ]);

                $table->index([
                    'customer_profile_id',
                    'created_at',
                ]);

                $table->index(
                    'is_verified_purchase'
                );

                $table->index(
                    'status'
                );

                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate Reviews
                |--------------------------------------------------------------------------
                |
                | Satu customer hanya boleh
                | membuat satu review per produk.
                |
                */

                $table->unique([
                    'product_id',
                    'customer_profile_id',
                ], 'product_customer_review_unique');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'product_reviews'
        );
    }
};