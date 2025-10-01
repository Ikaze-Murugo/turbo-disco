<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'name',
        'property_ids',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'property_ids' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the properties being compared
     */
    public function properties()
    {
        return Property::whereIn('id', $this->property_ids)
            ->where('status', 'active')
            ->with(['images', 'landlord'])
            ->get();
    }

    /**
     * Add a property to comparison
     */
    public function addProperty($propertyId)
    {
        $propertyIds = $this->property_ids ?? [];
        
        if (!in_array($propertyId, $propertyIds) && count($propertyIds) < 4) {
            $propertyIds[] = $propertyId;
            $this->update(['property_ids' => json_encode($propertyIds)]);
        }
    }

    /**
     * Remove a property from comparison
     */
    public function removeProperty($propertyId)
    {
        $propertyIds = $this->property_ids ?? [];
        $propertyIds = array_filter($propertyIds, fn($id) => $id != $propertyId);
        $this->update(['property_ids' => json_encode(array_values($propertyIds))]);
    }

    /**
     * Check if property is in comparison
     */
    public function hasProperty($propertyId)
    {
        return in_array($propertyId, $this->property_ids ?? []);
    }

    /**
     * Get comparison count
     */
    public function getCount()
    {
        return count($this->property_ids ?? []);
    }

    /**
     * Check if comparison is full
     */
    public function isFull()
    {
        return $this->getCount() >= 4;
    }

    /**
     * Clean up expired comparisons
     */
    public static function cleanupExpired()
    {
        static::where('expires_at', '<', now())->delete();
    }

    /**
     * Get or create comparison for user/session
     */
    public static function getOrCreate($userId = null, $sessionId = null)
    {
        $query = static::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        $comparison = $query->first();

        if (!$comparison) {
            $comparison = static::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'property_ids' => json_encode([]), // Convert to JSON string for SQLite
                'expires_at' => $userId ? null : now()->addDays(7), // 7 days for guests
            ]);
        }

        return $comparison;
    }
}