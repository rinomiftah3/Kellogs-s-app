<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'activity_log',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Activity Information
                |--------------------------------------------------------------------------
                */

                $table->string('log_name')
                    ->nullable()
                    ->index();

                $table->text('description');

                $table->string('event')
                    ->nullable()
                    ->index();

                /*
                |--------------------------------------------------------------------------
                | Subject
                |--------------------------------------------------------------------------
                */

                $table->nullableMorphs(
                    'subject'
                );

                /*
                |--------------------------------------------------------------------------
                | Causer
                |--------------------------------------------------------------------------
                */

                $table->nullableMorphs(
                    'causer'
                );

                /*
                |--------------------------------------------------------------------------
                | Activity Data
                |--------------------------------------------------------------------------
                |
                | Required for Spatie Activity Log v5 compatibility.
                |
                */

                $table->json(
                    'attribute_changes'
                )->nullable();

                $table->json(
                    'properties'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Batch UUID
                |--------------------------------------------------------------------------
                */

                $table->uuid(
                    'batch_uuid'
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
                    'batch_uuid'
                );

                $table->index([
                    'log_name',
                    'event',
                ]);

                $table->index(
                    'created_at'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'activity_log'
        );
    }
};