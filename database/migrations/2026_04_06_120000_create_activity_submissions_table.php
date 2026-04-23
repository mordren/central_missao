<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('activity_submissions')) {
            Schema::create('activity_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('activity_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('file_path');
                $table->string('original_name');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->string('status')->default('pending');
                $table->integer('points_awarded')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users');
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();

                $table->index(['activity_id', 'user_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('activity_submissions');
    }
};
