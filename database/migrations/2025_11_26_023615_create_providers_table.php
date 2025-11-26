<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            // Primary key is the IATA-like code (VA, EY, CX)
            $table->string('code', 3)->primary();
            $table->string('name')->nullable(); // e.g. "Virgin Australia"
            $table->string('country')->nullable(); // optional
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
