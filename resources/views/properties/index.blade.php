@extends('layouts.app')

@section('title', 'Browse Properties')
@section('description', 'Discover amazing rental properties across Rwanda. Find your perfect home today.')

@section('content')
<div id="main-content" class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="relative bg-slate-900 text-white overflow-hidden" style="min-height: 350px;">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="/images/heroes/properties-hero.png" 
                 alt="Browse Properties in Rwanda" 
                 class="w-full h-full object-cover object-center">
            <!-- Dark Overlay for Text Readability -->
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        
        <div class="container relative z-10 py-20 md:py-24">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 drop-shadow-2xl">Discover Your Perfect Home</h1>
                <p class="text-lg md:text-xl max-w-2xl mx-auto mb-8 text-white drop-shadow-lg">
                    Browse through thousands of rental properties across Rwanda. 
                    Find your dream home with our modern property search.
                </p>
                
                <!-- Quick Search -->
                <div class="max-w-2xl mx-auto">
                    <div class="card mobile-search-form bg-white/95 backdrop-blur-sm">
                        <form action="{{ route('properties.search') }}" method="GET" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="form-label" for="location-search">Location</label>
                                    <input type="text" 
                                           id="location-search"
                                           name="location" 
                                           placeholder="Enter city or district"
                                           class="form-input"
                                           value="{{ request('location') }}"
                                           aria-label="Search by location">
                                </div>
                                
                                <div>
                                    <label class="form-label" for="property-type-search">Property Type</label>
                                    <select id="property-type-search" name="property_type" class="form-input" aria-label="Filter by property type">
                                        <option value="">Any Type</option>
                                        <option value="house" {{ request('property_type') == 'house' ? 'selected' : '' }}>House</option>
                                        <option value="apartment" {{ request('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                        <option value="studio" {{ request('property_type') == 'studio' ? 'selected' : '' }}>Studio</option>
                                        <option value="condo" {{ request('property_type') == 'condo' ? 'selected' : '' }}>Condo</option>
                                        <option value="villa" {{ request('property_type') == 'villa' ? 'selected' : '' }}>Villa</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="form-label" for="max-price-search">Max Price</label>
                                    <select id="max-price-search" name="max_price" class="form-input" aria-label="Filter by maximum price">
                                        <option value="">Any Price</option>
                                        <option value="50000" {{ request('max_price') == '50000' ? 'selected' : '' }}>RWF 50,000</option>
                                        <option value="100000" {{ request('max_price') == '100000' ? 'selected' : '' }}>RWF 100,000</option>
                                        <option value="200000" {{ request('max_price') == '200000' ? 'selected' : '' }}>RWF 200,000</option>
                                        <option value="500000" {{ request('max_price') == '500000' ? 'selected' : '' }}>RWF 500,000</option>
                                        <option value="1000000" {{ request('max_price') == '1000000' ? 'selected' : '' }}>RWF 1,000,000+</option>
                                    </select>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-full" aria-label="Search for properties">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search Properties
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Properties Section -->
    <div class="py-12">
        <div class="container">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div>
                    <h2 class="text-heading-2 mb-2">
                        @if(auth()->check() && auth()->user()->isAdmin())
                            All Properties ({{ $properties->total() }})
                        @elseif(auth()->check() && auth()->user()->isLandlord())
                            My Properties ({{ $properties->total() }})
                        @else
                            Available Properties ({{ $properties->total() }})
                        @endif
                    </h2>
                    <p class="text-body text-gray-600">
                        @if(auth()->check() && auth()->user()->isLandlord())
                            Manage your property listings
                        @else
                            Find your perfect rental home
                        @endif
                    </p>
                </div>
                
                <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
                    @if(auth()->check() && auth()->user()->isRenter())
                        <a href="{{ route('properties.search') }}" class="btn btn-outline">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Advanced Search
                        </a>
                        <a href="{{ route('properties.search-map') }}" class="btn btn-outline">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Map View
                        </a>
                    @endif
                    
                    @if(auth()->check() && auth()->user()->isLandlord())
                        <a href="{{ route('properties.create') }}" class="btn btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New Property
                        </a>
                    @endif
                </div>
            </div>

            @if($properties->isEmpty())
                <!-- Empty State -->
                <div class="card text-center py-16">
                    <div class="max-w-md mx-auto">
                        <svg class="h-24 w-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h3 class="text-heading-3 mb-4">No Properties Found</h3>
                        <p class="text-body text-gray-600 mb-8">
                            @if(auth()->check() && auth()->user()->isLandlord())
                                Start by adding your first property to get started.
                            @else
                                Check back later for new listings or try adjusting your search criteria.
                            @endif
                        </p>
                        
                        @if(auth()->check() && auth()->user()->isLandlord())
                            <a href="{{ route('properties.create') }}" class="btn btn-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Your First Property
                            </a>
                        @else
                            <a href="{{ route('properties.search') }}" class="btn btn-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Try Advanced Search
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <!-- Enhanced Property Grid - Modern Webflow-inspired Design -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 justify-items-center">
                    @foreach($properties as $property)
                        <x-property-card 
                            :property="$property"
                            :show-carousel="true"
                            :enable-favorites="auth()->check() && auth()->user()->isRenter()"
                            :enable-comparison="auth()->check() && auth()->user()->isRenter()"
                            :show-actions="true"
                            class="w-full max-w-sm"
                        />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection