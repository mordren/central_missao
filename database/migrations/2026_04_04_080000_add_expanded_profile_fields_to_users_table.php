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
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('email');
            $table->string('religion')->nullable()->after('neighborhood');
            $table->string('education_level')->nullable()->after('religion');
            $table->string('higher_course')->nullable()->after('education_level');
            $table->string('profession')->nullable()->after('higher_course');
            $table->text('how_known')->nullable()->after('profession');
            $table->string('first_spokesperson')->nullable()->after('how_known');
            $table->string('pauta1')->nullable()->after('first_spokesperson');
            $table->string('pauta2')->nullable()->after('pauta1');
            $table->string('pauta3')->nullable()->after('pauta2');
            $table->string('political_ambition')->nullable()->after('pauta3');
            $table->string('current_status')->nullable()->after('political_ambition');
            $table->timestamp('profile_completed_at')->nullable()->after('current_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth', 'religion', 'education_level', 'higher_course', 'profession',
                'how_known', 'first_spokesperson', 'pauta1', 'pauta2', 'pauta3', 'political_ambition',
                'current_status', 'profile_completed_at'
            ]);
        });
    }
};
