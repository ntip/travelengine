<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scrape_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrape_id')->constrained('scrapes')->cascadeOnDelete();
            $table->foreignId('route_job_id')->constrained('route_jobs')->cascadeOnDelete();
            $table->longText('content')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrape_logs');
    }
};
