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
        Schema::create('email_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Property notifications
            $table->boolean('property_approved')->default(true);
            $table->boolean('property_rejected')->default(true);
            
            // Review notifications
            $table->boolean('review_approved')->default(true);
            $table->boolean('review_rejected')->default(true);
            $table->boolean('new_review_received')->default(true);
            
            // System notifications
            $table->boolean('system_updates')->default(true);
            $table->boolean('account_security')->default(true);
            
            // Email frequency
            $table->enum('frequency', ['immediate', 'daily', 'weekly'])->default('immediate');
            
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_preferences');
    }
};
