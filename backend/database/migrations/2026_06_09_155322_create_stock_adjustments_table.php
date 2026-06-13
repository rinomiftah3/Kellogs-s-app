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
            'stock_adjustments',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('product_sku_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Adjustment Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'adjustment_number',
                    100
                )->unique();

                $table->string(
                    'type',
                    50
                );

                /*
                |--------------------------------------------------------------------------
                | Types:
                |
                | increase
                | decrease
                | correction
                | damaged
                | expired
                | lost
                |--------------------------------------------------------------------------
                */

                $table->integer(
                    'old_stock'
                );

                $table->integer(
                    'new_stock'
                );

                $table->integer(
                    'difference'
                );

                /*
                |--------------------------------------------------------------------------
                | Reason
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'reason'
                );

                $table->text(
                    'notes'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Approval Workflow
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

                $table->foreignId(
                    'requested_by'
                )
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId(
                    'approved_by'
                )
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamp(
                    'approved_at'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Adjustment Date
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'adjustment_date'
                )
                    ->useCurrent();

                /*
                |--------------------------------------------------------------------------
                | Timestamps
                |--------------------------------------------------------------------------
                */

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Soft Delete
                |--------------------------------------------------------------------------
                */

                $table->softDeletes();

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'product_sku_id'
                );

                $table->index(
                    'adjustment_number'
                );

                $table->index(
                    'type'
                );

                $table->index(
                    'status'
                );

                $table->index(
                    'requested_by'
                );

                $table->index(
                    'approved_by'
                );

                $table->index(
                    'adjustment_date'
                );

                $table->index([
                    'product_sku_id',
                    'adjustment_date',
                ]);

                $table->index([
                    'status',
                    'adjustment_date',
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
            'stock_adjustments'
        );
    }
};