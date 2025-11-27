<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyEdit extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'field_name',
        'old_value',
        'new_value',
        'edit_type',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the property that was edited.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user who made the edit.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get edits for a specific property.
     */
    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    /**
     * Scope to get edits by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get recent edits.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get edits of a specific field.
     */
    public function scopeOfField($query, $fieldName)
    {
        return $query->where('field_name', $fieldName);
    }
}
