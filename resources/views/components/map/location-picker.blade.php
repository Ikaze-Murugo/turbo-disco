@props([
    'latitude' => -1.9441,
    'longitude' => 30.0619,
    'zoom' => 13,
    'height' => '400px',
    'required' => false,
    'name' => 'location'
])

<div class="location-picker-container">
    <!-- Map Container -->
    <div id="location-picker-map" class="w-full rounded-lg shadow-lg border" style="height: {{ $height }};"></div>
    
    <!-- Location Info -->
    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Coordinates Display -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Coordinates</label>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs text-gray-500">Latitude</label>
                        <input 
                            type="number" 
                            id="latitude-input" 
                            name="latitude" 
                            value="{{ $latitude }}"
                            step="any"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                            {{ $required ? 'required' : '' }}
                        >
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Longitude</label>
                        <input 
                            type="number" 
                            id="longitude-input" 
                            name="longitude" 
                            value="{{ $longitude }}"
                            step="any"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                            {{ $required ? 'required' : '' }}
                        >
                    </div>
                </div>
            </div>
            
            <!-- Address Display -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        id="address-display" 
                        readonly
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100"
                        placeholder="Click on map to set location"
                    >
                    <button 
                        type="button" 
                        id="reverse-geocode-btn" 
                        class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                        title="Get address from coordinates"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="mt-3 text-sm text-gray-600">
            <p class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Drag the marker to set your property location, or search for an address below.
            </p>
        </div>
    </div>
    
    <!-- Address Search -->
    <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Search Address</label>
        <div class="flex gap-2">
            <input 
                type="text" 
                id="address-search-input" 
                placeholder="Enter address (e.g., Kigali, Rwanda)" 
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <button 
                type="button" 
                id="address-search-btn" 
                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors"
            >
                Search
            </button>
        </div>
    </div>
    
    <!-- Validation Error -->
    <div id="location-error" class="mt-2 text-sm text-red-600 hidden">
        Please select a valid location within Rwanda.
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.css" />
<style>
    .location-picker-container {
        position: relative;
    }
    
    .location-picker-map {
        border: 2px solid #e5e7eb;
    }
    
    .location-picker-map.valid {
        border-color: #10b981;
    }
    
    .location-picker-map.invalid {
        border-color: #ef4444;
    }
    
    .marker-icon {
        background-color: #ef4444;
        border: 3px solid white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: bold;
    }
    
    .marker-icon.valid {
        background-color: #10b981;
    }
    
    .marker-icon.invalid {
        background-color: #ef4444;
    }
    
    @media (max-width: 768px) {
        .location-picker-container .grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Map configuration
    const mapConfig = {
        center: [{{ $latitude }}, {{ $longitude }}],
        zoom: {{ $zoom }},
        required: {{ $required ? 'true' : 'false' }}
    };

    // Rwanda bounds for validation
    const rwandaBounds = {
        north: 0.0,
        south: -2.9,
        east: 30.9,
        west: 28.8
    };

    // Initialize map
    const map = L.map('location-picker-map').setView(mapConfig.center, mapConfig.zoom);
    
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

    // Add geocoder control
    const geocoder = L.Control.Geocoder.nominatim();
    L.control.geocoder({
        geocoder: geocoder,
        position: 'topright',
        placeholder: 'Search address...',
        errorMessage: 'Nothing found.',
        showResultIcons: true,
        collapsed: false,
        expand: 'touch'
    }).addTo(map);

    // Create draggable marker
    let marker = L.marker(mapConfig.center, {
        draggable: true,
        icon: L.divIcon({
            className: 'custom-marker',
            html: '<div class="marker-icon">📍</div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        })
    }).addTo(map);

    // Current location state
    let currentLocation = {
        lat: {{ $latitude }},
        lng: {{ $longitude }},
        address: ''
    };

    // DOM elements
    const latInput = document.getElementById('latitude-input');
    const lngInput = document.getElementById('longitude-input');
    const addressDisplay = document.getElementById('address-display');
    const addressSearchInput = document.getElementById('address-search-input');
    const addressSearchBtn = document.getElementById('address-search-btn');
    const reverseGeocodeBtn = document.getElementById('reverse-geocode-btn');
    const locationError = document.getElementById('location-error');

    // Validate coordinates
    function validateCoordinates(lat, lng) {
        return lat >= rwandaBounds.south && 
               lat <= rwandaBounds.north && 
               lng >= rwandaBounds.west && 
               lng <= rwandaBounds.east;
    }

    // Update location
    function updateLocation(lat, lng, address = '') {
        currentLocation.lat = lat;
        currentLocation.lng = lng;
        currentLocation.address = address;
        
        // Update inputs
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        addressDisplay.value = address;
        
        // Validate location
        const isValid = validateCoordinates(lat, lng);
        const mapElement = document.getElementById('location-picker-map');
        const markerElement = marker.getElement();
        
        if (isValid) {
            mapElement.classList.remove('invalid');
            mapElement.classList.add('valid');
            markerElement.classList.remove('invalid');
            markerElement.classList.add('valid');
            locationError.classList.add('hidden');
        } else {
            mapElement.classList.remove('valid');
            mapElement.classList.add('invalid');
            markerElement.classList.remove('valid');
            markerElement.classList.add('invalid');
            if (mapConfig.required) {
                locationError.classList.remove('hidden');
            }
        }
        
        return isValid;
    }

    // Reverse geocode
    function reverseGeocode(lat, lng) {
        fetch(`/api/properties/reverse-geocode?lat=${lat}&lng=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data.address) {
                    updateLocation(lat, lng, data.address);
                }
            })
            .catch(error => {
                console.error('Reverse geocoding error:', error);
            });
    }

    // Forward geocode
    function forwardGeocode(address) {
        fetch(`/api/properties/geocode?address=${encodeURIComponent(address)}`)
            .then(response => response.json())
            .then(data => {
                if (data.latitude && data.longitude) {
                    const lat = parseFloat(data.latitude);
                    const lng = parseFloat(data.longitude);
                    
                    // Update marker position
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 15);
                    
                    // Update location
                    updateLocation(lat, lng, data.address);
                } else {
                    alert('Address not found. Please try a different address.');
                }
            })
            .catch(error => {
                console.error('Geocoding error:', error);
                alert('Error searching address. Please try again.');
            });
    }

    // Marker drag events
    marker.on('dragend', function(e) {
        const latlng = e.target.getLatLng();
        const lat = latlng.lat;
        const lng = latlng.lng;
        
        updateLocation(lat, lng);
        
        // Reverse geocode to get address
        reverseGeocode(lat, lng);
    });

    // Map click events
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        // Update marker position
        marker.setLatLng([lat, lng]);
        
        // Update location
        updateLocation(lat, lng);
        
        // Reverse geocode to get address
        reverseGeocode(lat, lng);
    });

    // Input change events
    latInput.addEventListener('input', function() {
        const lat = parseFloat(this.value);
        const lng = parseFloat(lngInput.value);
        
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], map.getZoom());
            updateLocation(lat, lng);
        }
    });

    lngInput.addEventListener('input', function() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(this.value);
        
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], map.getZoom());
            updateLocation(lat, lng);
        }
    });

    // Address search
    addressSearchBtn.addEventListener('click', function() {
        const address = addressSearchInput.value.trim();
        if (address) {
            forwardGeocode(address);
        }
    });

    addressSearchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const address = this.value.trim();
            if (address) {
                forwardGeocode(address);
            }
        }
    });

    // Reverse geocode button
    reverseGeocodeBtn.addEventListener('click', function() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        
        if (!isNaN(lat) && !isNaN(lng)) {
            reverseGeocode(lat, lng);
        }
    });

    // Geocoder result events
    map.on('geocoder_result', function(e) {
        const result = e.result;
        const lat = result.center.lat;
        const lng = result.center.lng;
        const address = result.name;
        
        // Update marker position
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 15);
        
        // Update location
        updateLocation(lat, lng, address);
    });

    // Initial validation
    updateLocation({{ $latitude }}, {{ $longitude }});
});
</script>
@endpush
