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
        Schema::table('users', function (Blueprint $table) {
            // Basic profile information
            $table->string('profile_picture')->nullable()->after('email');
            $table->text('bio')->nullable()->after('profile_picture');
            $table->string('phone_number')->nullable()->after('bio');
            $table->date('date_of_birth')->nullable()->after('phone_number');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->string('location')->nullable()->after('gender');
            $table->string('website')->nullable()->after('location');
            
            // Social links and preferences (JSON fields)
            $table->json('social_links')->nullable()->after('website');
            $table->json('preferences')->nullable()->after('social_links');
            
            // Activity tracking
            $table->timestamp('last_active_at')->nullable()->after('preferences');
            $table->integer('profile_completion_percentage')->default(0)->after('last_active_at');
            
            // Role-specific fields
            $table->string('business_name')->nullable()->after('profile_completion_percentage');
            $table->string('business_license')->nullable()->after('business_name');
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->nullable()->after('business_license');
            
            // Admin-specific fields
            $table->integer('admin_level')->default(1)->after('verification_status');
            $table->json('admin_permissions')->nullable()->after('admin_level');
            
            // Emergency contact information
            $table->json('emergency_contact')->nullable()->after('admin_permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_picture',
                'bio',
                'phone_number',
                'date_of_birth',
                'gender',
                'location',
                'website',
                'social_links',
                'preferences',
                'last_active_at',
                'profile_completion_percentage',
                'business_name',
                'business_license',
                'verification_status',
                'admin_level',
                'admin_permissions',
                'emergency_contact'
            ]);
        });
    }
};