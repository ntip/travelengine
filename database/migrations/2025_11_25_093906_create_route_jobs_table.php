<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('route_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')
                ->constrained('routes')
                ->cascadeOnDelete();

            // The travel date this job is responsible for
            $table->date('job_date');

            // pending | running | success | failed | archived
            $table->string('status')->default('pending');

            // Simple archive flag for jobs in the past
            $table->boolean('archived')->default(false);

            // When this job should next be run (worker can respect this)
            $table->timestamp('next_run_at')->nullable();

            // When the last SUCCESSFUL hydration happened
            // (if you hate this, you can remove it & use updated_at instead)
            $table->timestamp('last_hydrated_at')->nullable();

            $table->timestamps();

            // One job per route+date (add provider/cabin here if needed)
            $table->unique(['route_id', 'job_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_jobs');
    }
};
