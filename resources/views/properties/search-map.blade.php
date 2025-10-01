{{-- resources/views/properties/search-map.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Property Search Map
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('properties.index') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    List View
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                {{-- Search Filters Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow sticky top-4">
                        <h3 class="text-lg font-semibold mb-4">Search Filters</h3>
                        
                        <form method="GET" action="{{ route('properties.search-map') }}" class="space-y-4">
                            {{-- Property Type --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Types</option>
                                    <option value="house" {{ request('type') == 'house' ? 'selected' : '' }}>House</option>
                                    <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                    <option value="studio" {{ request('type') == 'studio' ? 'selected' : '' }}>Studio</option>
                                    <option value="commercial" {{ request('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                </select>
                            </div>

                            {{-- Price Range --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Max Price (RWF)</label>
                                <input type="number" name="max_price" value="{{ request('max_price') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 500000">
                            </div>

                            {{-- Bedrooms --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Min Bedrooms</label>
                                <select name="min_bedrooms" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Any</option>
                                    <option value="1" {{ request('min_bedrooms') == '1' ? 'selected' : '' }}>1+</option>
                                    <option value="2" {{ request('min_bedrooms') == '2' ? 'selected' : '' }}>2+</option>
                                    <option value="3" {{ request('min_bedrooms') == '3' ? 'selected' : '' }}>3+</option>
                                    <option value="4" {{ request('min_bedrooms') == '4' ? 'selected' : '' }}>4+</option>
                                </select>
                            </div>

                            {{-- Bathrooms --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Min Bathrooms</label>
                                <select name="min_bathrooms" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Any</option>
                                    <option value="1" {{ request('min_bathrooms') == '1' ? 'selected' : '' }}>1+</option>
                                    <option value="2" {{ request('min_bathrooms') == '2' ? 'selected' : '' }}>2+</option>
                                    <option value="3" {{ request('min_bathrooms') == '3' ? 'selected' : '' }}>3+</option>
                                </select>
                            </div>

                            {{-- Amenities --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="amenities[]" value="parking" 
                                               {{ in_array('parking', request('amenities', [])) ? 'checked' : '' }} class="mr-2">
                                        <span class="text-sm">Parking</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="amenities[]" value="security" 
                                               {{ in_array('security', request('amenities', [])) ? 'checked' : '' }} class="mr-2">
                                        <span class="text-sm">Security</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="amenities[]" value="air_conditioning" 
                                               {{ in_array('air_conditioning', request('amenities', [])) ? 'checked' : '' }} class="mr-2">
                                        <span class="text-sm">Air Conditioning</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="amenities[]" value="balcony" 
                                               {{ in_array('balcony', request('amenities', [])) ? 'checked' : '' }} class="mr-2">
                                        <span class="text-sm">Balcony</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Search Button --}}
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                Apply Filters
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Map and Results --}}
                <div class="lg:col-span-3">
                    {{-- Map Container --}}
                    <div class="bg-white p-6 rounded-lg shadow mb-6">
                        <h3 class="text-lg font-semibold mb-4">Property Map</h3>
                        <x-search-map :properties="$properties" :center-lat="-1.9441" :center-lng="30.0619" :zoom="12" class="w-full h-96" />
                    </div>

                    {{-- Results Summary --}}
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4">Search Results</h3>
                        <p class="text-gray-600 mb-4">
                            Found {{ $properties->count() }} properties matching your criteria
                        </p>

                        {{-- Property List --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($properties as $property)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-semibold text-lg">{{ $property->title }}</h4>
                                        <span class="text-lg font-bold text-blue-600">RWF {{ number_format($property->price) }}</span>
                                    </div>
                                    
                                    <p class="text-gray-600 text-sm mb-2">{{ $property->address }}</p>
                                    
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                        <span>{{ $property->bedrooms }} bed</span>
                                        <span>{{ $property->bathrooms }} bath</span>
                                        <span>{{ $property->area }} sqm</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">{{ ucfirst($property->type) }}</span>
                                        <a href="{{ route('properties.show', $property) }}" 
                                           class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors duration-200">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 text-center py-8">
                                    <p class="text-gray-500">No properties found matching your criteria.</p>
                                    <a href="{{ route('properties.search-map') }}" class="text-blue-600 hover:underline mt-2 inline-block">
                                        Clear filters
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
