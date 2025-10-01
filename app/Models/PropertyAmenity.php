<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyAmenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'amenity_id',
        'distance_km',
        'walking_time_minutes',
        'driving_time_minutes'
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'walking_time_minutes' => 'integer',
        'driving_time_minutes' => 'integer'
    ];

    /**
     * Get the property that owns this amenity relationship
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the amenity that is related to this property
     */
    public function amenity()
    {
        return $this->belongsTo(Amenity::class);
    }

    /**
     * Scope to get amenities within a specific distance
     */
    public function scopeWithinDistance($query, $maxDistanceKm)
    {
        return $query->where('distance_km', '<=', $maxDistanceKm);
    }

    /**
     * Scope to get amenities by type
     */
    public function scopeByAmenityType($query, $type)
    {
        return $query->whereHas('amenity', function ($q) use ($type) {
            $q->where('type', $type);
        });
    }

    /**
     * Scope to get amenities within walking distance (e.g., 15 minutes)
     */
    public function scopeWithinWalkingDistance($query, $maxMinutes = 15)
    {
        return $query->where('walking_time_minutes', '<=', $maxMinutes);
    }

    /**
     * Get formatted distance string
     */
    public function getFormattedDistanceAttribute()
    {
        if ($this->distance_km < 1) {
            return round($this->distance_km * 1000) . 'm';
        }
        
        return round($this->distance_km, 1) . 'km';
    }

    /**
     * Get formatted walking time string
     */
    public function getFormattedWalkingTimeAttribute()
    {
        if ($this->walking_time_minutes < 60) {
            return $this->walking_time_minutes . ' min walk';
        }
        
        $hours = floor($this->walking_time_minutes / 60);
        $minutes = $this->walking_time_minutes % 60;
        
        if ($minutes > 0) {
            return $hours . 'h ' . $minutes . 'm walk';
        }
        
        return $hours . 'h walk';
    }

    /**
     * Get formatted driving time string
     */
    public function getFormattedDrivingTimeAttribute()
    {
        if ($this->driving_time_minutes < 60) {
            return $this->driving_time_minutes . ' min drive';
        }
        
        $hours = floor($this->driving_time_minutes / 60);
        $minutes = $this->driving_time_minutes % 60;
        
        if ($minutes > 0) {
            return $hours . 'h ' . $minutes . 'm drive';
        }
        
        return $hours . 'h drive';
    }
}
