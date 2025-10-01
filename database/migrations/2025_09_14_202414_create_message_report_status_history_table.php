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
        Schema::create('message_report_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_report_id');
            $table->unsignedBigInteger('changed_by');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->string('old_priority')->nullable();
            $table->string('new_priority')->nullable();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('message_report_id')->references('id')->on('message_reports')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['message_report_id', 'created_at']);
            $table->index(['changed_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_report_status_history');
    }
};