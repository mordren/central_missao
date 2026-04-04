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
        if (! Schema::hasColumn('activities', 'deadline')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->dateTime('deadline')->nullable()->after('date_time');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('activities', 'deadline')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->dropColumn('deadline');
            });
        }
    }
};
