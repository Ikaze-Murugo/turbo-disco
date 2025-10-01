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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reported_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('reported_property_id')->nullable()->constrained('properties')->onDelete('cascade');
            $table->foreignId('reported_message_id')->nullable()->constrained('messages')->onDelete('cascade');
            $table->enum('report_type', ['property', 'user', 'message', 'bug', 'feature_request']);
            $table->enum('category', [
                'inappropriate_content', 'fraud', 'harassment', 'spam', 'fake_listing', 
                'technical_issue', 'feature_request', 'other'
            ]);
            $table->string('title');
            $table->text('description');
            $table->json('evidence_urls')->nullable(); // Screenshots, etc.
            $table->enum('status', ['pending', 'investigating', 'resolved', 'dismissed'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->text('admin_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->json('resolution_actions')->nullable(); // Actions taken by admin
            $table->timestamps();

            // Indexes for better performance
            $table->index(['status', 'priority']);
            $table->index(['report_type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};