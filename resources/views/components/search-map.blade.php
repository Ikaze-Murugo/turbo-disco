@props(['properties' => collect(), 'height' => '500px', 'center' => [30.0619, -1.9441], 'zoom' => 12])

<div class="search-map-container">
    <div id="search-map" 
         class="w-full border rounded-lg shadow-sm" 
         style="height: {{ $height }};">
    </div>
    
    <!-- Map Controls -->
    <div class="mt-4 flex flex-wrap gap-2">
        <button id="fit-bounds-btn" 
                class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
            Fit to Properties
        </button>
        <button id="reset-view-btn" 
                class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
            Reset View
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Mapbox
    mapboxgl.accessToken = '{{ config('services.mapbox.access_token') }}';
    
    const map = new mapboxgl.Map({
        container: 'search-map',
        style: '{{ config('services.mapbox.style_url') }}',
        center: [{{ $center[0] }}, {{ $center[1] }}],
        zoom: {{ $zoom }}
    });

    // Add navigation controls
    map.addControl(new mapboxgl.NavigationControl());

    // Store markers for later reference
    const markers = [];
    const bounds = new mapboxgl.LngLatBounds();

    // Add property markers
    @foreach($properties as $property)
        @if($property->hasCoordinates())
            const marker{{ $property->id }} = new mapboxgl.Marker({
                color: '#3B82F6'
            })
            .setLngLat([{{ $property->longitude }}, {{ $property->latitude }}])
            .addTo(map);

            const popup{{ $property->id }} = new mapboxgl.Popup({ offset: 25 })
                .setHTML(`
                    <div class="p-3 max-w-xs">
                        <div class="mb-2">
                            @if($property->images->where('is_primary', true)->first())
                                <img src="{{ Storage::url($property->images->where('is_primary', true)->first()->path) }}" 
                                     alt="{{ $property->title }}" 
                                     class="w-full h-24 object-cover rounded">
                            @endif
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $property->title }}</h3>
                        <p class="text-sm text-gray-600 mb-2">{{ $property->formatted_address }}</p>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">{{ $property->bedrooms }} bed • {{ $property->bathrooms }} bath</span>
                            <span class="text-sm font-medium text-blue-600">RWF {{ number_format($property->price) }}</span>
                        </div>
                        <a href="{{ route('properties.show', $property) }}" 
                           class="block w-full text-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            View Details
                        </a>
                    </div>
                `);
            
            marker{{ $property->id }}.setPopup(popup{{ $property->id }});
            markers.push(marker{{ $property->id }});
            bounds.extend([{{ $property->longitude }}, {{ $property->latitude }}]);
        @endif
    @endforeach

    // Fit bounds to show all properties
    if (markers.length > 0) {
        map.fitBounds(bounds, { padding: 50 });
    }

    // Fit bounds button
    document.getElementById('fit-bounds-btn').addEventListener('click', function() {
        if (markers.length > 0) {
            map.fitBounds(bounds, { padding: 50 });
        }
    });

    // Reset view button
    document.getElementById('reset-view-btn').addEventListener('click', function() {
        map.setCenter([{{ $center[0] }}, {{ $center[1] }}]);
        map.setZoom({{ $zoom }});
    });

    // Add click event to map for property search
    map.on('click', function(e) {
        // You can add functionality here to search for properties near the clicked location
        console.log('Map clicked at:', e.lngLat);
    });
});
</script>
@endpush
