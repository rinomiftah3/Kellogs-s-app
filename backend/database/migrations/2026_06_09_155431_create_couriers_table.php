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
            'couriers',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Courier Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'name'
                );

                $table->string(
                    'code'
                )->unique();

                $table->string(
                    'provider'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Description
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'description'
                )->nullable();

                $table->string(
                    'logo',
                    2048
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Contact Information
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'website',
                    2048
                )->nullable();

                $table->string(
                    'contact_email'
                )->nullable();

                $table->string(
                    'contact_phone',
                    50
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Tracking Integration
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'tracking_url_template',
                    2048
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Example
                |--------------------------------------------------------------------------
                |
                | https://cekresi.com/?no={tracking_number}
                |
                */

                $table->boolean(
                    'supports_tracking'
                )->default(true);

                $table->boolean(
                    'supports_cod'
                )->default(false);

                $table->boolean(
                    'supports_insurance'
                )->default(false);

                /*
                |--------------------------------------------------------------------------
                | Display Configuration
                |--------------------------------------------------------------------------
                */

                $table->unsignedInteger(
                    'sort_order'
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_active'
                )->default(true);

                /*
                |--------------------------------------------------------------------------
                | Publish
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'published_at'
                )->nullable();

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

                $table->softDeletes();

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'name'
                );

                $table->index(
                    'code'
                );

                $table->index(
                    'provider'
                );

                $table->index(
                    'is_active'
                );

                $table->index(
                    'sort_order'
                );

                $table->index(
                    'published_at'
                );

                $table->index([
                    'is_active',
                    'name',
                ]);

                $table->index([
                    'provider',
                    'is_active',
                ]);

                $table->index([
                    'is_active',
                    'sort_order',
                ]);

                $table->index([
                    'is_active',
                    'published_at',
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
            'couriers'
        );
    }
};