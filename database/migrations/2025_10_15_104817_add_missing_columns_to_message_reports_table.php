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
            // Add missing columns only if they don't exist
            if (!Schema::hasColumn('message_reports', 'conversation_id')) {
                $table->string('conversation_id')->nullable()->after('message_id');
            }
            if (!Schema::hasColumn('message_reports', 'sender_id')) {
                $table->unsignedBigInteger('sender_id')->nullable()->after('conversation_id');
            }
            if (!Schema::hasColumn('message_reports', 'recipient_id')) {
                $table->unsignedBigInteger('recipient_id')->nullable()->after('sender_id');
            }
            if (!Schema::hasColumn('message_reports', 'message_content')) {
                $table->text('message_content')->nullable()->after('recipient_id');
            }
        });
        
        // Add foreign key constraints separately (check if they don't exist)
        Schema::table('message_reports', function (Blueprint $table) {
            if (Schema::hasColumn('message_reports', 'sender_id')) {
                try {
                    $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
            if (Schema::hasColumn('message_reports', 'recipient_id')) {
                try {
                    $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_reports', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['recipient_id']);
            
            // Drop columns
            $table->dropColumn(['conversation_id', 'sender_id', 'recipient_id', 'message_content']);
        });
    }
};
