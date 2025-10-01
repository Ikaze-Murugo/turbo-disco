<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use App\Services\LocationService;

class CachePropertyProximity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:cache-proximity 
                            {--property= : Specific property ID to cache}
                            {--radius=5 : Search radius in kilometers}
                            {--force : Force recache even if data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache proximity data for properties and nearby amenities';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $propertyId = $this->option('property');
        $radius = (float) $this->option('radius');
        $force = $this->option('force');

        $this->info("Starting proximity caching process...");
        $this->info("Search radius: {$radius} km");
        
        if ($force) {
            $this->warn("Force mode enabled - will recache all data");
        }

        $locationService = new LocationService();
        $processed = 0;
        $skipped = 0;
        $errors = 0;

        if ($propertyId) {
            // Process specific property
            $property = Property::find($propertyId);
            if (!$property) {
                $this->error("Property with ID {$propertyId} not found.");
                return 1;
            }

            $this->processProperty($property, $locationService, $radius, $force);
        } else {
            // Process all properties with coordinates
            $properties = Property::whereNotNull('latitude')
                                ->whereNotNull('longitude')
                                ->get();

            $this->info("Found {$properties->count()} properties with coordinates");

            $progressBar = $this->output->createProgressBar($properties->count());
            $progressBar->start();

            foreach ($properties as $property) {
                try {
                    $result = $this->processProperty($property, $locationService, $radius, $force, false);
                    if ($result === 'processed') {
                        $processed++;
                    } elseif ($result === 'skipped') {
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("\nError processing property {$property->id}: " . $e->getMessage());
                }
                
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine();
        }

        // Summary
        $this->info("\n=== Proximity Caching Complete ===");
        $this->info("Properties processed: {$processed}");
        $this->info("Properties skipped: {$skipped}");
        $this->info("Errors: {$errors}");

        return 0;
    }

    /**
     * Process a single property for proximity caching
     */
    private function processProperty(Property $property, LocationService $locationService, float $radius, bool $force, bool $verbose = true): string
    {
        if ($verbose) {
            $this->info("Processing property: {$property->title} (ID: {$property->id})");
        }

        // Check if data already exists
        if (!$force && $property->propertyAmenities()->count() > 0) {
            if ($verbose) {
                $this->warn("  → Skipping (data already exists, use --force to recache)");
            }
            return 'skipped';
        }

        try {
            // Clear existing data if force mode
            if ($force) {
                $property->propertyAmenities()->delete();
                if ($verbose) {
                    $this->info("  → Cleared existing proximity data");
                }
            }

            // Cache proximity data
            $locationService->cachePropertyAmenities($property, $radius);
            
            $amenityCount = $property->propertyAmenities()->count();
            
            if ($verbose) {
                $this->info("  → Cached {$amenityCount} nearby amenities");
            }

            return 'processed';
        } catch (\Exception $e) {
            if ($verbose) {
                $this->error("  → Error: " . $e->getMessage());
            }
            throw $e;
        }
    }
}