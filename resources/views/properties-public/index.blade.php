@extends('layouts.app')

@section('title', 'Browse Properties - Find Your Perfect Home in Rwanda')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="relative bg-slate-900 text-white overflow-hidden min-h-[400px] md:min-h-[500px] flex items-center">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="/images/heroes/properties-hero.png" 
                 alt="Find Your Perfect Home in Rwanda" 
                 class="w-full h-full object-cover">
            <!-- Strong Dark Overlay for Text Readability -->
            <div class="absolute inset-0 bg-slate-900/75"></div>
        </div>
        
        <div class="container py-20 md:py-24 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 drop-shadow-2xl">
                    Find Your Perfect Home
                </h1>
                <p class="text-lg md:text-xl text-white mb-8 max-w-2xl mx-auto drop-shadow-lg">
                    Discover thousands of rental properties across Rwanda. From cozy apartments to spacious houses, find your ideal home today.
                </p>
                
                <!-- Quick Search Bar -->
                <div class="max-w-4xl mx-auto">
                    <form action="{{ route('properties.public.search') }}" method="GET" class="card p-6 bg-white/95 backdrop-blur-sm">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <label class="form-label">Location</label>
                                <input type="text" 
                                       name="search" 
                                       placeholder="Enter city, district, or neighborhood"
                                       class="form-input"
                                       value="{{ request('search') }}">
                            </div>
                            
                            <div>
                                <label class="form-label">Property Type</label>
                                <select name="type" class="form-input">
                                    <option value="">Any Type</option>
                                    @foreach($filterOptions['types'] as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex items-end">
                                <button type="submit" class="btn btn-primary w-full">
                                    Search Properties
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="container py-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <!-- Filter Toggle -->
                <button id="filterToggle" class="btn btn-ghost flex items-center space-x-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                    <span>Filters</span>
                </button>

                <!-- View Toggle -->
                <div class="flex items-center space-x-2">
                    <button id="gridView" class="btn btn-icon text-gray-400 hover:text-gray-600 active">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </button>
                    <button id="listView" class="btn btn-icon text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>

                <!-- Sort Dropdown -->
                <div class="flex items-center space-x-2">
                    <label class="form-label">Sort by:</label>
                    <select id="sortSelect" class="form-input">
                        <option value="newest">Newest First</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="size_large">Size: Largest First</option>
                        <option value="size_small">Size: Smallest First</option>
                        <option value="popular">Most Popular</option>
                    </select>
                </div>
            </div>

            <!-- Advanced Filters Panel -->
            <div id="filtersPanel" class="hidden mt-6 pt-6 border-t border-gray-200">
                <form action="{{ route('properties.public.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Price Range -->
                    <div>
                        <label class="form-label">Price Range</label>
                        <div class="space-y-2">
                            <input type="number" name="min_price" placeholder="Min Price" 
                                   class="form-input"
                                   value="{{ request('min_price') }}">
                            <input type="number" name="max_price" placeholder="Max Price" 
                                   class="form-input"
                                   value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <!-- Bedrooms -->
                    <div>
                        <label class="form-label">Bedrooms</label>
                        <select name="bedrooms" class="form-input">
                            <option value="">Any</option>
                            <option value="1" {{ request('bedrooms') == '1' ? 'selected' : '' }}>1+</option>
                            <option value="2" {{ request('bedrooms') == '2' ? 'selected' : '' }}>2+</option>
                            <option value="3" {{ request('bedrooms') == '3' ? 'selected' : '' }}>3+</option>
                            <option value="4" {{ request('bedrooms') == '4' ? 'selected' : '' }}>4+</option>
                            <option value="5" {{ request('bedrooms') == '5' ? 'selected' : '' }}>5+</option>
                        </select>
                    </div>

                    <!-- Bathrooms -->
                    <div>
                        <label class="form-label">Bathrooms</label>
                        <select name="bathrooms" class="form-input">
                            <option value="">Any</option>
                            <option value="1" {{ request('bathrooms') == '1' ? 'selected' : '' }}>1+</option>
                            <option value="2" {{ request('bathrooms') == '2' ? 'selected' : '' }}>2+</option>
                            <option value="3" {{ request('bathrooms') == '3' ? 'selected' : '' }}>3+</option>
                            <option value="4" {{ request('bathrooms') == '4' ? 'selected' : '' }}>4+</option>
                        </select>
                    </div>

                    <!-- Furnishing Status -->
                    <div>
                        <label class="form-label">Furnishing</label>
                        <select name="furnishing_status" class="form-input">
                            <option value="">Any</option>
                            @foreach($filterOptions['furnishing_statuses'] as $status)
                                <option value="{{ $status }}" {{ request('furnishing_status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('-', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amenities -->
                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="form-label">Amenities</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            @foreach($filterOptions['amenities'] as $amenity)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" 
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           {{ in_array($amenity->id, (array) request('amenities', [])) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700">{{ $amenity->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="md:col-span-2 lg:col-span-4 flex justify-end space-x-4">
                        <a href="{{ route('properties.public.index') }}" class="btn btn-outline">
                            Clear Filters
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Properties Grid -->
    <div class="container py-8">
        <!-- Results Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-heading-2">
                    {{ $properties->total() }} Properties Found
                </h2>
                @if(request()->hasAny(['search', 'type', 'min_price', 'max_price', 'bedrooms', 'bathrooms']))
                    <p class="text-body mt-1">
                        Showing results for your search criteria
                    </p>
                @endif
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="{{ route('properties.public.map') }}" class="btn btn-outline flex items-center space-x-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Map View</span>
                </a>
            </div>
        </div>

        @if($properties->count() > 0)
            <!-- Properties Grid -->
            <div id="propertiesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($properties as $property)
                    <x-property-card 
                        :property="$property"
                        :show-carousel="true"
                        :enable-favorites="auth()->check() && auth()->user()->isRenter()"
                        :enable-comparison="auth()->check() && auth()->user()->isRenter()"
                        :show-actions="true"
                        class="w-full"
                    />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $properties->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No properties found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Try adjusting your search criteria or browse all properties.
                </p>
                <div class="mt-6">
                    <a href="{{ route('properties.public.index') }}" class="btn btn-primary">
                        Browse All Properties
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- JavaScript for interactivity -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter toggle
    const filterToggle = document.getElementById('filterToggle');
    const filtersPanel = document.getElementById('filtersPanel');
    
    filterToggle.addEventListener('click', function() {
        filtersPanel.classList.toggle('hidden');
    });
    
    // View toggle
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const propertiesGrid = document.getElementById('propertiesGrid');
    
    gridView.addEventListener('click', function() {
        gridView.classList.add('active');
        listView.classList.remove('active');
        propertiesGrid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6';
    });
    
    listView.addEventListener('click', function() {
        listView.classList.add('active');
        gridView.classList.remove('active');
        propertiesGrid.className = 'grid grid-cols-1 gap-6';
    });
    
    // Sort functionality
    const sortSelect = document.getElementById('sortSelect');
    sortSelect.addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });
});
</script>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.active {
    color: #2563eb !important;
}
</style>
@endsection
