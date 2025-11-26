<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scrape_logs', function (Blueprint $table) {
            $table->longText('scrape_response_raw')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('scrape_logs', function (Blueprint $table) {
            $table->dropColumn('scrape_response_raw');
        });
    }
};
