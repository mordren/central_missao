<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_user', function (Blueprint $table) {
            $table->boolean('rsvp_confirmed')->default(false)->after('confirmed_at');
            $table->boolean('did_participate')->default(false)->after('rsvp_confirmed');
            $table->decimal('points_awarded', 8, 2)->nullable()->after('did_participate');
            $table->boolean('penalty_applied')->default(false)->after('points_awarded');

            // Index for fast lazy-penalty lookups
            $table->index(['rsvp_confirmed', 'did_participate', 'penalty_applied'], 'idx_au_penalty_lookup');
        });
    }

    public function down(): void
    {
        Schema::table('activity_user', function (Blueprint $table) {
            $table->dropIndex('idx_au_penalty_lookup');
            $table->dropColumn(['rsvp_confirmed', 'did_participate', 'points_awarded', 'penalty_applied']);
        });
    }
};
