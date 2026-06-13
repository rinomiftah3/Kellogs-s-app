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
        Schema::table(
            'products',
            function (Blueprint $table) {

                /*
                |--------------------------------------------------------------------------
                | Descriptions
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'short_description',
                    500
                )
                    ->nullable()
                    ->after('slug');

                /*
                |--------------------------------------------------------------------------
                | Media
                |--------------------------------------------------------------------------
                */

                $table->renameColumn(
                    'image',
                    'thumbnail'
                );

                /*
                |--------------------------------------------------------------------------
                | Product Status
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'status',
                    30
                )
                    ->default('draft')
                    ->after('thumbnail');

                $table->boolean(
                    'is_featured'
                )
                    ->default(false)
                    ->after('status');

                $table->timestamp(
                    'published_at'
                )
                    ->nullable()
                    ->after('is_active');

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index([
                    'is_featured',
                    'is_active',
                ]);

                $table->index([
                    'status',
                    'is_active',
                ]);

                $table->index([
                    'created_at',
                    'is_active',
                ]);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Remove Legacy Columns
        |--------------------------------------------------------------------------
        |
        | Stock & Price dipindahkan ke:
        |
        | product_skus
        | inventories
        |
        */

        Schema::table(
            'products',
            function (Blueprint $table) {

                $table->dropColumn([
                    'price',
                    'stock',
                ]);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'products',
            function (Blueprint $table) {

                /*
                |--------------------------------------------------------------------------
                | Restore Legacy Columns
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'price',
                    12,
                    2
                )
                    ->default(0);

                $table->integer(
                    'stock'
                )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Remove Indexes
                |--------------------------------------------------------------------------
                */

                $table->dropIndex([
                    'is_featured',
                    'is_active',
                ]);

                $table->dropIndex([
                    'status',
                    'is_active',
                ]);

                $table->dropIndex([
                    'created_at',
                    'is_active',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Remove New Columns
                |--------------------------------------------------------------------------
                */

                $table->dropColumn([
                    'short_description',
                    'status',
                    'is_featured',
                    'published_at',
                ]);

                $table->renameColumn(
                    'thumbnail',
                    'image'
                );
            }
        );
    }
};