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
        // Enable PostGIS extension if not already enabled
        try {
            DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');
        } catch (\Exception $e) {
            // PostGIS might not be available, continue without it
        }

        // Add location_point geometry column if it doesn't exist
        if (!Schema::hasColumn('properties', 'location_point')) {
            Schema::table('properties', function (Blueprint $table) {
                DB::statement('ALTER TABLE properties ADD COLUMN location_point geometry(Point, 4326) NULL');
            });
        }

        // Populate location_point from existing latitude/longitude data
        DB::statement("
            UPDATE properties 
            SET location_point = ST_SetSRID(ST_MakePoint(longitude, latitude), 4326)
            WHERE longitude IS NOT NULL AND latitude IS NOT NULL AND location_point IS NULL
        ");

        // Create spatial index for location_point if it doesn't exist
        try {
            DB::statement('CREATE INDEX IF NOT EXISTS properties_location_point_idx ON properties USING GIST(location_point)');
        } catch (\Exception $e) {
            // Index creation might fail if PostGIS is not available
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop spatial index
        try {
            DB::statement('DROP INDEX IF EXISTS properties_location_point_idx');
        } catch (\Exception $e) {
            // Index might not exist
        }

        // Drop location_point column
        if (Schema::hasColumn('properties', 'location_point')) {
            Schema::table('properties', function (Blueprint $table) {
                DB::statement('ALTER TABLE properties DROP COLUMN IF EXISTS location_point');
            });
        }
    }
};
