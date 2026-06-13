<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relationship
            |--------------------------------------------------------------------------
            */

            $table->foreignId('customer_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Transaction Information
            |--------------------------------------------------------------------------
            */

            $table->string('transaction_number')
                ->unique();

            $table->enum('type', [
                'earn',
                'redeem',
                'expire',
                'refund',
                'adjustment',
                'bonus',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Point Values
            |--------------------------------------------------------------------------
            */

            $table->integer('points');

            $table->integer('balance_before')
                ->default(0);

            $table->integer('balance_after')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Reference Information
            |--------------------------------------------------------------------------
            */

            $table->string('reference_type')
                ->nullable();

            $table->unsignedBigInteger('reference_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

            $table->string('title');

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Expiration
            |--------------------------------------------------------------------------
            */

            $table->dateTime('expired_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Approval
            |--------------------------------------------------------------------------
            */

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('approved_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'pending',
                'completed',
                'cancelled',
            ])->default('completed');

            /*
            |--------------------------------------------------------------------------
            | Additional Data
            |--------------------------------------------------------------------------
            */

            $table->json('metadata')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Transaction Date
            |--------------------------------------------------------------------------
            */

            $table->dateTime('transaction_at')
                ->nullable();

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

            $table->index('customer_profile_id');

            $table->index('order_id');

            $table->index('type');

            $table->index('status');

            $table->index('transaction_at');

            $table->index('expired_at');

            $table->index([
                'customer_profile_id',
                'transaction_at',
            ]);

            $table->index([
                'customer_profile_id',
                'type',
            ]);

            $table->index([
                'type',
                'status',
            ]);

            $table->index([
                'reference_type',
                'reference_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};