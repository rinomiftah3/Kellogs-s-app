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
            'customer_addresses',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relationship
                |--------------------------------------------------------------------------
                */

                $table->foreignId('customer_profile_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Address Identity
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'label',
                    100
                );

                /*
                |--------------------------------------------------------------------------
                | Example:
                |
                | Rumah
                | Kantor
                | Gudang
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'recipient_name'
                );

                $table->string(
                    'recipient_phone',
                    30
                );

                /*
                |--------------------------------------------------------------------------
                | Address Information
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'address'
                );

                $table->string(
                    'province'
                );

                $table->string(
                    'city'
                );

                $table->string(
                    'district'
                );

                $table->string(
                    'subdistrict'
                );

                $table->string(
                    'postal_code',
                    20
                );

                /*
                |--------------------------------------------------------------------------
                | Shipping Integration
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'latitude',
                    10,
                    7
                )
                    ->nullable();

                $table->decimal(
                    'longitude',
                    10,
                    7
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Address Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_default'
                )
                    ->default(false);

                $table->boolean(
                    'is_active'
                )
                    ->default(true);

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'notes'
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
                    'customer_profile_id'
                );

                $table->index(
                    'postal_code'
                );

                $table->index(
                    'is_default'
                );

                $table->index(
                    'is_active'
                );

                $table->index([
                    'customer_profile_id',
                    'is_default',
                ]);

                $table->index([
                    'customer_profile_id',
                    'is_active',
                ]);

                $table->index([
                    'province',
                    'city',
                ]);

                $table->index([
                    'province',
                    'city',
                    'district',
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
            'customer_addresses'
        );
    }
};