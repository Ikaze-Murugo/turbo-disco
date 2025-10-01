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
        // General user statistics table
        Schema::create('user_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('properties_viewed')->default(0);
            $table->integer('properties_favorited')->default(0);
            $table->integer('messages_sent')->default(0);
            $table->integer('reviews_given')->default(0);
            $table->integer('reports_submitted')->default(0);
            $table->integer('searches_performed')->default(0);
            $table->integer('comparison_count')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->json('favorite_categories')->nullable();
            $table->json('search_preferences')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });

        // Landlord-specific statistics
        Schema::create('landlord_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('properties_listed')->default(0);
            $table->integer('properties_approved')->default(0);
            $table->integer('total_views')->default(0);
            $table->integer('total_inquiries')->default(0);
            $table->integer('total_rentals')->default(0);
            $table->decimal('response_rate', 5, 2)->default(0.00); // percentage
            $table->integer('average_response_time')->default(0); // in minutes
            $table->decimal('tenant_satisfaction_score', 3, 2)->default(0.00); // 0.00 to 5.00
            $table->decimal('revenue_generated', 15, 2)->default(0.00);
            $table->json('property_performance')->nullable();
            $table->json('tenant_insights')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });

        // Admin-specific statistics
        Schema::create('admin_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('reports_processed')->default(0);
            $table->integer('properties_approved')->default(0);
            $table->integer('users_managed')->default(0);
            $table->integer('tickets_resolved')->default(0);
            $table->integer('system_actions')->default(0);
            $table->timestamp('last_admin_action')->nullable();
            $table->decimal('admin_efficiency_score', 3, 2)->default(0.00);
            $table->json('action_breakdown')->nullable();
            $table->json('performance_metrics')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_statistics');
        Schema::dropIfExists('landlord_statistics');
        Schema::dropIfExists('user_statistics');
    }
};