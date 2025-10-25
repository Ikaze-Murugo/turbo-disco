<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Composite indexes for common filter combinations
            $table->index(['status', 'is_available', 'type'], 'idx_properties_status_available_type');
            $table->index(['status', 'is_available', 'price'], 'idx_properties_status_available_price');
            $table->index(['status', 'is_available', 'bedrooms'], 'idx_properties_status_available_bedrooms');
            $table->index(['status', 'is_available', 'bathrooms'], 'idx_properties_status_available_bathrooms');
            $table->index(['status', 'is_available', 'furnishing_status'], 'idx_properties_status_available_furnishing');
            $table->index(['status', 'is_available', 'neighborhood'], 'idx_properties_status_available_neighborhood');
            
            // Individual column indexes for specific filters
            $table->index('price', 'idx_properties_price');
            $table->index('bedrooms', 'idx_properties_bedrooms');
            $table->index('bathrooms', 'idx_properties_bathrooms');
            $table->index('area', 'idx_properties_area');
            $table->index('furnishing_status', 'idx_properties_furnishing_status');
            $table->index('type', 'idx_properties_type');
            $table->index('created_at', 'idx_properties_created_at');
            $table->index('updated_at', 'idx_properties_updated_at');
            
            // Featured properties optimization
            $table->index(['is_featured', 'status', 'is_available'], 'idx_properties_featured_status_available');
            $table->index(['featured_until', 'is_featured'], 'idx_properties_featured_until');
            $table->index('priority', 'idx_properties_priority');
            
            // Location-based search optimization
            $table->index('address', 'idx_properties_address');
            $table->index('location', 'idx_properties_location');
            
            // Policy filters
            $table->index('pets_allowed', 'idx_properties_pets_allowed');
            $table->index('smoking_allowed', 'idx_properties_smoking_allowed');
            $table->index('parking_spaces', 'idx_properties_parking_spaces');
            
            // Amenity flags for quick filtering
            $table->index('has_balcony', 'idx_properties_has_balcony');
            $table->index('has_garden', 'idx_properties_has_garden');
            $table->index('has_pool', 'idx_properties_has_pool');
            $table->index('has_gym', 'idx_properties_has_gym');
            $table->index('has_security', 'idx_properties_has_security');
            $table->index('has_elevator', 'idx_properties_has_elevator');
            $table->index('has_air_conditioning', 'idx_properties_has_air_conditioning');
            $table->index('has_heating', 'idx_properties_has_heating');
            $table->index('has_internet', 'idx_properties_has_internet');
            $table->index('has_cable_tv', 'idx_properties_has_cable_tv');
        });

        // Add indexes to property_amenities table for better JOIN performance
        Schema::table('property_amenities', function (Blueprint $table) {
            $table->index(['amenity_id', 'distance_km'], 'idx_property_amenities_amenity_distance');
            $table->index(['property_id', 'amenity_id'], 'idx_property_amenities_property_amenity');
        });

        // Add indexes to amenities table
        Schema::table('amenities', function (Blueprint $table) {
            $table->index('name', 'idx_amenities_name');
            $table->index('type', 'idx_amenities_type');
            $table->index('is_active', 'idx_amenities_is_active');
        });

        // Add indexes to users table for landlord filtering
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'is_active'], 'idx_users_role_active');
        });

        // Create full-text search indexes for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX idx_properties_title_fts ON properties USING gin(to_tsvector(\'english\', title))');
            DB::statement('CREATE INDEX idx_properties_description_fts ON properties USING gin(to_tsvector(\'english\', description))');
            DB::statement('CREATE INDEX idx_properties_address_fts ON properties USING gin(to_tsvector(\'english\', address))');
            DB::statement('CREATE INDEX idx_properties_neighborhood_fts ON properties USING gin(to_tsvector(\'english\', neighborhood))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Drop composite indexes
            $table->dropIndex('idx_properties_status_available_type');
            $table->dropIndex('idx_properties_status_available_price');
            $table->dropIndex('idx_properties_status_available_bedrooms');
            $table->dropIndex('idx_properties_status_available_bathrooms');
            $table->dropIndex('idx_properties_status_available_furnishing');
            $table->dropIndex('idx_properties_status_available_neighborhood');
            
            // Drop individual indexes
            $table->dropIndex('idx_properties_price');
            $table->dropIndex('idx_properties_bedrooms');
            $table->dropIndex('idx_properties_bathrooms');
            $table->dropIndex('idx_properties_area');
            $table->dropIndex('idx_properties_furnishing_status');
            $table->dropIndex('idx_properties_type');
            $table->dropIndex('idx_properties_created_at');
            $table->dropIndex('idx_properties_updated_at');
            
            // Drop featured properties indexes
            $table->dropIndex('idx_properties_featured_status_available');
            $table->dropIndex('idx_properties_featured_until');
            $table->dropIndex('idx_properties_priority');
            
            // Drop location indexes
            $table->dropIndex('idx_properties_address');
            $table->dropIndex('idx_properties_location');
            
            // Drop policy indexes
            $table->dropIndex('idx_properties_pets_allowed');
            $table->dropIndex('idx_properties_smoking_allowed');
            $table->dropIndex('idx_properties_parking_spaces');
            
            // Drop amenity flag indexes
            $table->dropIndex('idx_properties_has_balcony');
            $table->dropIndex('idx_properties_has_garden');
            $table->dropIndex('idx_properties_has_pool');
            $table->dropIndex('idx_properties_has_gym');
            $table->dropIndex('idx_properties_has_security');
            $table->dropIndex('idx_properties_has_elevator');
            $table->dropIndex('idx_properties_has_air_conditioning');
            $table->dropIndex('idx_properties_has_heating');
            $table->dropIndex('idx_properties_has_internet');
            $table->dropIndex('idx_properties_has_cable_tv');
        });

        Schema::table('property_amenities', function (Blueprint $table) {
            $table->dropIndex('idx_property_amenities_amenity_distance');
            $table->dropIndex('idx_property_amenities_property_amenity');
        });

        Schema::table('amenities', function (Blueprint $table) {
            $table->dropIndex('idx_amenities_name');
            $table->dropIndex('idx_amenities_type');
            $table->dropIndex('idx_amenities_is_active');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_active');
        });

        // Drop full-text search indexes for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS idx_properties_title_fts');
            DB::statement('DROP INDEX IF EXISTS idx_properties_description_fts');
            DB::statement('DROP INDEX IF EXISTS idx_properties_address_fts');
            DB::statement('DROP INDEX IF EXISTS idx_properties_neighborhood_fts');
        }
    }
};