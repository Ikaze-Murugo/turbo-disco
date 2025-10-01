@extends('public.layout')

@section('title', 'Advanced Property Search - Murugo Property Platform')
@section('description', 'Use our advanced search filters to find the perfect property in Rwanda. Filter by price, amenities, location, and more.')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Advanced Property Search</h1>
            <p class="text-gray-600">Find your perfect property with our comprehensive search filters</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Advanced Search Form Sidebar -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Search Filters</h3>
                        <button type="button" onclick="clearAllFilters()" class="text-sm text-blue-600 hover:text-blue-800">
                            Clear All
                        </button>
                    </div>
                    
                    <form method="GET" action="{{ route('public.search') }}" id="searchForm" class="space-y-6">
                        <!-- Search Term with Auto-complete -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <div class="relative">
                                <input type="text" name="search" id="searchInput" placeholder="Enter keywords, location..." 
                                       value="{{ request('search') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       autocomplete="off">
                                <div id="searchSuggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg hidden mt-1 max-h-60 overflow-y-auto"></div>
                            </div>
                        </div>

                        <!-- Property Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Types</option>
                                @foreach($filterOptions['types'] as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purpose</label>
                            <select name="purpose" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Rent or Sale</option>
                                <option value="rent" {{ request('purpose') == 'rent' ? 'selected' : '' }}>For Rent</option>
                                <option value="sale" {{ request('purpose') == 'sale' ? 'selected' : '' }}>For Sale</option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price Range (RWF)</label>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <input type="number" name="price_min" placeholder="Min" 
                                           value="{{ request('price_min') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <span class="text-gray-500">to</span>
                                    <input type="number" name="price_max" placeholder="Max" 
                                           value="{{ request('price_max') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="text-xs text-gray-500">
                                    Range: {{ number_format($filterOptions['price_range']['min']) }} - {{ number_format($filterOptions['price_range']['max']) }} RWF
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <select name="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Locations</option>
                                @foreach($filterOptions['locations'] as $location)
                                    <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                        {{ $location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bedrooms -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bedrooms</label>
                            <select name="bedrooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Any</option>
                                @foreach($filterOptions['bedrooms'] as $bedrooms)
                                    <option value="{{ $bedrooms }}" {{ request('bedrooms') == $bedrooms ? 'selected' : '' }}>
                                        {{ $bedrooms }}+ Bedrooms
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bathrooms -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bathrooms</label>
                            <select name="bathrooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Any</option>
                                @foreach($filterOptions['bathrooms'] as $bathrooms)
                                    <option value="{{ $bathrooms }}" {{ request('bathrooms') == $bathrooms ? 'selected' : '' }}>
                                        {{ $bathrooms }}+ Bathrooms
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Area Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Area (m²)</label>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <input type="number" name="area_min" placeholder="Min" 
                                           value="{{ request('area_min') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <span class="text-gray-500">to</span>
                                    <input type="number" name="area_max" placeholder="Max" 
                                           value="{{ request('area_max') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Furnishing Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Furnishing</label>
                            <select name="furnishing_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Any</option>
                                <option value="furnished" {{ request('furnishing_status') == 'furnished' ? 'selected' : '' }}>Furnished</option>
                                <option value="semi-furnished" {{ request('furnishing_status') == 'semi-furnished' ? 'selected' : '' }}>Semi-Furnished</option>
                                <option value="unfurnished" {{ request('furnishing_status') == 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                            </select>
                        </div>

                        <!-- Parking Spaces -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Parking Spaces</label>
                            <select name="parking_spaces" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Any</option>
                                <option value="1" {{ request('parking_spaces') == '1' ? 'selected' : '' }}>1+ Spaces</option>
                                <option value="2" {{ request('parking_spaces') == '2' ? 'selected' : '' }}>2+ Spaces</option>
                                <option value="3" {{ request('parking_spaces') == '3' ? 'selected' : '' }}>3+ Spaces</option>
                            </select>
                        </div>

                        <!-- Amenities -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_balcony" value="1" {{ request('has_balcony') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Balcony</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_garden" value="1" {{ request('has_garden') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Garden</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_pool" value="1" {{ request('has_pool') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Swimming Pool</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_gym" value="1" {{ request('has_gym') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Gym</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_security" value="1" {{ request('has_security') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Security</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_elevator" value="1" {{ request('has_elevator') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Elevator</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_air_conditioning" value="1" {{ request('has_air_conditioning') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Air Conditioning</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_internet" value="1" {{ request('has_internet') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Internet</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="pets_allowed" value="1" {{ request('pets_allowed') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Pets Allowed</span>
                                </label>
                            </div>
                        </div>

                        <!-- Search Button -->
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            Search Properties
                        </button>
                    </form>
                </div>
            </div>

            <!-- Results Section -->
            <div class="lg:w-3/4">
                <!-- Results Header -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">
                                {{ $properties->total() }} Properties Found
                            </h2>
                            <p class="text-gray-600 mt-1">
                                @if(request()->hasAny(['search', 'type', 'purpose', 'price_min', 'price_max', 'location', 'bedrooms', 'bathrooms']))
                                    Showing results for your search criteria
                                @else
                                    All available properties
                                @endif
                            </p>
                        </div>
                        
                        <!-- Sort Options -->
                        <div class="mt-4 sm:mt-0">
                            <form method="GET" class="flex items-center space-x-2">
                                @foreach(request()->except(['sort', 'order']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="priority" {{ request('sort') == 'priority' ? 'selected' : '' }}>Priority</option>
                                    <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Price</option>
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Newest</option>
                                    <option value="view_count" {{ request('sort') == 'view_count' ? 'selected' : '' }}>Most Viewed</option>
                                </select>
                                <select name="order" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Descending</option>
                                    <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Properties Grid -->
                @if($properties->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($properties as $property)
                            <div class="property-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                @if($property->images->count() > 0)
                                    <div class="h-48 bg-gray-200 relative">
                                        <img src="{{ Storage::url($property->images->first()->path) }}" 
                                             alt="{{ $property->title }}" 
                                             class="w-full h-full object-cover">
                                        @if($property->is_featured)
                                            <div class="absolute top-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                                Featured
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400 text-lg">No Image</span>
                                    </div>
                                @endif
                                
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $property->title }}</h3>
                                    <p class="text-gray-600 mb-3">{{ $property->location }}</p>
                                    
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="text-xl font-bold text-blue-600">
                                            {{ number_format($property->price) }} RWF
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ ucfirst($property->type) }}
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                        <span>{{ $property->bedrooms }} Bedrooms</span>
                                        <span>{{ $property->bathrooms }} Bathrooms</span>
                                        @if($property->area)
                                            <span>{{ $property->area }} m²</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <a href="{{ route('public.property.show', $property) }}" 
                                           class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                            View Details
                                        </a>
                                        @if(isset($comparison))
                                            <button onclick="toggleComparison({{ $property->id }})" 
                                                    class="px-3 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                                                    id="compare-btn-{{ $property->id }}">
                                                Compare
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $properties->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <div class="text-gray-400 text-6xl mb-4">🏠</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Properties Found</h3>
                        <p class="text-gray-600 mb-6">Try adjusting your search criteria to find more properties.</p>
                        <button onclick="clearAllFilters()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Clear Filters
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Search suggestions
document.getElementById('searchInput').addEventListener('input', function() {
    const query = this.value;
    const suggestionsDiv = document.getElementById('searchSuggestions');
    
    if (query.length < 2) {
        suggestionsDiv.classList.add('hidden');
        return;
    }
    
    fetch(`/search/suggestions?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            
            if (data.popular && data.popular.length > 0) {
                html += '<div class="px-3 py-2 text-xs font-semibold text-gray-500 border-b">Popular Searches</div>';
                data.popular.forEach(term => {
                    html += `<div class="px-3 py-2 hover:bg-gray-100 cursor-pointer" onclick="selectSuggestion('${term}')">${term}</div>`;
                });
            }
            
            if (data.locations && data.locations.length > 0) {
                html += '<div class="px-3 py-2 text-xs font-semibold text-gray-500 border-b">Locations</div>';
                data.locations.forEach(location => {
                    html += `<div class="px-3 py-2 hover:bg-gray-100 cursor-pointer" onclick="selectSuggestion('${location}')">${location}</div>`;
                });
            }
            
            if (data.types && data.types.length > 0) {
                html += '<div class="px-3 py-2 text-xs font-semibold text-gray-500 border-b">Property Types</div>';
                data.types.forEach(type => {
                    html += `<div class="px-3 py-2 hover:bg-gray-100 cursor-pointer" onclick="selectSuggestion('${type}')">${type}</div>`;
                });
            }
            
            if (html) {
                suggestionsDiv.innerHTML = html;
                suggestionsDiv.classList.remove('hidden');
            } else {
                suggestionsDiv.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error fetching suggestions:', error);
            suggestionsDiv.classList.add('hidden');
        });
});

function selectSuggestion(term) {
    document.getElementById('searchInput').value = term;
    document.getElementById('searchSuggestions').classList.add('hidden');
}

// Hide suggestions when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#searchInput') && !e.target.closest('#searchSuggestions')) {
        document.getElementById('searchSuggestions').classList.add('hidden');
    }
});

// Clear all filters
function clearAllFilters() {
    document.getElementById('searchForm').reset();
    window.location.href = '{{ route("public.search") }}';
}

// Comparison functionality
function toggleComparison(propertyId) {
    const button = document.getElementById(`compare-btn-${propertyId}`);
    const isCurrentlyAdded = button.classList.contains('bg-blue-600');
    
    if (isCurrentlyAdded) {
        // Remove from comparison
        fetch(`/properties/${propertyId}/compare`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.textContent = 'Compare';
                button.classList.remove('bg-blue-600', 'text-white');
                button.classList.add('border-blue-600', 'text-blue-600');
            }
        });
    } else {
        // Add to comparison
        fetch(`/properties/${propertyId}/compare`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.textContent = 'Added';
                button.classList.add('bg-blue-600', 'text-white');
                button.classList.remove('border-blue-600', 'text-blue-600');
            } else {
                alert(data.message);
            }
        });
    }
}
</script>
@endsection
