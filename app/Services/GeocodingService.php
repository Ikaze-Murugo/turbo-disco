<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Convert address to coordinates using Google Geocoding API
     */
    public function geocodeAddress($address)
    {
        $apiKey = config('services.google.maps_api_key');
        
        if (!$apiKey) {
            Log::warning('Google Maps API key not configured');
            return null;
        }
        
        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $apiKey,
                'region' => 'rw' // Bias results towards Rwanda
            ]);
            
            $data = $response->json();
            
            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];
                
                return [
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                    'formatted_address' => $data['results'][0]['formatted_address'],
                    'place_id' => $data['results'][0]['place_id'] ?? null
                ];
            }
            
            Log::warning('Geocoding failed', [
                'address' => $address,
                'status' => $data['status'] ?? 'unknown',
                'error_message' => $data['error_message'] ?? null
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Geocoding service error', [
                'address' => $address,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Convert coordinates to address (reverse geocoding)
     */
    public function reverseGeocode($latitude, $longitude)
    {
        $apiKey = config('services.google.maps_api_key');
        
        if (!$apiKey) {
            Log::warning('Google Maps API key not configured');
            return null;
        }
        
        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$latitude},{$longitude}",
                'key' => $apiKey,
                'region' => 'rw'
            ]);
            
            $data = $response->json();
            
            if ($data['status'] === 'OK' && !empty($data['results'])) {
                return [
                    'formatted_address' => $data['results'][0]['formatted_address'],
                    'place_id' => $data['results'][0]['place_id'] ?? null,
                    'address_components' => $data['results'][0]['address_components'] ?? []
                ];
            }
            
            Log::warning('Reverse geocoding failed', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'status' => $data['status'] ?? 'unknown',
                'error_message' => $data['error_message'] ?? null
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Reverse geocoding service error', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Geocode address with fallback to manual coordinate entry
     */
    public function geocodeWithFallback($address, $latitude = null, $longitude = null)
    {
        // If coordinates are provided, use them
        if ($latitude && $longitude) {
            $locationService = new LocationService();
            if ($locationService->isValidCoordinates($latitude, $longitude)) {
                return [
                    'latitude' => (float) $latitude,
                    'longitude' => (float) $longitude,
                    'formatted_address' => $address,
                    'source' => 'manual'
                ];
            }
        }
        
        // Try geocoding the address
        $geocoded = $this->geocodeAddress($address);
        
        if ($geocoded) {
            $geocoded['source'] = 'geocoded';
            return $geocoded;
        }
        
        // If geocoding fails, return null
        return null;
    }
    
    /**
     * Validate and clean address
     */
    public function cleanAddress($address)
    {
        // Remove extra whitespace
        $address = trim(preg_replace('/\s+/', ' ', $address));
        
        // Add Rwanda if not present
        if (!stripos($address, 'rwanda') && !stripos($address, 'rw')) {
            $address .= ', Rwanda';
        }
        
        return $address;
    }
    
    /**
     * Get place details from Google Places API
     */
    public function getPlaceDetails($placeId)
    {
        $apiKey = config('services.google.maps_api_key');
        
        if (!$apiKey) {
            return null;
        }
        
        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                'place_id' => $placeId,
                'key' => $apiKey,
                'fields' => 'name,formatted_address,geometry,place_id,types'
            ]);
            
            $data = $response->json();
            
            if ($data['status'] === 'OK' && !empty($data['result'])) {
                return $data['result'];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Place details service error', [
                'place_id' => $placeId,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
}
