<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scrapes', function (Blueprint $table) {
            $table->unsignedInteger('attempt')->default(0)->after('finished_at');
        });
    }

    public function down(): void
    {
        Schema::table('scrapes', function (Blueprint $table) {
            $table->dropColumn('attempt');
        });
    }
};
