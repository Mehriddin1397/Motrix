<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('news') && !Schema::hasColumn('news', 'views_count')) {
            Schema::table('news', function (Blueprint $table) {
                $table->unsignedInteger('views_count')->default(0);
            });
        }

        if (Schema::hasTable('parts') && !Schema::hasColumn('parts', 'views_count')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->unsignedInteger('views_count')->default(0);
            });
        }

        if (Schema::hasTable('service_providers') && !Schema::hasColumn('service_providers', 'views_count')) {
            Schema::table('service_providers', function (Blueprint $table) {
                $table->unsignedInteger('views_count')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('news') && Schema::hasColumn('news', 'views_count')) {
            Schema::table('news', function (Blueprint $table) {
                $table->dropColumn('views_count');
            });
        }

        if (Schema::hasTable('parts') && Schema::hasColumn('parts', 'views_count')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->dropColumn('views_count');
            });
        }

        if (Schema::hasTable('service_providers') && Schema::hasColumn('service_providers', 'views_count')) {
            Schema::table('service_providers', function (Blueprint $table) {
                $table->dropColumn('views_count');
            });
        }
    }
};
