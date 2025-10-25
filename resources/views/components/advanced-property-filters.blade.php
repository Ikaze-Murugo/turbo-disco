{{-- Advanced Property Filters Component --}}
@props([
    'filterOptions' => [],
    'currentFilters' => [],
    'class' => ''
])

<div class="advanced-filters {{ $class }}" x-data="advancedFilters()">
    <!-- Filter Toggle Button (Mobile) -->
    <div class="lg:hidden mb-4">
        <button @click="showFilters = !showFilters" 
                class="w-full btn btn-outline flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
            </svg>
            Advanced Filters
            <span x-show="activeFiltersCount > 0" 
                  class="bg-blue-500 text-white text-xs rounded-full px-2 py-1"
                  x-text="activeFiltersCount"></span>
        </button>
    </div>

    <!-- Filters Panel -->
    <div class="filters-panel" 
         :class="{ 'hidden': !showFilters }"
         x-show="showFilters"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95">
        
        <form method="GET" action="{{ request()->url() }}" class="space-y-6">
            <!-- Search Query -->
            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" 
                       name="search" 
                       placeholder="Search properties, locations, amenities..."
                       class="filter-input"
                       value="{{ request('search') }}"
                       x-model="filters.search">
            </div>

            <!-- Location Filters -->
            <div class="filter-group">
                <label class="filter-label">Location</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="filter-sublabel">Neighborhood</label>
                        <select name="location" class="filter-select" x-model="filters.location">
                            <option value="">Any Neighborhood</option>
                            @foreach($filterOptions['neighborhoods'] ?? [] as $neighborhood)
                                <option value="{{ $neighborhood }}" 
                                        {{ request('location') == $neighborhood ? 'selected' : '' }}>
                                    {{ $neighborhood }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="filter-sublabel">City</label>
                        <input type="text" 
                               name="city" 
                               placeholder="Enter city"
                               class="filter-input"
                               value="{{ request('city') }}"
                               x-model="filters.city">
                    </div>
                </div>
            </div>

            <!-- Property Type & Purpose -->
            <div class="filter-group">
                <label class="filter-label">Property Details</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="filter-sublabel">Property Type</label>
                        <select name="type" class="filter-select" x-model="filters.type">
                            <option value="">Any Type</option>
                            @foreach($filterOptions['types'] ?? [] as $type)
                                <option value="{{ $type }}" 
                                        {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="filter-sublabel">Furnishing</label>
                        <select name="furnishing_status" class="filter-select" x-model="filters.furnishing_status">
                            <option value="">Any Furnishing</option>
                            @foreach($filterOptions['furnishing_statuses'] ?? [] as $status)
                                <option value="{{ $status }}" 
                                        {{ request('furnishing_status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Price Range -->
            <div class="filter-group">
                <label class="filter-label">Price Range (RWF)</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="filter-sublabel">Minimum Price</label>
                        <input type="number" 
                               name="min_price" 
                               placeholder="0"
                               class="filter-input"
                               value="{{ request('min_price') }}"
                               x-model="filters.min_price">
                    </div>
                    <div>
                        <label class="filter-sublabel">Maximum Price</label>
                        <input type="number" 
                               name="max_price" 
                               placeholder="{{ number_format($filterOptions['price_range']['max'] ?? 10000000) }}"
                               class="filter-input"
                               value="{{ request('max_price') }}"
                               x-model="filters.max_price">
                    </div>
                </div>
                <!-- Price Range Slider -->
                <div class="mt-4">
                    <div class="price-slider" 
                         x-data="priceSlider({{ $filterOptions['price_range']['min'] ?? 0 }}, {{ $filterOptions['price_range']['max'] ?? 10000000 }})"
                         x-init="init()">
                        <input type="range" 
                               x-model="filters.min_price" 
                               :min="min" 
                               :max="max" 
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                        <div class="flex justify-between text-sm text-gray-600 mt-2">
                            <span x-text="formatPrice(filters.min_price)"></span>
                            <span x-text="formatPrice(filters.max_price)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property Features -->
            <div class="filter-group">
                <label class="filter-label">Property Features</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="filter-sublabel">Bedrooms</label>
                        <select name="bedrooms" class="filter-select" x-model="filters.bedrooms">
                            <option value="">Any</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('bedrooms') == $i ? 'selected' : '' }}>
                                    {{ $i }}+ Bedrooms
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="filter-sublabel">Bathrooms</label>
                        <select name="bathrooms" class="filter-select" x-model="filters.bathrooms">
                            <option value="">Any</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('bathrooms') == $i ? 'selected' : '' }}>
                                    {{ $i }}+ Bathrooms
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="filter-sublabel">Parking Spaces</label>
                        <select name="parking_spaces" class="filter-select" x-model="filters.parking_spaces">
                            <option value="">Any</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('parking_spaces') == $i ? 'selected' : '' }}>
                                    {{ $i }}+ Spaces
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <!-- Area Range -->
            <div class="filter-group">
                <label class="filter-label">Area Range (m²)</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="filter-sublabel">Minimum Area</label>
                        <input type="number" 
                               name="min_area" 
                               placeholder="0"
                               class="filter-input"
                               value="{{ request('min_area') }}"
                               x-model="filters.min_area">
                    </div>
                    <div>
                        <label class="filter-sublabel">Maximum Area</label>
                        <input type="number" 
                               name="max_area" 
                               placeholder="{{ $filterOptions['area_range']['max'] ?? 1000 }}"
                               class="filter-input"
                               value="{{ request('max_area') }}"
                               x-model="filters.max_area">
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <div class="filter-group">
                <label class="filter-label">Property Amenities</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $amenityFlags = [
                            'has_balcony' => 'Balcony',
                            'has_garden' => 'Garden',
                            'has_pool' => 'Swimming Pool',
                            'has_gym' => 'Gym',
                            'has_security' => 'Security',
                            'has_elevator' => 'Elevator',
                            'has_air_conditioning' => 'Air Conditioning',
                            'has_heating' => 'Heating',
                            'has_internet' => 'Internet',
                            'has_cable_tv' => 'Cable TV'
                        ];
                    @endphp
                    
                    @foreach($amenityFlags as $flag => $label)
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" 
                                   name="{{ $flag }}" 
                                   value="1"
                                   class="filter-checkbox"
                                   {{ request($flag) ? 'checked' : '' }}
                                   x-model="filters.{{ $flag }}">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Policies -->
            <div class="filter-group">
                <label class="filter-label">Policies</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" 
                               name="pets_allowed" 
                               value="1"
                               class="filter-checkbox"
                               {{ request('pets_allowed') ? 'checked' : '' }}
                               x-model="filters.pets_allowed">
                        <span class="text-sm text-gray-700">Pets Allowed</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" 
                               name="smoking_allowed" 
                               value="1"
                               class="filter-checkbox"
                               {{ request('smoking_allowed') ? 'checked' : '' }}
                               x-model="filters.smoking_allowed">
                        <span class="text-sm text-gray-700">Smoking Allowed</span>
                    </label>
                </div>
            </div>

            <!-- Nearby Amenities -->
            <div class="filter-group">
                <label class="filter-label">Nearby Amenities</label>
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="filter-sublabel">Amenity Type</label>
                            <select name="nearby_amenities[]" 
                                    class="filter-select" 
                                    multiple
                                    x-model="filters.nearby_amenities">
                                @foreach($filterOptions['amenities'] ?? [] as $amenity)
                                    <option value="{{ $amenity->id }}">
                                        {{ $amenity->name }} ({{ ucfirst($amenity->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="filter-sublabel">Maximum Distance (km)</label>
                            <select name="max_distance" class="filter-select" x-model="filters.max_distance">
                                <option value="">Any Distance</option>
                                <option value="0.5">Within 0.5 km</option>
                                <option value="1">Within 1 km</option>
                                <option value="2">Within 2 km</option>
                                <option value="5">Within 5 km</option>
                                <option value="10">Within 10 km</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Special Filters -->
            <div class="filter-group">
                <label class="filter-label">Special Filters</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" 
                               name="featured_only" 
                               value="1"
                               class="filter-checkbox"
                               {{ request('featured_only') ? 'checked' : '' }}
                               x-model="filters.featured_only">
                        <span class="text-sm text-gray-700">Featured Properties Only</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" 
                               name="new_only" 
                               value="1"
                               class="filter-checkbox"
                               {{ request('new_only') ? 'checked' : '' }}
                               x-model="filters.new_only">
                        <span class="text-sm text-gray-700">New Properties Only</span>
                    </label>
                </div>
            </div>

            <!-- Sort Options -->
            <div class="filter-group">
                <label class="filter-label">Sort By</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="filter-sublabel">Sort Order</label>
                        <select name="sort" class="filter-select" x-model="filters.sort">
                            <option value="priority">Recommended</option>
                            <option value="price">Price</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="bedrooms">Most Bedrooms</option>
                            <option value="bathrooms">Most Bathrooms</option>
                            <option value="area">Largest Area</option>
                            <option value="views">Most Viewed</option>
                        </select>
                    </div>
                    <div>
                        <label class="filter-sublabel">Order</label>
                        <select name="order" class="filter-select" x-model="filters.order">
                            <option value="desc">Descending</option>
                            <option value="asc">Ascending</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="filter-actions">
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="btn btn-primary flex-1">
                        Apply Filters
                    </button>
                    <button type="button" 
                            @click="clearFilters()" 
                            class="btn btn-outline flex-1">
                        Clear All
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function advancedFilters() {
    return {
        showFilters: false,
        filters: {
            search: '{{ request("search") }}',
            location: '{{ request("location") }}',
            city: '{{ request("city") }}',
            type: '{{ request("type") }}',
            furnishing_status: '{{ request("furnishing_status") }}',
            min_price: '{{ request("min_price") }}',
            max_price: '{{ request("max_price") }}',
            bedrooms: '{{ request("bedrooms") }}',
            bathrooms: '{{ request("bathrooms") }}',
            parking_spaces: '{{ request("parking_spaces") }}',
            min_area: '{{ request("min_area") }}',
            max_area: '{{ request("max_area") }}',
            has_balcony: {{ request("has_balcony") ? "true" : "false" }},
            has_garden: {{ request("has_garden") ? "true" : "false" }},
            has_pool: {{ request("has_pool") ? "true" : "false" }},
            has_gym: {{ request("has_gym") ? "true" : "false" }},
            has_security: {{ request("has_security") ? "true" : "false" }},
            has_elevator: {{ request("has_elevator") ? "true" : "false" }},
            has_air_conditioning: {{ request("has_air_conditioning") ? "true" : "false" }},
            has_heating: {{ request("has_heating") ? "true" : "false" }},
            has_internet: {{ request("has_internet") ? "true" : "false" }},
            has_cable_tv: {{ request("has_cable_tv") ? "true" : "false" }},
            pets_allowed: {{ request("pets_allowed") ? "true" : "false" }},
            smoking_allowed: {{ request("smoking_allowed") ? "true" : "false" }},
            nearby_amenities: [],
            max_distance: '{{ request("max_distance") }}',
            featured_only: {{ request("featured_only") ? "true" : "false" }},
            new_only: {{ request("new_only") ? "true" : "false" }},
            sort: '{{ request("sort", "priority") }}',
            order: '{{ request("order", "desc") }}'
        },
        
        get activeFiltersCount() {
            let count = 0;
            Object.keys(this.filters).forEach(key => {
                if (this.filters[key] && this.filters[key] !== '' && this.filters[key] !== false) {
                    count++;
                }
            });
            return count;
        },
        
        clearFilters() {
            Object.keys(this.filters).forEach(key => {
                if (typeof this.filters[key] === 'boolean') {
                    this.filters[key] = false;
                } else if (Array.isArray(this.filters[key])) {
                    this.filters[key] = [];
                } else {
                    this.filters[key] = '';
                }
            });
            this.filters.sort = 'priority';
            this.filters.order = 'desc';
        }
    }
}

function priceSlider(min, max) {
    return {
        min: min,
        max: max,
        init() {
            this.filters = this.$parent.filters;
        },
        formatPrice(price) {
            return new Intl.NumberFormat('en-RW', {
                style: 'currency',
                currency: 'RWF',
                minimumFractionDigits: 0
            }).format(price);
        }
    }
}
</script>

<style>
.advanced-filters {
    @apply bg-white rounded-lg shadow-sm border border-gray-200;
}

.filters-panel {
    @apply p-6;
}

.filter-group {
    @apply space-y-3;
}

.filter-label {
    @apply block text-sm font-semibold text-gray-900 mb-2;
}

.filter-sublabel {
    @apply block text-xs font-medium text-gray-600 mb-1;
}

.filter-input {
    @apply w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
}

.filter-select {
    @apply w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
}

.filter-checkbox {
    @apply w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500;
}

.filter-actions {
    @apply pt-4 border-t border-gray-200;
}

.price-slider .slider {
    @apply appearance-none bg-transparent cursor-pointer;
}

.price-slider .slider::-webkit-slider-track {
    @apply bg-gray-200 h-2 rounded-lg;
}

.price-slider .slider::-webkit-slider-thumb {
    @apply appearance-none bg-blue-500 h-4 w-4 rounded-full cursor-pointer;
}

.price-slider .slider::-moz-range-track {
    @apply bg-gray-200 h-2 rounded-lg;
}

.price-slider .slider::-moz-range-thumb {
    @apply bg-blue-500 h-4 w-4 rounded-full cursor-pointer border-0;
}
</style>
