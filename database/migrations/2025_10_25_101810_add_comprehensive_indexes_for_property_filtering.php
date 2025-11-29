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
        // Create indexes using raw SQL with IF NOT EXISTS for PostgreSQL
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_properties_status_available_type ON properties (status, is_available, type)",
            "CREATE INDEX IF NOT EXISTS idx_properties_status_available_price ON properties (status, is_available, price)",
            "CREATE INDEX IF NOT EXISTS idx_properties_status_available_bedrooms ON properties (status, is_available, bedrooms)",
            "CREATE INDEX IF NOT EXISTS idx_properties_status_available_bathrooms ON properties (status, is_available, bathrooms)",
            "CREATE INDEX IF NOT EXISTS idx_properties_price ON properties (price)",
            "CREATE INDEX IF NOT EXISTS idx_properties_bedrooms ON properties (bedrooms)",
            "CREATE INDEX IF NOT EXISTS idx_properties_bathrooms ON properties (bathrooms)",
            "CREATE INDEX IF NOT EXISTS idx_properties_type ON properties (type)",
            "CREATE INDEX IF NOT EXISTS idx_properties_created_at ON properties (created_at)",
            "CREATE INDEX IF NOT EXISTS idx_properties_updated_at ON properties (updated_at)",
        ];
        
        // Only create indexes for columns that exist
        $conditionalIndexes = [
            ['column' => 'furnishing_status', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_furnishing_status ON properties (furnishing_status)"],
            ['column' => 'furnishing_status', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_status_available_furnishing ON properties (status, is_available, furnishing_status)"],
            ['column' => 'neighborhood', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_status_available_neighborhood ON properties (status, is_available, neighborhood)"],
            ['column' => 'is_featured', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_featured_status_available ON properties (is_featured, status, is_available)"],
            ['column' => 'featured_until', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_featured_until ON properties (featured_until, is_featured)"],
            ['column' => 'priority', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_priority ON properties (priority)"],
            ['column' => 'address', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_address ON properties (address)"],
            ['column' => 'pets_allowed', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_pets_allowed ON properties (pets_allowed)"],
            ['column' => 'smoking_allowed', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_smoking_allowed ON properties (smoking_allowed)"],
            ['column' => 'parking_spaces', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_parking_spaces ON properties (parking_spaces)"],
            ['column' => 'has_balcony', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_has_balcony ON properties (has_balcony)"],
            ['column' => 'has_garden', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_has_garden ON properties (has_garden)"],
            ['column' => 'has_pool', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_has_pool ON properties (has_pool)"],
            ['column' => 'has_gym', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_has_gym ON properties (has_gym)"],
            ['column' => 'has_security', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_has_security ON properties (has_security)"],
            ['column' => 'has_elevator', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_has_elevator ON properties (has_elevator)"],
            ['column' => 'area', 'sql' => "CREATE INDEX IF NOT EXISTS idx_properties_area ON properties (area)"],
        ];
        
        // Execute basic indexes
        foreach ($indexes as $sql) {
            try {
                DB::statement($sql);
            } catch (\Exception $e) {
                // Index might already exist or column might not exist
            }
        }
        
        // Execute conditional indexes (only if column exists)
        foreach ($conditionalIndexes as $index) {
            if (Schema::hasColumn('properties', $index['column'])) {
                try {
                    DB::statement($index['sql']);
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        $indexes = [
            'idx_properties_status_available_type',
            'idx_properties_status_available_price',
            'idx_properties_status_available_bedrooms',
            'idx_properties_status_available_bathrooms',
            'idx_properties_status_available_furnishing',
            'idx_properties_status_available_neighborhood',
            'idx_properties_price',
            'idx_properties_bedrooms',
            'idx_properties_bathrooms',
            'idx_properties_area',
            'idx_properties_furnishing_status',
            'idx_properties_type',
            'idx_properties_created_at',
            'idx_properties_updated_at',
            'idx_properties_featured_status_available',
            'idx_properties_featured_until',
            'idx_properties_priority',
            'idx_properties_address',
            'idx_properties_pets_allowed',
            'idx_properties_smoking_allowed',
            'idx_properties_parking_spaces',
            'idx_properties_has_balcony',
            'idx_properties_has_garden',
            'idx_properties_has_pool',
            'idx_properties_has_gym',
            'idx_properties_has_security',
            'idx_properties_has_elevator',
        ];
        
        foreach ($indexes as $indexName) {
            try {
                DB::statement("DROP INDEX IF EXISTS {$indexName}");
            } catch (\Exception $e) {
                // Index might not exist
            }
        }
    }
};
