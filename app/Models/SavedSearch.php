<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'search_query',
        'filters',
        'is_active',
        'last_searched_at',
        'search_count',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'is_active' => 'boolean',
            'last_searched_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Increment search count and update last searched timestamp
     */
    public function incrementSearchCount()
    {
        $this->increment('search_count');
        $this->update(['last_searched_at' => now()]);
    }

    /**
     * Get search results based on saved filters
     */
    public function getSearchResults()
    {
        $query = Property::where('status', 'active');

        // Apply filters
        if ($this->filters) {
            $query = $this->applyFilters($query, $this->filters);
        }

        // Apply search query
        if ($this->search_query) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search_query}%")
                  ->orWhere('description', 'like', "%{$this->search_query}%")
                  ->orWhere('location', 'like', "%{$this->search_query}%");
            });
        }

        return $query->with(['images', 'landlord'])->get();
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, $filters)
    {
        foreach ($filters as $key => $value) {
            if (empty($value)) continue;

            switch ($key) {
                case 'type':
                    $query->where('type', $value);
                    break;
                case 'price_min':
                    $query->where('price', '>=', $value);
                    break;
                case 'price_max':
                    $query->where('price', '<=', $value);
                    break;
                case 'bedrooms':
                    $query->where('bedrooms', '>=', $value);
                    break;
                case 'bathrooms':
                    $query->where('bathrooms', '>=', $value);
                    break;
                case 'location':
                    $query->where('location', 'like', "%{$value}%");
                    break;
                case 'amenities':
                    if (is_array($value)) {
                        foreach ($value as $amenity) {
                            $query->where("has_{$amenity}", true);
                        }
                    }
                    break;
            }
        }

        return $query;
    }
}