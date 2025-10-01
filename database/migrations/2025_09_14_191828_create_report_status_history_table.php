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
        Schema::create('report_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->enum('old_status', ['pending', 'investigating', 'resolved', 'dismissed'])->nullable();
            $table->enum('new_status', ['pending', 'investigating', 'resolved', 'dismissed']);
            $table->enum('old_priority', ['low', 'medium', 'high', 'urgent'])->nullable();
            $table->enum('new_priority', ['low', 'medium', 'high', 'urgent'])->nullable();
            $table->text('reason')->nullable(); // Reason for status change
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();

            // Indexes for better performance
            $table->index(['report_id', 'created_at']);
            $table->index(['changed_by', 'created_at']);
            $table->index('new_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_status_history');
    }
};