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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['evento_presencial', 'denuncia', 'tarefa_manual', 'convite'])->default('evento_presencial');
            $table->dateTime('date_time');
            $table->dateTime('deadline');
            $table->string('location')->nullable();
            $table->integer('points')->default(0);
            $table->string('qr_code')->unique()->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
