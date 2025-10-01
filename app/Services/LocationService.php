<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Amenity;
use App\Models\PropertyAmenity;

class LocationService
{
    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    public function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Find nearby amenities for a property within specified radius
     */
    public function findNearbyAmenities(Property $property, $radiusKm = 5)
    {
        $amenities = Amenity::where('is_active', true)->get();
        $nearbyAmenities = [];
        
        foreach ($amenities as $amenity) {
            $distance = $this->calculateDistance(
                $property->latitude,
                $property->longitude,
                $amenity->latitude,
                $amenity->longitude
            );
            
            if ($distance <= $radiusKm) {
                $nearbyAmenities[] = [
                    'amenity' => $amenity,
                    'distance' => round($distance, 2),
                    'walking_time' => $this->estimateWalkingTime($distance),
                    'driving_time' => $this->estimateDrivingTime($distance)
                ];
            }
        }
        
        // Sort by distance (closest first)
        usort($nearbyAmenities, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        return collect($nearbyAmenities);
    }
    
    /**
     * Cache proximity data for a property
     * This stores the calculated distances in the database for performance
     */
    public function cachePropertyAmenities(Property $property)
    {
        // Clear existing cache for this property
        PropertyAmenity::where('property_id', $property->id)->delete();
        
        $nearbyAmenities = $this->findNearbyAmenities($property);
        
        foreach ($nearbyAmenities as $item) {
            PropertyAmenity::create([
                'property_id' => $property->id,
                'amenity_id' => $item['amenity']->id,
                'distance_km' => $item['distance'],
                'walking_time_minutes' => $item['walking_time'],
                'driving_time_minutes' => $item['driving_time']
            ]);
        }
        
        return $nearbyAmenities;
    }
    
    /**
     * Get cached proximity data for a property
     */
    public function getCachedPropertyAmenities(Property $property)
    {
        return PropertyAmenity::where('property_id', $property->id)
            ->with('amenity')
            ->orderBy('distance_km')
            ->get();
    }
    
    /**
     * Estimate walking time in minutes
     * Assumes average walking speed of 5 km/h
     */
    private function estimateWalkingTime($distanceKm)
    {
        return round(($distanceKm / 5) * 60); // Convert to minutes
    }
    
    /**
     * Estimate driving time in minutes
     * Assumes average driving speed of 30 km/h in city traffic
     */
    private function estimateDrivingTime($distanceKm)
    {
        return round(($distanceKm / 30) * 60); // Convert to minutes
    }
    
    /**
     * Find properties within a radius of given coordinates
     */
    public function findPropertiesNearby($latitude, $longitude, $radiusKm = 5)
    {
        $properties = Property::where('status', 'active')->get();
        $nearbyProperties = [];
        
        foreach ($properties as $property) {
            if ($property->latitude && $property->longitude) {
                $distance = $this->calculateDistance(
                    $latitude,
                    $longitude,
                    $property->latitude,
                    $property->longitude
                );
                
                if ($distance <= $radiusKm) {
                    $nearbyProperties[] = [
                        'property' => $property,
                        'distance' => round($distance, 2)
                    ];
                }
            }
        }
        
        // Sort by distance
        usort($nearbyProperties, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        return collect($nearbyProperties);
    }
    
    /**
     * Validate coordinates
     */
    public function isValidCoordinates($latitude, $longitude)
    {
        return $latitude >= -90 && $latitude <= 90 && 
               $longitude >= -180 && $longitude <= 180;
    }
    
    /**
     * Check if coordinates are within Rwanda bounds
     * Rwanda approximate bounds: Lat: -2.8 to -1.0, Lng: 28.8 to 30.9
     */
    public function isWithinRwanda($latitude, $longitude)
    {
        return $latitude >= -2.8 && $latitude <= -1.0 && 
               $longitude >= 28.8 && $longitude <= 30.9;
    }
}
