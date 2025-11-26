<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('route_jobs', function (Blueprint $table) {
            $table->id(); // bigint auto-increment for the job itself

            // UUID FK → routes.id
            $table->uuid('route_id');
            $table->foreign('route_id')
                ->references('id')
                ->on('routes')
                ->onDelete('cascade');

            // Travel date this job is responsible for
            $table->date('job_date');

            // pending | running | success | failed | archived
            $table->string('status')->default('pending');

            // Archive flag
            $table->boolean('archived')->default(false);

            // When this job should next be run
            $table->timestamp('next_run_at')->nullable();

            // When the last SUCCESSFUL hydration happened
            $table->timestamp('last_hydrated_at')->nullable();

            $table->timestamps();

            // One job per route+date
            $table->unique(['route_id', 'job_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_jobs');
    }
};
