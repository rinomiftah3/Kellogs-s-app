<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'products',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('category_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Product Information
                |--------------------------------------------------------------------------
                */

                $table->string('name');

                $table->string('slug')
                    ->unique();

                $table->text('description')
                    ->nullable();

                $table->decimal(
                    'price',
                    12,
                    2
                )->default(0);

                $table->integer('stock')
                    ->default(0);

                $table->string(
                    'image',
                    2048
                )->nullable();

                $table->boolean('is_active')
                    ->default(true);

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

                $table->index('name');

                $table->index('stock');

                $table->index('is_active');

                $table->index([
                    'category_id',
                    'is_active',
                ]);

                $table->index([
                    'is_active',
                    'created_at',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'products'
        );
    }
};