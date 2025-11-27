<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'title',
        'description',
        'amenities',
        'features',
        'area',
        'furnishing_status',
        'parking_spaces',
        'has_balcony',
        'has_garden',
        'has_pool',
        'has_gym',
        'has_security',
        'has_elevator',
        'has_air_conditioning',
        'has_heating',
        'has_internet',
        'has_cable_tv',
        'pets_allowed',
        'smoking_allowed',
        'price',
        'location',
        'type',
        'bedrooms',
        'bathrooms',
        'rejection_reason',
        'rejected_at',
        'is_available',
        'status',
        'priority',
        'is_featured',
        'featured_until',
        'view_count',
        'latitude',
        'longitude',
        'coordinates',
        'address',
        'neighborhood',
        // Versioning fields
        'version',
        'parent_property_id',
        'version_status',
        'last_approved_at',
        'update_requested_at',
        'update_notes',
        'pending_changes',
        'approved_by',
        'update_requested_by',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'features' => 'array',
            'area' => 'decimal:2',
            'parking_spaces' => 'integer',
            'has_balcony' => 'boolean',
            'has_garden' => 'boolean',
            'has_pool' => 'boolean',
            'has_gym' => 'boolean',
            'has_security' => 'boolean',
            'has_elevator' => 'boolean',
            'has_air_conditioning' => 'boolean',
            'has_heating' => 'boolean',
            'has_internet' => 'boolean',
            'has_cable_tv' => 'boolean',
            'pets_allowed' => 'boolean',
            'smoking_allowed' => 'boolean',
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'featured_until' => 'datetime',
            'view_count' => 'integer',
            'rejected_at' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            // Versioning casts
            'last_approved_at' => 'datetime',
            'update_requested_at' => 'datetime',
            'pending_changes' => 'array',
        ];
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Get the admin who approved this property
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who requested the update
     */
    public function updateRequestedBy()
    {
        return $this->belongsTo(User::class, 'update_requested_by');
    }

    /**
     * Get the parent property (for versioned properties)
     */
    public function parentProperty()
    {
        return $this->belongsTo(Property::class, 'parent_property_id');
    }

    /**
     * Get all versions of this property
     */
    public function versions()
    {
        return $this->hasMany(Property::class, 'parent_property_id')->orderBy('version');
    }

    /**
     * Get the latest approved version of this property
     */
    public function latestApprovedVersion()
    {
        return $this->hasOne(Property::class, 'parent_property_id')
                    ->where('version_status', 'approved_update')
                    ->orderBy('version', 'desc');
    }

    /**
     * Get pending updates for this property
     */
    public function pendingUpdates()
    {
        return $this->hasMany(Property::class, 'parent_property_id')
                    ->where('version_status', 'pending_update');
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(Image::class)->where('is_primary', true);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')
                    ->withPivot(['list_name', 'notes', 'created_at'])
                    ->withTimestamps();
    }

    /**
     * Check if property is favorited by a specific user
     */
    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    /**
     * Get the favorite record for a specific user
     */
    public function getFavoriteForUser($userId)
    {
        return $this->favorites()->where('user_id', $userId)->first();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    // ML-related relationships
    public function propertyEdits()
    {
        return $this->hasMany(PropertyEdit::class);
    }

    public function fraudScore()
    {
        return $this->morphOne(FraudScore::class, 'scoreable')->latest();
    }

    public function fraudScores()
    {
        return $this->morphMany(FraudScore::class, 'scoreable');
    }

    /**
     * Get average rating for this property
     */
    public function getAverageRating()
    {
        return $this->approvedReviews()->avg('property_rating');
    }

    /**
     * Scope for featured properties
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->where(function($q) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>', now());
                    });
    }

    /**
     * Scope for high priority properties
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope for properties ordered by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END")
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Increment view count
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Check if property is currently featured
     */
    public function isCurrentlyFeatured()
    {
        return $this->is_featured && 
               (is_null($this->featured_until) || $this->featured_until > now());
    }

    /**
     * Get review count for this property
     */
    public function getReviewCount()
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get the property amenities (cached proximity data)
     */
    public function propertyAmenities()
    {
        return $this->hasMany(PropertyAmenity::class);
    }

    /**
     * Get nearby amenities for this property
     */
    public function nearbyAmenities()
    {
        return $this->belongsToMany(Amenity::class, 'property_amenities')
            ->withPivot('distance_km', 'walking_time_minutes', 'driving_time_minutes')
            ->withTimestamps()
            ->orderBy('distance_km');
    }

    /**
     * Get blueprints for this property
     */
    public function blueprints()
    {
        return $this->hasMany(Image::class)->where('image_type', 'blueprint');
    }

    /**
     * Get images by type
     */
    public function imagesByType($type)
    {
        return $this->hasMany(Image::class)->where('image_type', $type);
    }

    /**
     * Check if property has coordinates
     */
    public function hasCoordinates()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute()
    {
        if ($this->address) {
            return $this->address;
        }
        
        return $this->location;
    }

    /**
     * Scope for properties with coordinates
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    /**
     * Scope for properties in a specific neighborhood
     */
    public function scopeInNeighborhood($query, $neighborhood)
    {
        return $query->where('neighborhood', 'like', "%{$neighborhood}%");
    }

    /**
     * Scope for original properties (not versions)
     */
    public function scopeOriginal($query)
    {
        return $query->where('version_status', 'original');
    }

    /**
     * Scope for pending updates
     */
    public function scopePendingUpdates($query)
    {
        return $query->where('version_status', 'pending_update');
    }

    /**
     * Scope for approved updates
     */
    public function scopeApprovedUpdates($query)
    {
        return $query->where('version_status', 'approved_update');
    }

    /**
     * Check if this property has pending updates
     */
    public function hasPendingUpdates()
    {
        return $this->pendingUpdates()->exists();
    }

    /**
     * Get the current approved version of this property
     */
    public function getCurrentApprovedVersion()
    {
        if ($this->version_status === 'original') {
            return $this;
        }

        return $this->parentProperty()->with('latestApprovedVersion')->first()?->latestApprovedVersion ?? $this->parentProperty;
    }

    /**
     * Check if this property is the current approved version
     */
    public function isCurrentApprovedVersion()
    {
        if ($this->version_status === 'original') {
            return !$this->hasPendingUpdates();
        }

        return $this->version_status === 'approved_update' && 
               $this->parentProperty && 
               !$this->parentProperty->hasPendingUpdates();
    }

    /**
     * Get the display version of this property (approved version or original)
     */
    public function getDisplayVersion()
    {
        if ($this->version_status === 'original' && !$this->hasPendingUpdates()) {
            return $this;
        }

        if ($this->version_status === 'original' && $this->hasPendingUpdates()) {
            return $this; // Show original while pending updates exist
        }

        if ($this->version_status === 'approved_update') {
            return $this; // Show approved update
        }

        return $this->parentProperty ?? $this;
    }

    /**
     * Create a new version of this property with pending changes
     */
    public function createPendingVersion($changes, $notes = null, $requestedBy = null)
    {
        $newVersion = $this->replicate();
        $newVersion->version = $this->versions()->max('version') + 1;
        $newVersion->parent_property_id = $this->id;
        $newVersion->version_status = 'pending_update';
        $newVersion->update_requested_at = now();
        $newVersion->update_notes = $notes;
        $newVersion->update_requested_by = $requestedBy ?? auth()->id();
        $newVersion->status = 'pending';
        
        // Set array fields to null initially (will be populated from pending_changes if needed)
        $newVersion->amenities = null;
        $newVersion->features = null;
        $newVersion->pending_changes = is_array($changes) ? json_encode($changes) : null;
        
        $newVersion->save();

        return $newVersion;
    }

    /**
     * Approve a pending update
     */
    public function approveUpdate($approvedBy = null)
    {
        if ($this->version_status !== 'pending_update') {
            throw new \Exception('Only pending updates can be approved');
        }

        $this->version_status = 'approved_update';
        $this->last_approved_at = now();
        $this->approved_by = $approvedBy ?? auth()->id();
        $this->status = 'active';
        $this->save();

        // Update the parent property to reflect the changes
        if ($this->parentProperty) {
            $this->parentProperty->update($this->pending_changes ?? []);
        }

        return $this;
    }

    /**
     * Reject a pending update
     */
    public function rejectUpdate($reason = null)
    {
        if ($this->version_status !== 'pending_update') {
            throw new \Exception('Only pending updates can be rejected');
        }

        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        $this->rejected_at = now();
        $this->save();

        return $this;
    }

    // ========================================
    // SPATIAL METHODS FOR MAPS INTEGRATION
    // ========================================

    /**
     * Boot method to handle location_point updates
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($property) {
            if ($property->isDirty('latitude') || $property->isDirty('longitude')) {
                if ($property->latitude && $property->longitude) {
                    // Check if PostGIS is available
                    try {
                        DB::statement("SELECT 1 FROM pg_extension WHERE extname = 'postgis'");
                        // PostGIS available, use spatial column
                        $property->location_point = DB::raw("ST_SetSRID(ST_MakePoint({$property->longitude}, {$property->latitude}), 4326)");
                    } catch (\Exception $e) {
                        // PostGIS not available, use JSON coordinates
                        $property->coordinates = json_encode([
                            'lat' => $property->latitude,
                            'lng' => $property->longitude
                        ]);
                    }
                }
            }
        });
    }

    /**
     * Scope for properties within a radius (fallback without PostGIS)
     */
    public function scopeWithinRadius($query, $latitude, $longitude, $radiusInKm)
    {
        // Fallback method using Haversine formula
        $earthRadius = 6371; // Earth's radius in kilometers
        
        return $query->whereRaw("
            (? * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + 
            sin(radians(?)) * sin(radians(latitude)))) <= ?
        ", [$earthRadius, $latitude, $longitude, $latitude, $radiusInKm]);
    }

    /**
     * Scope for properties within a bounding box (fallback without PostGIS)
     */
    public function scopeWithinBounds($query, $north, $south, $east, $west)
    {
        return $query->whereBetween('latitude', [$south, $north])
                    ->whereBetween('longitude', [$west, $east]);
    }

    /**
     * Scope for properties within a polygon (fallback without PostGIS)
     */
    public function scopeWithinPolygon($query, $polygonCoordinates)
    {
        // Simple bounding box fallback for polygon search
        $lats = array_column($polygonCoordinates, 1);
        $lngs = array_column($polygonCoordinates, 0);
        
        return $query->whereBetween('latitude', [min($lats), max($lats)])
                    ->whereBetween('longitude', [min($lngs), max($lngs)]);
    }

    /**
     * Scope for ordering by distance from a point (fallback without PostGIS)
     */
    public function scopeOrderByDistance($query, $latitude, $longitude)
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        return $query->orderByRaw("
            ? * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + 
            sin(radians(?)) * sin(radians(latitude)))
        ", [$earthRadius, $latitude, $longitude, $latitude]);
    }

    /**
     * Get distance from a specific point in kilometers (fallback without PostGIS)
     */
    public function getDistanceFrom($latitude, $longitude)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        // Haversine formula
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $dLat = deg2rad($this->latitude - $latitude);
        $dLng = deg2rad($this->longitude - $longitude);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($latitude)) * cos(deg2rad($this->latitude)) *
             sin($dLng / 2) * sin($dLng / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return round($earthRadius * $c, 2);
    }

    /**
     * Check if property has valid coordinates
     */
    public function hasValidCoordinates()
    {
        return $this->latitude && $this->longitude && 
               $this->latitude >= -2.9 && $this->latitude <= 0.0 && // Rwanda bounds
               $this->longitude >= 28.8 && $this->longitude <= 30.9;
    }

    /**
     * Get properties as GeoJSON format
     */
    public static function getAsGeoJSON($properties = null)
    {
        $properties = $properties ?: static::where('status', 'active')->get();

        $features = $properties->map(function ($property) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $property->id,
                    'title' => $property->title,
                    'price' => $property->price,
                    'type' => $property->type,
                    'bedrooms' => $property->bedrooms,
                    'bathrooms' => $property->bathrooms,
                    'area' => $property->area,
                    'neighborhood' => $property->neighborhood,
                    'location' => $property->location,
                    'image' => $property->primaryImage?->url ?? null,
                    'url' => route('properties.public.show', $property),
                    'is_featured' => $property->is_featured,
                    'furnishing_status' => $property->furnishing_status,
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$property->longitude, $property->latitude]
                ]
            ];
        });

        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }

    /**
     * Geocode an address to get coordinates
     */
    public static function geocodeAddress($address)
    {
        $cacheKey = 'geocode_' . md5($address);
        
        return Cache::remember($cacheKey, 86400, function () use ($address) { // Cache for 24 hours
            $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
                'q' => $address . ', Rwanda',
                'format' => 'json',
                'limit' => 1,
                'addressdetails' => 1
            ]);

            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Murugo-Real-Estate/1.0\r\n"
                ]
            ]);

            $response = file_get_contents($url, false, $context);
            $data = json_decode($response, true);

            if (!empty($data)) {
                return [
                    'latitude' => floatval($data[0]['lat']),
                    'longitude' => floatval($data[0]['lon']),
                    'address' => $data[0]['display_name']
                ];
            }

            return null;
        });
    }

    /**
     * Reverse geocode coordinates to get address
     */
    public static function reverseGeocode($latitude, $longitude)
    {
        $cacheKey = 'reverse_geocode_' . md5($latitude . ',' . $longitude);
        
        return Cache::remember($cacheKey, 86400, function () use ($latitude, $longitude) {
            $url = 'https://nominatim.openstreetmap.org/reverse?' . http_build_query([
                'lat' => $latitude,
                'lon' => $longitude,
                'format' => 'json',
                'addressdetails' => 1
            ]);

            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Murugo-Real-Estate/1.0\r\n"
                ]
            ]);

            $response = file_get_contents($url, false, $context);
            $data = json_decode($response, true);

            if ($data && !isset($data['error'])) {
                return [
                    'address' => $data['display_name'],
                    'neighborhood' => $data['address']['suburb'] ?? $data['address']['neighbourhood'] ?? null,
                    'city' => $data['address']['city'] ?? $data['address']['town'] ?? null,
                    'district' => $data['address']['county'] ?? null
                ];
            }

            return null;
        });
    }

    /**
     * Get nearby properties within a radius
     */
    public function getNearbyProperties($radiusKm = 5, $limit = 10)
    {
        if (!$this->hasValidCoordinates()) {
            return collect();
        }

        return static::where('id', '!=', $this->id)
            ->where('status', 'active')
            ->withinRadius($this->latitude, $this->longitude, $radiusKm)
            ->orderByDistance($this->latitude, $this->longitude)
            ->limit($limit)
            ->get();
    }

    /**
     * Validate coordinates are within Rwanda bounds
     */
    public static function validateCoordinates($latitude, $longitude)
    {
        return $latitude >= -2.9 && $latitude <= 0.0 && 
               $longitude >= 28.8 && $longitude <= 30.9;
    }
}