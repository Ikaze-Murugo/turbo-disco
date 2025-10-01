@extends('layouts.app')

@section('title', 'Properties Map - Find Properties by Location')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Map Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Properties Map</h1>
                    <p class="text-gray-600 mt-1">Explore properties by location</p>
                </div>
                <a href="{{ route('properties.public.index') }}" class="text-blue-600 hover:text-blue-700">
                    ← Back to List View
                </a>
            </div>
        </div>
    </div>

    <div class="flex h-screen">
        <!-- Map Container -->
        <div class="flex-1 relative">
            <div id="map" class="w-full h-full"></div>
            
            <!-- Map Controls -->
            <div class="absolute top-4 left-4 bg-white rounded-lg shadow-md p-4">
                <h3 class="font-semibold text-gray-900 mb-2">Quick Filters</h3>
                <div class="space-y-2">
                    <select id="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">All Types</option>
                        <option value="apartment">Apartment</option>
                        <option value="house">House</option>
                        <option value="studio">Studio</option>
                        <option value="villa">Villa</option>
                    </select>
                    <select id="priceFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">All Prices</option>
                        <option value="0-50000">Under RWF 50,000</option>
                        <option value="50000-100000">RWF 50,000 - 100,000</option>
                        <option value="100000-200000">RWF 100,000 - 200,000</option>
                        <option value="200000-500000">RWF 200,000 - 500,000</option>
                        <option value="500000+">RWF 500,000+</option>
                    </select>
                </div>
            </div>
            
            <!-- Property Count -->
            <div class="absolute bottom-4 left-4 bg-white rounded-lg shadow-md p-3">
                <div class="text-sm text-gray-600">
                    <span id="propertyCount">{{ $properties->count() }}</span> properties shown
                </div>
            </div>
        </div>

        <!-- Properties List Sidebar -->
        <div class="w-96 bg-white border-l border-gray-200 overflow-y-auto">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Properties</h3>
                <p class="text-sm text-gray-600">Click on a property to view details</p>
            </div>
            
            <div class="p-4 space-y-4">
                @foreach($properties as $property)
                    <div class="property-card border border-gray-200 rounded-lg p-4 hover:border-blue-300 cursor-pointer transition-colors" 
                         data-lat="{{ $property->latitude }}" 
                         data-lng="{{ $property->longitude }}"
                         data-property-id="{{ $property->id }}">
                        <div class="flex space-x-3">
                            <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                @if($property->images->count() > 0)
                                            <img src="{{ asset('storage/' . $property->images->first()->path) }}" 
                                                 alt="{{ $property->title }}"
                                                 class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 line-clamp-1">{{ $property->title }}</h4>
                                <p class="text-sm text-gray-500 line-clamp-1">{{ $property->neighborhood ?? $property->address }}</p>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-sm text-gray-600">{{ $property->bedrooms }} bed, {{ $property->bathrooms }} bath</span>
                                    <span class="font-medium text-blue-600">RWF {{ number_format($property->price) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Mapbox GL JS -->
<script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    mapboxgl.accessToken = '{{ env("MAPBOX_ACCESS_TOKEN") }}';
    
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [-1.9441, 30.0619], // Kigali coordinates
        zoom: 12
    });
    
    // Add navigation controls
    map.addControl(new mapboxgl.NavigationControl());
    
    // Properties data
    const properties = {!! json_encode($properties->map(function($property) {
        return [
            'id' => $property->id,
            'title' => $property->title,
            'price' => $property->price,
            'bedrooms' => $property->bedrooms,
            'bathrooms' => $property->bathrooms,
            'type' => $property->type,
            'latitude' => $property->latitude,
            'longitude' => $property->longitude,
            'neighborhood' => $property->neighborhood,
            'address' => $property->address,
            'image' => $property->images->count() > 0 ? 'storage/' . $property->images->first()->path : null
        ];
    })) !!};
    
    // Add markers for each property
    const markers = [];
    properties.forEach(property => {
        if (property.latitude && property.longitude) {
            // Create marker element
            const markerEl = document.createElement('div');
            markerEl.className = 'property-marker';
            markerEl.style.cssText = `
                width: 30px;
                height: 30px;
                background-color: #3B82F6;
                border: 2px solid white;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 12px;
                font-weight: bold;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            `;
            markerEl.textContent = 'RWF';
            
            // Create popup
            const popup = new mapboxgl.Popup({
                offset: 25,
                closeButton: true,
                closeOnClick: false
            }).setHTML(`
                <div class="p-2">
                    <h3 class="font-semibold text-gray-900 text-sm">${property.title}</h3>
                    <p class="text-gray-600 text-xs">${property.neighborhood || property.address}</p>
                    <p class="text-blue-600 font-medium text-sm">RWF ${property.price.toLocaleString()}</p>
                    <p class="text-gray-500 text-xs">${property.bedrooms} bed, ${property.bathrooms} bath</p>
                    <a href="/properties/${property.id}" class="text-blue-600 text-xs hover:underline">View Details</a>
                </div>
            `);
            
            // Create marker
            const marker = new mapboxgl.Marker(markerEl)
                .setLngLat([property.longitude, property.latitude])
                .setPopup(popup)
                .addTo(map);
            
            markers.push(marker);
            
            // Add click event to property card
            const propertyCard = document.querySelector(`[data-property-id="${property.id}"]`);
            if (propertyCard) {
                propertyCard.addEventListener('click', function() {
                    map.flyTo({
                        center: [property.longitude, property.latitude],
                        zoom: 15
                    });
                    marker.togglePopup();
                });
            }
        }
    });
    
    // Filter functionality
    const typeFilter = document.getElementById('typeFilter');
    const priceFilter = document.getElementById('priceFilter');
    const propertyCount = document.getElementById('propertyCount');
    
    function filterProperties() {
        const selectedType = typeFilter.value;
        const selectedPrice = priceFilter.value;
        
        let visibleCount = 0;
        
        properties.forEach((property, index) => {
            let show = true;
            
            // Type filter
            if (selectedType && property.type !== selectedType) {
                show = false;
            }
            
            // Price filter
            if (selectedPrice && show) {
                const price = property.price;
                switch (selectedPrice) {
                    case '0-50000':
                        show = price <= 50000;
                        break;
                    case '50000-100000':
                        show = price > 50000 && price <= 100000;
                        break;
                    case '100000-200000':
                        show = price > 100000 && price <= 200000;
                        break;
                    case '200000-500000':
                        show = price > 200000 && price <= 500000;
                        break;
                    case '500000+':
                        show = price > 500000;
                        break;
                }
            }
            
            // Show/hide marker
            if (markers[index]) {
                if (show) {
                    markers[index].addTo(map);
                    visibleCount++;
                } else {
                    markers[index].remove();
                }
            }
            
            // Show/hide property card
            const propertyCard = document.querySelector(`[data-property-id="${property.id}"]`);
            if (propertyCard) {
                propertyCard.style.display = show ? 'block' : 'none';
            }
        });
        
        propertyCount.textContent = visibleCount;
    }
    
    typeFilter.addEventListener('change', filterProperties);
    priceFilter.addEventListener('change', filterProperties);
});
</script>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.property-marker:hover {
    background-color: #2563eb !important;
    transform: scale(1.1);
    transition: all 0.2s ease;
}
</style>
@endsection
