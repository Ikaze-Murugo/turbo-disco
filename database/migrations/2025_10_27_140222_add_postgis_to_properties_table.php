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
        // Add coordinates column for maps integration (fallback without PostGIS)
        Schema::table('properties', function (Blueprint $table) {
            $table->json('coordinates')->nullable()->after('longitude');
        });
        
        // Populate coordinates from existing latitude/longitude data
        DB::statement("
            UPDATE properties 
            SET coordinates = json_build_object('lat', latitude, 'lng', longitude) 
            WHERE longitude IS NOT NULL AND latitude IS NOT NULL
        ");
        
        // Create composite indexes for common filter combinations
        DB::statement("CREATE INDEX properties_status_featured_idx ON properties (status, is_featured);");
        DB::statement("CREATE INDEX properties_type_bedrooms_idx ON properties (type, bedrooms, bathrooms);");
        DB::statement("CREATE INDEX properties_price_idx ON properties (price);");
        DB::statement("CREATE INDEX properties_location_idx ON properties (location);");
        DB::statement("CREATE INDEX properties_neighborhood_idx ON properties (neighborhood);");
        
        // Create index for featured properties with expiration
        DB::statement("CREATE INDEX properties_featured_until_idx ON properties (is_featured, featured_until) WHERE is_featured = true;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes
        DB::statement("DROP INDEX IF EXISTS properties_location_point_idx;");
        DB::statement("DROP INDEX IF EXISTS properties_status_featured_idx;");
        DB::statement("DROP INDEX IF EXISTS properties_type_bedrooms_idx;");
        DB::statement("DROP INDEX IF EXISTS properties_price_idx;");
        DB::statement("DROP INDEX IF EXISTS properties_location_idx;");
        DB::statement("DROP INDEX IF EXISTS properties_neighborhood_idx;");
        DB::statement("DROP INDEX IF EXISTS properties_featured_until_idx;");
        
        // Drop geometry column
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('location_point');
        });
        
        // Note: We don't drop the PostGIS extension as it might be used by other tables
    }
};