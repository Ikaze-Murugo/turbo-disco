@props([
    'height' => '600px',
    'center' => ['lat' => -1.9441, 'lng' => 30.0619],
    'zoom' => 13
])

<div class="simple-map-container w-full" style="height: {{ $height }};">
    <!-- Map Container -->
    <div id="simple-property-map" class="w-full h-full rounded-lg shadow-lg"></div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .simple-map-container {
        position: relative;
    }
    
    #simple-property-map {
        border: 1px solid #e5e7eb;
    }
    
    .leaflet-popup-content {
        margin: 8px 12px;
        line-height: 1.4;
    }
    
    .property-popup {
        min-width: 250px;
    }
    
    .property-popup img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 4px;
        margin-bottom: 8px;
    }
    
    .property-popup h3 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 4px;
        color: #1f2937;
    }
    
    .property-popup .price {
        font-size: 18px;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 4px;
    }
    
    .property-popup .details {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 8px;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Simple map initializing...');
    console.log('Alpine.js loaded:', typeof Alpine !== 'undefined');
    console.log('Leaflet loaded:', typeof L !== 'undefined');
    
    // Check if map container exists
    const mapContainer = document.getElementById('simple-property-map');
    if (!mapContainer) {
        console.error('Map container not found!');
        return;
    }
    
    console.log('Map container found:', mapContainer);
    console.log('Container dimensions:', mapContainer.offsetWidth, 'x', mapContainer.offsetHeight);
    console.log('Container visible:', mapContainer.offsetWidth > 0 && mapContainer.offsetHeight > 0);
    console.log('Container display:', window.getComputedStyle(mapContainer).display);
    console.log('Container visibility:', window.getComputedStyle(mapContainer).visibility);
    
    // Initialize map with a small delay to ensure container is ready
    setTimeout(() => {
        try {
            const map = L.map('simple-property-map').setView([{{ $center['lat'] }}, {{ $center['lng'] }}], {{ $zoom }});
        
        // Configure Leaflet to use CDN for default icons
        L.Icon.Default.mergeOptions({
            iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
            iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        });

        // Add CartoDB Positron tile layer (very reliable, no API key needed)
        console.log('Creating map with CartoDB Positron tiles...');
        
        const cartoLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20,
            minZoom: 1,
            // Performance optimizations
            updateWhenZooming: false,
            updateWhenIdle: true,
            keepBuffer: 2
        });
        
        // Fallback to OpenStreetMap if CartoDB fails
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
            minZoom: 1
        });
        
        // Try CartoDB first, fallback to OSM
        cartoLayer.addTo(map);
        
        // Add error handling for tile loading
        cartoLayer.on('tileerror', function(e) {
            console.warn('CartoDB tile error, switching to OpenStreetMap');
            map.removeLayer(cartoLayer);
            osmLayer.addTo(map);
        });
        
        console.log('Map initialized, loading properties...');
        
        // Load properties
        loadProperties(map);
        
        } catch (error) {
            console.error('Map initialization failed:', error);
            mapContainer.innerHTML = '<div style="padding: 20px; color: red; text-align: center;">Map failed to load: ' + error.message + '</div>';
        }
    }, 100);

    // Function to load properties
    function loadProperties(map) {
        fetch('/api/properties/geojson')
            .then(response => {
                console.log('API response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Properties loaded:', data.features.length);
                console.log('Raw data:', data);
                
                // Create markers and add to map
                const markers = [];
                const usedCoordinates = new Set();
                
                data.features.forEach((feature, index) => {
                    const property = feature.properties;
                    let coords = feature.geometry.coordinates;
                    
                    // Ensure coordinates are numbers
                    coords = [parseFloat(coords[0]), parseFloat(coords[1])];
                    
                    console.log(`Creating marker for property: ${property.title} at [${coords[1]}, ${coords[0]}]`);
                    
                    // Validate coordinates
                    if (!coords || coords.length < 2 || isNaN(coords[0]) || isNaN(coords[1])) {
                        console.error('Invalid coordinates for property:', property.title, coords);
                        return;
                    }
                    
                    // If coordinates are already used, add slight offset to make markers visible
                    const coordKey = `${coords[0]},${coords[1]}`;
                    if (usedCoordinates.has(coordKey)) {
                        // Add small random offset to make markers visible
                        const offset = 0.001; // About 100m offset
                        coords = [
                            parseFloat(coords[0]) + (Math.random() - 0.5) * offset,
                            parseFloat(coords[1]) + (Math.random() - 0.5) * offset
                        ];
                        console.log(`Offsetting duplicate coordinates to: [${coords[1]}, ${coords[0]}]`);
                    }
                    usedCoordinates.add(coordKey);
                    
                    // Use standard Leaflet marker
                    const marker = L.marker([coords[1], coords[0]]);
                    
                    // Create popup content
                    const popupContent = `
                        <div class="property-popup">
                            ${property.image ? `<img src="${property.image}" alt="${property.title}">` : ''}
                            <h3>${property.title}</h3>
                            <div class="price">${formatPrice(property.price)}</div>
                            <div class="details">
                                ${property.type} • ${property.bedrooms} bed • ${property.bathrooms} bath
                                ${property.area ? ` • ${property.area} m²` : ''}
                            </div>
                            <div style="margin-top: 8px;">
                                <a href="${property.url}" style="display: inline-block; background: #2563eb; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">View Details</a>
                            </div>
                        </div>
                    `;
                    
                    marker.bindPopup(popupContent, {
                        className: 'property-popup',
                        maxWidth: 300
                    });

                    marker.addTo(map);
                    markers.push(marker);
                });

                // Fit map to show all markers
                if (markers.length > 0) {
                    const group = new L.featureGroup(markers);
                    const bounds = group.getBounds();
                    
                    // Check if bounds are valid (not collapsed to a single point)
                    const boundsSize = bounds.getNorthEast().distanceTo(bounds.getSouthWest());
                    console.log('Bounds size:', boundsSize);
                    
                    if (boundsSize < 0.001) {
                        // If all markers are very close, set a fixed zoom level
                        console.log('All markers are very close, using fixed zoom level');
                        map.setView(bounds.getCenter(), 15);
                    } else {
                        // Normal bounds fitting with reasonable zoom limits
                        map.fitBounds(bounds.pad(0.1), {
                            maxZoom: 16, // Prevent excessive zoom
                            animate: true
                        });
                    }
                    
                    console.log('Markers added to map:', markers.length);
                    console.log('Map bounds:', bounds);
                    console.log('Map center:', map.getCenter());
                    console.log('Map zoom:', map.getZoom());
                } else {
                    console.log('No properties to display');
                }
                
                console.log('Map setup complete!');
            })
            .catch(error => {
                console.error('Error loading properties:', error);
                alert('Failed to load properties: ' + error.message);
            });
    }

    // Utility functions
    function formatPrice(price) {
        return new Intl.NumberFormat('en-RW', {
            style: 'currency',
            currency: 'RWF',
            minimumFractionDigits: 0
        }).format(price);
    }

    function formatPriceShort(price) {
        if (price >= 1000000) {
            return (price / 1000000).toFixed(1) + 'M';
        } else if (price >= 1000) {
            return (price / 1000).toFixed(0) + 'K';
        }
        return price.toString();
    }
});
</script>
@endpush
