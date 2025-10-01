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
        Schema::table('properties', function (Blueprint $table) {
            // Property versioning fields
            $table->integer('version')->default(1)->after('id');
            $table->integer('parent_property_id')->nullable()->after('version');
            $table->enum('version_status', ['original', 'pending_update', 'approved_update'])->default('original')->after('parent_property_id');
            $table->timestamp('last_approved_at')->nullable()->after('version_status');
            $table->timestamp('update_requested_at')->nullable()->after('last_approved_at');
            $table->text('update_notes')->nullable()->after('update_requested_at');
            $table->json('pending_changes')->nullable()->after('update_notes');
            $table->foreignId('approved_by')->nullable()->after('pending_changes')->constrained('users')->onDelete('set null');
            $table->foreignId('update_requested_by')->nullable()->after('approved_by')->constrained('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index(['parent_property_id', 'version_status']);
            $table->index(['version_status', 'update_requested_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['parent_property_id', 'version_status']);
            $table->dropIndex(['version_status', 'update_requested_at']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['update_requested_by']);
            $table->dropColumn([
                'version',
                'parent_property_id', 
                'version_status',
                'last_approved_at',
                'update_requested_at',
                'update_notes',
                'pending_changes',
                'approved_by',
                'update_requested_by'
            ]);
        });
    }
};