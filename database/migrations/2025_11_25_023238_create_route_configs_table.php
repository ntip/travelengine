<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes_config', function (Blueprint $table) {
            $table->id(); // auto-increment PK for config row

            // Link back to routes.id (UUID)
            $table->uuid('route_id');

            // Config key/value
            $table->string('name');
            $table->text('value')->nullable();

            // Prevent duplicate config names per route
            $table->unique(['route_id', 'name']);

            $table->timestamps();

            $table->foreign('route_id')
                ->references('id')
                ->on('routes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes_config');
    }
};
