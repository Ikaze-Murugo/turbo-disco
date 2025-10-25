@extends('layouts.app')

@section('title', 'Search Properties - Advanced Property Search')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Search Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Search Properties</h1>
                    <p class="text-gray-600 mt-1">Find your perfect home with advanced filters</p>
                </div>
                <a href="{{ route('properties.public.index') }}" class="text-blue-600 hover:text-blue-700">
                    ← Back to All Properties
                </a>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <x-advanced-property-filters 
                :filter-options="$filterOptions" 
                :current-filters="request()->all()" 
                class="w-full" />
        </div>
    </div>

    <!-- Results Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Results Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    Search Results
                </h2>
                <p class="text-gray-600">
                    Found {{ $properties->total() }} properties
                </p>
            </div>
            
            <!-- Sort Options -->
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Sort by:</label>
                <select id="sortSelect" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="priority" {{ request('sort') == 'priority' ? 'selected' : '' }}>Recommended</option>
                    <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Price</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="bedrooms" {{ request('sort') == 'bedrooms' ? 'selected' : '' }}>Bedrooms</option>
                    <option value="area" {{ request('sort') == 'area' ? 'selected' : '' }}>Area</option>
                </select>
            </div>
        </div>

        <!-- Properties Grid -->
        @if($properties->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No properties found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your search criteria or filters</p>
                <a href="{{ route('properties.public.search') }}" class="btn btn-primary">
                    Clear All Filters
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($properties as $property)
                    <x-property-card 
                        :property="$property"
                        :enable-comparison="true"
                        :show-actions="true"
                        layout="grid" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</div>

<script>
// Sort functionality
document.getElementById('sortSelect').addEventListener('change', function() {
    const url = new URL(window.location);
    url.searchParams.set('sort', this.value);
    window.location.href = url.toString();
});
</script>
@endsection