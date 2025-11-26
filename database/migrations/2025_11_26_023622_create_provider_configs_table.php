<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('provider_configs', function (Blueprint $table) {
            $table->id();
            // FK → providers.code
            $table->string('provider_code', 3);
            $table->string('name', 255);
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->unique(['provider_code', 'name']);
            $table->foreign('provider_code')
                ->references('code')
                ->on('providers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_configs');
    }
};
