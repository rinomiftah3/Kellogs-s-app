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
            'order_histories',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('order_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | History Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'action'
                );

                $table->string(
                    'old_status'
                )->nullable();

                $table->string(
                    'new_status'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Description
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'description'
                )->nullable();

                $table->text(
                    'notes'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Source
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'source',
                    [
                        'system',
                        'customer',
                        'admin',
                        'payment_gateway',
                        'courier',
                    ]
                )->default('system');

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
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'order_id'
                );

                $table->index(
                    'user_id'
                );

                $table->index(
                    'action'
                );

                $table->index(
                    'source'
                );

                $table->index(
                    'new_status'
                );

                $table->index([
                    'order_id',
                    'created_at',
                ]);

                $table->index([
                    'order_id',
                    'new_status',
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
            'order_histories'
        );
    }
};