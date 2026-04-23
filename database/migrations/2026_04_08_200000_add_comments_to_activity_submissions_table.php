<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_submissions', 'comment')) {
                $table->text('comment')->nullable()->after('submitted_at');
            }
            if (!Schema::hasColumn('activity_submissions', 'reviewer_comment')) {
                $table->text('reviewer_comment')->nullable()->after('comment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_submissions', function (Blueprint $table) {
            $table->dropColumn(['comment', 'reviewer_comment']);
        });
    }
};
