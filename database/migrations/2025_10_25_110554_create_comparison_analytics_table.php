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
        Schema::create('comparison_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->json('property_ids');
            $table->string('session_id')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('comparison_duration')->default(0); // in seconds
            $table->integer('properties_viewed')->default(0);
            $table->boolean('comparison_completed')->default(false);
            $table->string('conversion_type')->nullable(); // 'inquiry', 'favorite', 'share', etc.
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
            $table->index(['comparison_completed', 'created_at']);
            $table->index(['conversion_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparison_analytics');
    }
};
