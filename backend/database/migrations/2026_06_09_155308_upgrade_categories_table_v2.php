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
            'categories',
            function (Blueprint $table) {

                /*
                |--------------------------------------------------------------------------
                | Category Hierarchy
                |--------------------------------------------------------------------------
                */

                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('id');

                /*
                |--------------------------------------------------------------------------
                | Media
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'image',
                    2048
                )
                    ->nullable()
                    ->after('description');

                /*
                |--------------------------------------------------------------------------
                | Sorting
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'sort_order'
                )
                    ->default(0)
                    ->after('image');

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'parent_id'
                );

                $table->index([
                    'parent_id',
                    'is_active',
                ]);

                $table->index([
                    'is_active',
                    'sort_order',
                ]);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Self Reference Foreign Key
        |--------------------------------------------------------------------------
        */

        Schema::table(
            'categories',
            function (Blueprint $table) {

                $table->foreign('parent_id')
                    ->references('id')
                    ->on('categories')
                    ->restrictOnDelete();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'categories',
            function (Blueprint $table) {

                $table->dropForeign([
                    'parent_id',
                ]);

                $table->dropIndex([
                    'parent_id',
                ]);

                $table->dropIndex([
                    'parent_id',
                    'is_active',
                ]);

                $table->dropIndex([
                    'is_active',
                    'sort_order',
                ]);

                $table->dropColumn([
                    'parent_id',
                    'image',
                    'sort_order',
                ]);
            }
        );
    }
};