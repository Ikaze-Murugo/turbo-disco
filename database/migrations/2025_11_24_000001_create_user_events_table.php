<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This table tracks user behavior for ML fraud detection.
     */
    public function up(): void
    {
        Schema::create('user_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('session_id')->index();
            $table->string('event_type'); // 'page_view', 'click', 'scroll', 'form_submit', 'search', etc.
            $table->string('page_url', 500);
            $table->string('element_id')->nullable(); // For click events
            $table->string('element_class')->nullable(); // For click events
            $table->integer('scroll_depth')->nullable(); // Percentage scrolled
            $table->integer('time_on_page')->nullable(); // Seconds spent on page
            $table->json('event_data')->nullable(); // Additional event-specific data
            $table->string('ip_address', 45)->index();
            $table->text('user_agent')->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('device_type')->nullable(); // 'desktop', 'mobile', 'tablet'
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
            $table->index(['session_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_events');
    }
};
