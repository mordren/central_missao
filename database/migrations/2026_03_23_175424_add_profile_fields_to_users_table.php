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
            $table->enum('role', ['participante', 'coordenador', 'administrador'])->default('participante')->after('phone');
            $table->string('city')->nullable()->after('email');
            $table->string('neighborhood')->nullable()->after('city');
            $table->string('referral_code')->unique()->nullable()->after('neighborhood');
            $table->string('referred_by')->nullable()->after('referral_code');
            $table->integer('points')->default(0)->after('referred_by');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'city', 'neighborhood', 'referral_code', 'referred_by', 'points']);
        });
    }
};
