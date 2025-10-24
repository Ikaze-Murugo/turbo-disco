<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Search Properties') }}
            </h2>
            <a href="{{ route('properties.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                Back to Properties
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('properties.search') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Search Input -->
                            <div>
                                <x-input-label for="search" :value="__('Search')" />
                                <x-text-input id="search" type="text" name="search" 
                                             :value="request('search')" 
                                             placeholder="Search by title, description, or location" />
                            </div>

                            <!-- Location -->
                            <div>
                                <x-input-label for="location" :value="__('Location')" />
                                <x-text-input id="location" type="text" name="location" 
                                             :value="request('location')" 
                                             placeholder="e.g., Kigali, Remera" />
                            </div>

                            <!-- Bedrooms -->
                            <div>
                                <x-input-label for="bedrooms" :value="__('Min Bedrooms')" />
                                <select id="bedrooms" name="bedrooms" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Any</option>
                                    <option value="1" {{ request('bedrooms') == '1' ? 'selected' : '' }}>1+</option>
                                    <option value="2" {{ request('bedrooms') == '2' ? 'selected' : '' }}>2+</option>
                                    <option value="3" {{ request('bedrooms') == '3' ? 'selected' : '' }}>3+</option>
                                    <option value="4" {{ request('bedrooms') == '4' ? 'selected' : '' }}>4+</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Min Price -->
                            <div>
                                <x-input-label for="min_price" :value="__('Min Price (RWF)')" />
                                <x-text-input id="min_price" type="number" name="min_price" 
                                             :value="request('min_price')" 
                                             placeholder="e.g., 100000" />
                            </div>

                            <!-- Max Price -->
                            <div>
                                <x-input-label for="max_price" :value="__('Max Price (RWF)')" />
                                <x-text-input id="max_price" type="number" name="max_price" 
                                             :value="request('max_price')" 
                                             placeholder="e.g., 500000" />
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <a href="{{ route('properties.search') }}" class="text-gray-600 hover:text-gray-800">
                                Clear Filters
                            </a>
                            <x-primary-button type="submit">
                                {{ __('Search') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results -->
            @if($properties->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="h-24 w-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No properties found</h3>
                        <p class="text-gray-500 mb-4">Try adjusting your search criteria or clear the filters.</p>
                        <a href="{{ route('properties.search') }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                            Clear Filters
                        </a>
                    </div>
                </div>
            @else
                <div class="mb-4">
                    <p class="text-gray-600">
                        Found {{ $properties->total() }} propert{{ $properties->total() === 1 ? 'y' : 'ies' }}
                        @if(request()->hasAny(['search', 'location', 'min_price', 'max_price', 'bedrooms']))
                            matching your criteria
                        @endif
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($properties as $property)
                        <x-property-card 
                            :property="$property"
                            :show-carousel="true"
                            :enable-favorites="auth()->check() && auth()->user()->isRenter()"
                            :enable-comparison="auth()->check() && auth()->user()->isRenter()"
                            :show-actions="true"
                            layout="grid"
                            class="w-full"
                        />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
