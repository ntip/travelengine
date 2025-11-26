<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scrapes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_job_id')->constrained('route_jobs')->cascadeOnDelete();
            $table->string('provider_code', 3);
            $table->text('provider_url')->nullable();
            $table->string('status')->default('pending'); // pending|running|success|failed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['provider_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrapes');
    }
};
