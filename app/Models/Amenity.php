<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'address',
        'latitude',
        'longitude',
        'phone',
        'website',
        'rating',
        'is_active'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get the property amenities that reference this amenity
     */
    public function propertyAmenities()
    {
        return $this->hasMany(PropertyAmenity::class);
    }

    /**
     * Get properties that are near this amenity
     */
    public function nearbyProperties()
    {
        return $this->belongsToMany(Property::class, 'property_amenities')
            ->withPivot('distance_km', 'walking_time_minutes', 'driving_time_minutes')
            ->withTimestamps();
    }

    /**
     * Scope to get active amenities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get amenities by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get amenities within a radius
     */
    public function scopeWithinRadius($query, $latitude, $longitude, $radiusKm = 5)
    {
        // This is a simplified version - in production, you might want to use
        // a more sophisticated spatial query
        return $query->whereRaw(
            "ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?",
            [$longitude, $latitude, $radiusKm * 1000] // Convert km to meters
        );
    }

    /**
     * Get the amenity type in a human-readable format
     */
    public function getTypeFormattedAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }

    /**
     * Get the distance to a specific point
     */
    public function distanceTo($latitude, $longitude)
    {
        $locationService = app(LocationService::class);
        return $locationService->calculateDistance(
            $this->latitude,
            $this->longitude,
            $latitude,
            $longitude
        );
    }
}
