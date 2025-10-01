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
        Schema::create('message_report_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_report_id');
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            $table->boolean('is_internal')->default(false);
            $table->boolean('is_admin_comment')->default(false);
            $table->timestamps();
            
            $table->foreign('message_report_id')->references('id')->on('message_reports')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['message_report_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_report_comments');
    }
};