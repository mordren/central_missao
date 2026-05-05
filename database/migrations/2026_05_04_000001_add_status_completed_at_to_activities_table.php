<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (! Schema::hasColumn('activities', 'status')) {
                $table->string('status')->default('active')->after('banner');
            }
            if (! Schema::hasColumn('activities', 'completed_at')) {
                $table->dateTime('completed_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('activities', 'skip_points')) {
                $table->boolean('skip_points')->default(false)->after('completed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['status', 'completed_at', 'skip_points']);
        });
    }
};
