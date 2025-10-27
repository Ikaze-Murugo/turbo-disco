@props([
    'height' => '600px',
    'showFilters' => true,
    'clustering' => true,
    'center' => ['lat' => -1.9441, 'lng' => 30.0619],
    'zoom' => 13,
    'properties' => null,
    'searchUrl' => null,
    'enableSearch' => true,
    'enableRadius' => true,
    'enableArea' => true,
    'enableNearby' => true
])

<div class="map-container w-full" style="height: {{ $height }};">
    <!-- Map Controls -->
    @if($showFilters)
        <div class="map-controls absolute top-4 left-4 z-10 bg-white rounded-lg shadow-lg p-4 max-w-sm">
            <div class="flex flex-col gap-3">
                <!-- Search Input -->
                @if($enableSearch)
                    <div class="relative">
                        <input 
                            type="text" 
                            id="map-search-input" 
                            placeholder="Search location..." 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <button 
                            id="map-search-btn" 
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 p-1 text-gray-500 hover:text-gray-700"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                @endif

                <!-- Filter Controls -->
                <div class="flex gap-2">
                    @if($enableRadius)
                        <button 
                            id="radius-search-btn" 
                            class="flex-1 px-3 py-2 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                        >
                            Radius Search
                        </button>
                    @endif
                    
                    @if($enableArea)
                        <button 
                            id="area-search-btn" 
                            class="flex-1 px-3 py-2 text-sm bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors"
                        >
                            Area Search
                        </button>
                    @endif
                </div>

                <!-- Radius Slider -->
                @if($enableRadius)
                    <div id="radius-controls" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Search Radius: <span id="radius-value">5</span> km
                        </label>
                        <input 
                            type="range" 
                            id="radius-slider" 
                            min="0.5" 
                            max="50" 
                            step="0.5" 
                            value="5" 
                            class="w-full"
                        >
                    </div>
                @endif

                <!-- Property Type Filter -->
                <select id="property-type-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All Types</option>
                    <option value="apartment">Apartment</option>
                    <option value="house">House</option>
                    <option value="villa">Villa</option>
                    <option value="commercial">Commercial</option>
                </select>

                <!-- Price Range -->
                <div class="grid grid-cols-2 gap-2">
                    <input 
                        type="number" 
                        id="min-price" 
                        placeholder="Min Price" 
                        class="px-3 py-2 border border-gray-300 rounded-md text-sm"
                    >
                    <input 
                        type="number" 
                        id="max-price" 
                        placeholder="Max Price" 
                        class="px-3 py-2 border border-gray-300 rounded-md text-sm"
                    >
                </div>

                <!-- Clear Filters -->
                <button 
                    id="clear-filters-btn" 
                    class="w-full px-3 py-2 text-sm bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors"
                >
                    Clear Filters
                </button>
            </div>
        </div>
    @endif

    <!-- Map Container -->
    <div id="property-map" class="w-full h-full rounded-lg shadow-lg"></div>

    <!-- Loading Overlay -->
    <div id="map-loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-20 hidden">
        <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
            <p class="text-sm text-gray-600">Loading map...</p>
        </div>
    </div>

    <!-- Map Legend -->
    <div class="map-legend absolute bottom-4 right-4 z-10 bg-white rounded-lg shadow-lg p-3 text-xs">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
            <span>Available</span>
        </div>
        <div class="flex items-center gap-2 mb-2">
            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
            <span>Featured</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            <span>New</span>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.css" />
<style>
    .map-container {
        position: relative;
    }
    
    .map-controls {
        max-width: 300px;
    }
    
    @media (max-width: 768px) {
        .map-controls {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            max-width: none;
            border-radius: 0;
            z-index: 1000;
        }
        
        .map-legend {
            bottom: 10px;
            right: 10px;
        }
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
    
    .property-popup .features {
        display: flex;
        gap: 8px;
        margin-bottom: 8px;
    }
    
    .property-popup .feature {
        background: #f3f4f6;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
    }
    
    .property-popup .actions {
        display: flex;
        gap: 8px;
    }
    
    .property-popup .btn {
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .property-popup .btn-primary {
        background: #2563eb;
        color: white;
    }
    
    .property-popup .btn-primary:hover {
        background: #1d4ed8;
    }
    
    .property-popup .btn-secondary {
        background: #6b7280;
        color: white;
    }
    
    .property-popup .btn-secondary:hover {
        background: #4b5563;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Map configuration
    const mapConfig = {
        center: @json($center),
        zoom: {{ $zoom }},
        clustering: {{ $clustering ? 'true' : 'false' }},
        searchUrl: '{{ $searchUrl ?? route("api.properties.geojson") }}',
        enableSearch: {{ $enableSearch ? 'true' : 'false' }},
        enableRadius: {{ $enableRadius ? 'true' : 'false' }},
        enableArea: {{ $enableArea ? 'true' : 'false' }},
        enableNearby: {{ $enableNearby ? 'true' : 'false' }}
    };

    // Initialize map
    const map = L.map('property-map').setView([mapConfig.center.lat, mapConfig.center.lng], mapConfig.zoom);
    
    // Configure Leaflet to use CDN for default icons
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
        iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
    });

    // Add tile layer using CartoDB (more reliable than OpenStreetMap)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    // Initialize marker cluster group
    let markers = null;
    if (mapConfig.clustering) {
        markers = L.markerClusterGroup({
            chunkedLoading: true,
            maxClusterRadius: 50,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });
        map.addLayer(markers);
    }

    // Current search state
    let currentSearch = {
        type: null,
        radius: 5,
        center: null,
        area: null,
        filters: {}
    };

    // Load properties
    function loadProperties() {
        showLoading();
        
        const params = new URLSearchParams();
        
        // Add search parameters
        if (currentSearch.type === 'radius' && currentSearch.center) {
            params.append('lat', currentSearch.center.lat);
            params.append('lng', currentSearch.center.lng);
            params.append('radius', currentSearch.radius);
        } else if (currentSearch.type === 'area' && currentSearch.area) {
            params.append('polygon', JSON.stringify(currentSearch.area));
        }
        
        // Add filters
        Object.keys(currentSearch.filters).forEach(key => {
            if (currentSearch.filters[key]) {
                params.append(key, currentSearch.filters[key]);
            }
        });
        
        fetch(`${mapConfig.searchUrl}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                displayProperties(data);
                hideLoading();
            })
            .catch(error => {
                console.error('Error loading properties:', error);
                hideLoading();
            });
    }

    // Display properties on map
    function displayProperties(data) {
        // Clear existing markers
        if (markers) {
            markers.clearLayers();
        } else {
            map.eachLayer(layer => {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });
        }

        // Add new markers
        if (data.features && data.features.length > 0) {
            data.features.forEach(feature => {
                const property = feature.properties;
                const coords = feature.geometry.coordinates;
                
                // Create custom icon based on property status
                const iconColor = getPropertyIconColor(property);
                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div class="marker-icon" style="background-color: ${iconColor}">
                        <span class="marker-price">${formatPrice(property.price)}</span>
                    </div>`,
                    iconSize: [40, 40],
                    iconAnchor: [20, 40],
                    popupAnchor: [0, -40]
                });

                const marker = L.marker([coords[1], coords[0]], { icon: icon });
                
                // Create popup content
                const popupContent = createPopupContent(property);
                marker.bindPopup(popupContent, {
                    className: 'property-popup',
                    maxWidth: 300
                });

                if (markers) {
                    markers.addLayer(marker);
                } else {
                    marker.addTo(map);
                }
            });

            // Fit map to show all markers
            if (markers) {
                map.fitBounds(markers.getBounds(), { padding: [20, 20] });
            }
        }
    }

    // Get property icon color
    function getPropertyIconColor(property) {
        if (property.is_featured) return '#f59e0b'; // Yellow for featured
        if (property.type === 'commercial') return '#8b5cf6'; // Purple for commercial
        return '#3b82f6'; // Blue for regular
    }

    // Format price
    function formatPrice(price) {
        return new Intl.NumberFormat('en-RW', {
            style: 'currency',
            currency: 'RWF',
            minimumFractionDigits: 0
        }).format(price);
    }

    // Create popup content
    function createPopupContent(property) {
        return `
            <div class="property-popup">
                ${property.image ? `<img src="${property.image}" alt="${property.title}" loading="lazy">` : ''}
                <h3>${property.title}</h3>
                <div class="price">${formatPrice(property.price)}</div>
                <div class="details">
                    ${property.type} • ${property.bedrooms} bed • ${property.bathrooms} bath
                    ${property.area ? ` • ${property.area} m²` : ''}
                </div>
                <div class="features">
                    <span class="feature">${property.furnishing_status}</span>
                    ${property.is_featured ? '<span class="feature">Featured</span>' : ''}
                </div>
                <div class="actions">
                    <a href="${property.url}" class="btn btn-primary">View Details</a>
                    <button onclick="addToCompare(${property.id})" class="btn btn-secondary">Compare</button>
                </div>
            </div>
        `;
    }

    // Show/hide loading
    function showLoading() {
        document.getElementById('map-loading').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('map-loading').classList.add('hidden');
    }

    // Search functionality
    if (mapConfig.enableSearch) {
        const searchInput = document.getElementById('map-search-input');
        const searchBtn = document.getElementById('map-search-btn');
        
        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        
        function performSearch() {
            const query = searchInput.value.trim();
            if (query) {
                // Geocode the address
                fetch(`/api/properties/geocode?address=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.latitude && data.longitude) {
                            currentSearch.center = {
                                lat: data.latitude,
                                lng: data.longitude
                            };
                            currentSearch.type = 'radius';
                            map.setView([data.latitude, data.longitude], 15);
                            loadProperties();
                        }
                    })
                    .catch(error => {
                        console.error('Geocoding error:', error);
                    });
            }
        }
    }

    // Radius search
    if (mapConfig.enableRadius) {
        const radiusBtn = document.getElementById('radius-search-btn');
        const radiusControls = document.getElementById('radius-controls');
        const radiusSlider = document.getElementById('radius-slider');
        const radiusValue = document.getElementById('radius-value');
        
        radiusBtn.addEventListener('click', function() {
            if (currentSearch.type === 'radius') {
                currentSearch.type = null;
                radiusBtn.classList.remove('bg-blue-600');
                radiusBtn.classList.add('bg-blue-500');
                radiusControls.classList.add('hidden');
            } else {
                currentSearch.type = 'radius';
                radiusBtn.classList.add('bg-blue-600');
                radiusBtn.classList.remove('bg-blue-500');
                radiusControls.classList.remove('hidden');
                
                // Set center to current map center
                const center = map.getCenter();
                currentSearch.center = {
                    lat: center.lat,
                    lng: center.lng
                };
            }
            loadProperties();
        });
        
        radiusSlider.addEventListener('input', function() {
            currentSearch.radius = parseFloat(this.value);
            radiusValue.textContent = currentSearch.radius;
            if (currentSearch.type === 'radius') {
                loadProperties();
            }
        });
    }

    // Area search
    if (mapConfig.enableArea) {
        const areaBtn = document.getElementById('area-search-btn');
        let drawControl = null;
        
        areaBtn.addEventListener('click', function() {
            if (currentSearch.type === 'area') {
                currentSearch.type = null;
                areaBtn.classList.remove('bg-green-600');
                areaBtn.classList.add('bg-green-500');
                if (drawControl) {
                    map.removeControl(drawControl);
                }
            } else {
                currentSearch.type = 'area';
                areaBtn.classList.add('bg-green-600');
                areaBtn.classList.remove('bg-green-500');
                
                // Add drawing control
                drawControl = new L.Control.Draw({
                    draw: {
                        polygon: true,
                        rectangle: true,
                        circle: false,
                        marker: false,
                        polyline: false,
                        circlemarker: false
                    },
                    edit: {
                        featureGroup: new L.FeatureGroup()
                    }
                });
                
                map.addControl(drawControl);
                
                map.on(L.Draw.Event.CREATED, function(e) {
                    const layer = e.layer;
                    const type = e.layerType;
                    
                    if (type === 'polygon' || type === 'rectangle') {
                        const coordinates = layer.getLatLngs()[0].map(latlng => [latlng.lng, latlng.lat]);
                        currentSearch.area = coordinates;
                        loadProperties();
                    }
                });
            }
        });
    }

    // Filter functionality
    const propertyTypeFilter = document.getElementById('property-type-filter');
    const minPriceInput = document.getElementById('min-price');
    const maxPriceInput = document.getElementById('max-price');
    const clearFiltersBtn = document.getElementById('clear-filters-btn');
    
    function updateFilters() {
        currentSearch.filters = {
            type: propertyTypeFilter.value,
            min_price: minPriceInput.value,
            max_price: maxPriceInput.value
        };
        loadProperties();
    }
    
    propertyTypeFilter.addEventListener('change', updateFilters);
    minPriceInput.addEventListener('input', updateFilters);
    maxPriceInput.addEventListener('input', updateFilters);
    
    clearFiltersBtn.addEventListener('click', function() {
        propertyTypeFilter.value = '';
        minPriceInput.value = '';
        maxPriceInput.value = '';
        currentSearch.filters = {};
        currentSearch.type = null;
        currentSearch.center = null;
        currentSearch.area = null;
        
        // Reset buttons
        document.getElementById('radius-search-btn').classList.remove('bg-blue-600');
        document.getElementById('area-search-btn').classList.remove('bg-green-600');
        document.getElementById('radius-controls').classList.add('hidden');
        
        loadProperties();
    });

    // Initial load
    loadProperties();
});
</script>
@endpush
