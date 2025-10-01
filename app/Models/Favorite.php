<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'property_id',
        'list_name',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get all wishlists for a user
     */
    public static function getUserWishlists($userId)
    {
        return static::where('user_id', $userId)
                    ->select('list_name')
                    ->distinct()
                    ->pluck('list_name');
    }

    /**
     * Get properties in a specific wishlist
     */
    public static function getWishlistProperties($userId, $listName = 'default')
    {
        return static::where('user_id', $userId)
                    ->where('list_name', $listName)
                    ->with(['property.images', 'property.landlord'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
}