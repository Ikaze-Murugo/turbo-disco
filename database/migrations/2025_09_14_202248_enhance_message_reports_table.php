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
        Schema::table('message_reports', function (Blueprint $table) {
            // Add missing fields for complete ticketing system
            $table->string('report_type')->default('message')->after('report_id');
            $table->string('category')->after('report_type');
            $table->string('title')->after('category');
            $table->text('description')->after('title');
            $table->json('evidence_urls')->nullable()->after('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('evidence_urls');
            $table->enum('status', ['pending', 'investigating', 'resolved', 'dismissed'])->default('pending')->after('priority');
            $table->unsignedBigInteger('assigned_to')->nullable()->after('status');
            $table->timestamp('resolved_at')->nullable()->after('assigned_to');
            $table->unsignedBigInteger('resolved_by')->nullable()->after('resolved_at');
            $table->json('resolution_actions')->nullable()->after('resolved_by');
            $table->text('resolution_notes')->nullable()->after('resolution_actions');
            
            // Add foreign key constraints
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            
            // Add indexes for better performance
            $table->index(['status', 'priority']);
            $table->index(['sender_id', 'created_at']);
            $table->index(['recipient_id', 'created_at']);
            $table->index(['report_type', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_reports', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['resolved_by']);
            $table->dropIndex(['status', 'priority']);
            $table->dropIndex(['sender_id', 'created_at']);
            $table->dropIndex(['recipient_id', 'created_at']);
            $table->dropIndex(['report_type', 'category']);
            
            $table->dropColumn([
                'report_type', 'category', 'title', 'description', 'evidence_urls',
                'priority', 'status', 'assigned_to', 'resolved_at', 'resolved_by',
                'resolution_actions', 'resolution_notes'
            ]);
        });
    }
};