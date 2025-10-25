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
            <!-- Advanced Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <x-advanced-property-filters 
                        :filter-options="$filterOptions" 
                        :current-filters="request()->all()" 
                        class="w-full" />
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
                            :enable-comparison="true"
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
