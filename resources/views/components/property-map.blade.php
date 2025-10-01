@props(['property', 'height' => '400px', 'zoom' => 15, 'showAmenities' => false])

<div class="property-map-container">
    <div id="property-map-{{ $property->id }}" 
         class="w-full border rounded-lg shadow-sm" 
         style="height: {{ $height }};">
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Mapbox
    mapboxgl.accessToken = '{{ config('services.mapbox.access_token') }}';
    
    const map = new mapboxgl.Map({
        container: 'property-map-{{ $property->id }}',
        style: '{{ config('services.mapbox.style_url') }}',
        center: [{{ $property->longitude ?? 30.0619 }}, {{ $property->latitude ?? -1.9441 }}],
        zoom: {{ $zoom }}
    });

    // Add navigation controls
    map.addControl(new mapboxgl.NavigationControl());

    // Add property marker
    @if($property->hasCoordinates())
        const propertyMarker = new mapboxgl.Marker({
            color: '#3B82F6' // Blue color
        })
        .setLngLat([{{ $property->longitude }}, {{ $property->latitude }}])
        .addTo(map);

        // Add popup to property marker
        const popup = new mapboxgl.Popup({ offset: 25 })
            .setHTML(`
                <div class="p-2">
                    <h3 class="font-semibold text-gray-900">{{ $property->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $property->formatted_address }}</p>
                    <p class="text-sm font-medium text-blue-600">RWF {{ number_format($property->price) }}/month</p>
                </div>
            `);
        
        propertyMarker.setPopup(popup);
    @endif

    // Add nearby amenities if requested
    @if($showAmenities && $property->propertyAmenities->count() > 0)
        @foreach($property->propertyAmenities->take(10) as $amenityItem)
            @if($amenityItem->amenity && $amenityItem->amenity->longitude && $amenityItem->amenity->latitude)
                const amenityMarker{{ $loop->index }} = new mapboxgl.Marker({
                    color: '#10B981' // Green color for amenities
                })
                .setLngLat([{{ $amenityItem->amenity->longitude }}, {{ $amenityItem->amenity->latitude }}])
                .addTo(map);

                const amenityPopup{{ $loop->index }} = new mapboxgl.Popup({ offset: 25 })
                    .setHTML(`
                        <div class="p-2">
                            <h4 class="font-semibold text-gray-900">{{ $amenityItem->amenity->name }}</h4>
                            <p class="text-sm text-gray-600">{{ ucfirst($amenityItem->amenity->type) }}</p>
                            <p class="text-sm text-green-600">{{ $amenityItem->distance_km }} km away</p>
                            <p class="text-xs text-gray-500">{{ $amenityItem->walking_time_minutes }} min walk</p>
                        </div>
                    `);
                
                amenityMarker{{ $loop->index }}.setPopup(amenityPopup{{ $loop->index }});
            @endif
        @endforeach
    @endif

    // Fit map to show all markers
    @if($showAmenities && $property->propertyAmenities->count() > 0)
        const bounds = new mapboxgl.LngLatBounds();
        
        // Add property to bounds
        @if($property->longitude && $property->latitude)
            bounds.extend([{{ $property->longitude }}, {{ $property->latitude }}]);
        @endif
        
        // Add amenities to bounds
        @foreach($property->propertyAmenities->take(10) as $amenityItem)
            @if($amenityItem->amenity && $amenityItem->amenity->longitude && $amenityItem->amenity->latitude)
                bounds.extend([{{ $amenityItem->amenity->longitude }}, {{ $amenityItem->amenity->latitude }}]);
            @endif
        @endforeach
        
        map.fitBounds(bounds, { padding: 50 });
    @endif
});
</script>
@endpush
