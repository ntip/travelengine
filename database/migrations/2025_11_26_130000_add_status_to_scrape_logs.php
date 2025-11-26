<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('scrape_logs', function (Blueprint $table) {
            $table->string('status')->nullable()->after('scrape_id');
        });
    }

    public function down()
    {
        Schema::table('scrape_logs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
