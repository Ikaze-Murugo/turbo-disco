<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    protected $fillable = [
        'property_id',
        'filename',
        'path',
        'alt_text',
        'is_primary',
        'sort_order',
        'image_type',
        'image_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'image_order' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Scope to get images by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('image_type', $type);
    }

    /**
     * Scope to get primary images
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope to get blueprints
     */
    public function scopeBlueprints($query)
    {
        return $query->where('image_type', 'blueprint');
    }

    /**
     * Scope to get regular property images (not blueprints)
     */
    public function scopePropertyImages($query)
    {
        return $query->where('image_type', '!=', 'blueprint');
    }

    /**
     * Get the full URL for the image
     */
    public function getUrlAttribute()
    {
        if ($this->path) {
            return asset('storage/' . $this->path);
        }
        
        return null;
    }

    /**
     * Check if this is a blueprint
     */
    public function isBlueprint()
    {
        return $this->image_type === 'blueprint';
    }

    /**
     * Get the image type in a human-readable format
     */
    public function getTypeFormattedAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->image_type ?? 'image'));
    }
}