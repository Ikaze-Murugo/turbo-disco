@extends('public.layout')

@section('title', 'All Properties - Murugo Property Platform')
@section('description', 'Browse all available properties for rent and sale in Rwanda. Find your perfect home with our comprehensive property listings.')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">All Properties</h1>
            <p class="text-gray-600">Discover amazing properties across Rwanda</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
                    
                    <form method="GET" action="{{ route('public.properties') }}" class="space-y-6">
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
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="price_min" placeholder="Min Price" 
                                       value="{{ request('price_min') }}"
                                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <input type="number" name="price_max" placeholder="Max Price" 
                                       value="{{ request('price_max') }}"
                                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
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

                        <!-- Location -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" name="location" placeholder="Enter location" 
                                   value="{{ request('location') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Newest First</option>
                                <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price" {{ request('sort') == 'price' && request('order') == 'desc' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="bedrooms" {{ request('sort') == 'bedrooms' ? 'selected' : '' }}>Most Bedrooms</option>
                            </select>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                Apply Filters
                            </button>
                            <a href="{{ route('public.properties') }}" class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors text-center">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Properties Grid -->
            <div class="lg:w-3/4">
                <!-- Results Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ $properties->total() }} Properties Found
                        </h2>
                        @if(request()->hasAny(['type', 'purpose', 'price_min', 'price_max', 'bedrooms', 'bathrooms', 'location']))
                            <p class="text-sm text-gray-600 mt-1">Filtered results</p>
                        @endif
                    </div>
                    
                    <!-- View Toggle -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">View:</span>
                        <button class="p-2 bg-blue-600 text-white rounded-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                @if($properties->count() > 0)
                    <!-- Properties Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($properties as $property)
                            <div class="property-card bg-white rounded-lg shadow-md overflow-hidden">
                                @if($property->images->count() > 0)
                                    <div class="h-48 bg-gray-200 relative">
                                        <img src="{{ Storage::url($property->images->first()->path) }}" 
                                             alt="{{ $property->title }}" 
                                             class="w-full h-full object-cover">
                                        <div class="absolute top-3 right-3 bg-white bg-opacity-90 px-2 py-1 rounded-full text-sm font-semibold text-gray-900">
                                            {{ number_format($property->price) }} RWF
                                        </div>
                                    </div>
                                @else
                                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400 text-lg">No Image</span>
                                    </div>
                                @endif
                                
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $property->title }}</h3>
                                    <p class="text-gray-600 mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $property->location }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                            </svg>
                                            {{ $property->bedrooms }} bed
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $property->bathrooms }} bath
                                        </span>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                            {{ ucfirst($property->type) }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <a href="{{ route('public.property.show', $property) }}" 
                                           class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                            View Details
                                        </a>
                                        @guest
                                            <a href="{{ route('login') }}" 
                                               class="flex-1 border border-blue-600 text-blue-600 text-center py-2 px-4 rounded-lg hover:bg-blue-50 transition-colors text-sm">
                                                Login to Contact
                                            </a>
                                        @endguest
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $properties->appends(request()->query())->links() }}
                    </div>
                @else
                    <!-- No Results -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No properties found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria.</p>
                        <div class="mt-6">
                            <a href="{{ route('public.properties') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Clear Filters
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
