<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'property_id',
        'landlord_id',
        'property_rating',
        'landlord_rating',
        'property_review',
        'landlord_review',
        'is_approved',
        'is_anonymous',
    ];

    protected function casts(): array
    {
        return [
            'property_rating' => 'integer',
            'landlord_rating' => 'integer',
            'is_approved' => 'boolean',
            'is_anonymous' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Get approved reviews for a property
     */
    public static function getApprovedReviewsForProperty($propertyId)
    {
        return static::where('property_id', $propertyId)
                    ->where('is_approved', true)
                    ->with(['user'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get approved reviews for a landlord
     */
    public static function getApprovedReviewsForLandlord($landlordId)
    {
        return static::where('landlord_id', $landlordId)
                    ->where('is_approved', true)
                    ->with(['user', 'property'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get average rating for a property
     */
    public static function getAveragePropertyRating($propertyId)
    {
        return static::where('property_id', $propertyId)
                    ->where('is_approved', true)
                    ->avg('property_rating');
    }

    /**
     * Get average rating for a landlord
     */
    public static function getAverageLandlordRating($landlordId)
    {
        return static::where('landlord_id', $landlordId)
                    ->where('is_approved', true)
                    ->avg('landlord_rating');
    }

    /**
     * Get review count for a property
     */
    public static function getPropertyReviewCount($propertyId)
    {
        return static::where('property_id', $propertyId)
                    ->where('is_approved', true)
                    ->count();
    }

    /**
     * Get review count for a landlord
     */
    public static function getLandlordReviewCount($landlordId)
    {
        return static::where('landlord_id', $landlordId)
                    ->where('is_approved', true)
                    ->count();
    }

    /**
     * Check if user has reviewed a property
     */
    public static function hasUserReviewedProperty($userId, $propertyId)
    {
        return static::where('user_id', $userId)
                    ->where('property_id', $propertyId)
                    ->exists();
    }
}