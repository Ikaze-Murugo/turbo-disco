@props([
    'height' => '500px',
    'showResults' => true,
    'resultsCount' => 0,
    'searchQuery' => '',
    'filters' => []
])

<div class="search-map-container">
    <!-- Search Header -->
    <div class="search-header bg-white border-b border-gray-200 p-4">
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
            <!-- Search Input -->
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input 
                        type="text" 
                        id="search-input" 
                        value="{{ $searchQuery }}"
                        placeholder="Search properties by location..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Results Count -->
            @if($showResults)
                <div class="text-sm text-gray-600">
                    <span id="results-count">{{ $resultsCount }}</span> properties found
                </div>
            @endif
            
            <!-- View Toggle -->
            <div class="flex gap-2">
                <button 
                    id="map-view-btn" 
                    class="px-3 py-2 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                >
                    Map View
                </button>
                <button 
                    id="list-view-btn" 
                    class="px-3 py-2 text-sm bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors"
                >
                    List View
                </button>
            </div>
        </div>
    </div>

    <!-- Map and Results Container -->
    <div class="flex flex-col lg:flex-row h-full">
        <!-- Map Container -->
        <div class="map-section flex-1" style="height: {{ $height }};">
            <div id="search-map" class="w-full h-full"></div>
        </div>
        
        <!-- Results Panel -->
        @if($showResults)
            <div id="results-panel" class="results-panel w-full lg:w-96 bg-white border-l border-gray-200 overflow-y-auto" style="height: {{ $height }};">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Search Results</h3>
                        <button 
                            id="close-results-btn" 
                            class="lg:hidden p-2 text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Loading State -->
                    <div id="results-loading" class="hidden text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                        <p class="text-sm text-gray-600">Searching properties...</p>
                    </div>
                    
                    <!-- Results List -->
                    <div id="results-list" class="space-y-4">
                        <!-- Results will be populated here -->
                    </div>
                    
                    <!-- No Results -->
                    <div id="no-results" class="hidden text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 6.291A7.962 7.962 0 0012 5c-2.34 0-4.29 1.009-5.824 2.709"></path>
                        </svg>
                        <p class="text-gray-600">No properties found</p>
                        <p class="text-sm text-gray-500">Try adjusting your search criteria</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Mobile Results Overlay -->
    @if($showResults)
        <div id="mobile-results-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden lg:hidden">
            <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-lg max-h-96 overflow-y-auto">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Search Results</h3>
                        <button 
                            id="close-mobile-results-btn" 
                            class="p-2 text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="mobile-results-list" class="space-y-4">
                        <!-- Mobile results will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<style>
    .search-map-container {
        position: relative;
        height: 100%;
    }
    
    .map-section {
        position: relative;
    }
    
    .results-panel {
        display: none;
    }
    
    .results-panel.show {
        display: block;
    }
    
    @media (min-width: 1024px) {
        .results-panel {
            display: block;
        }
    }
    
    .property-result-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .property-result-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .property-result-card.active {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    
    .property-result-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }
    
    .property-result-content {
        padding: 12px;
    }
    
    .property-result-title {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
        line-height: 1.4;
    }
    
    .property-result-price {
        font-size: 16px;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 4px;
    }
    
    .property-result-details {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 8px;
    }
    
    .property-result-features {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }
    
    .property-result-feature {
        background: #f3f4f6;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        color: #374151;
    }
    
    .search-marker {
        background: #3b82f6;
        border: 2px solid white;
        border-radius: 50%;
        width: 12px;
        height: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .search-marker.active {
        background: #ef4444;
        width: 16px;
        height: 16px;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Map configuration
    const mapConfig = {
        center: [-1.9441, 30.0619],
        zoom: 13,
        showResults: {{ $showResults ? 'true' : 'false' }}
    };

    // Initialize map
    const map = L.map('search-map').setView(mapConfig.center, mapConfig.zoom);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Initialize marker cluster group
    const markers = L.markerClusterGroup({
        chunkedLoading: true,
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true
    });
    map.addLayer(markers);

    // Current search state
    let currentSearch = {
        query: '{{ $searchQuery }}',
        center: null,
        radius: null,
        filters: @json($filters),
        results: []
    };

    // DOM elements
    const searchInput = document.getElementById('search-input');
    const resultsCount = document.getElementById('results-count');
    const resultsList = document.getElementById('results-list');
    const mobileResultsList = document.getElementById('mobile-results-list');
    const resultsLoading = document.getElementById('results-loading');
    const noResults = document.getElementById('no-results');
    const mapViewBtn = document.getElementById('map-view-btn');
    const listViewBtn = document.getElementById('list-view-btn');
    const resultsPanel = document.getElementById('results-panel');
    const closeResultsBtn = document.getElementById('close-results-btn');
    const mobileResultsOverlay = document.getElementById('mobile-results-overlay');
    const closeMobileResultsBtn = document.getElementById('close-mobile-results-btn');

    // Search functionality
    function performSearch() {
        const query = searchInput.value.trim();
        currentSearch.query = query;
        
        if (query) {
            // Geocode the search query
            fetch(`/api/properties/geocode?address=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.latitude && data.longitude) {
                        currentSearch.center = {
                            lat: data.latitude,
                            lng: data.longitude
                        };
                        currentSearch.radius = 10; // Default 10km radius
                        
                        // Center map on search location
                        map.setView([data.latitude, data.longitude], 15);
                        
                        // Add search marker
                        addSearchMarker(data.latitude, data.longitude);
                        
                        // Search for properties
                        searchProperties();
                    } else {
                        showNoResults();
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                    showNoResults();
                });
        } else {
            // Clear search
            clearSearch();
        }
    }

    // Search for properties
    function searchProperties() {
        showLoading();
        
        const params = new URLSearchParams();
        
        if (currentSearch.center) {
            params.append('lat', currentSearch.center.lat);
            params.append('lng', currentSearch.center.lng);
            params.append('radius', currentSearch.radius);
        }
        
        // Add filters
        Object.keys(currentSearch.filters).forEach(key => {
            if (currentSearch.filters[key]) {
                params.append(key, currentSearch.filters[key]);
            }
        });
        
        fetch(`/api/properties/geojson?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                currentSearch.results = data.features || [];
                displayResults(currentSearch.results);
                hideLoading();
            })
            .catch(error => {
                console.error('Search error:', error);
                showNoResults();
                hideLoading();
            });
    }

    // Display search results
    function displayResults(results) {
        // Clear existing markers
        markers.clearLayers();
        
        // Update results count
        if (resultsCount) {
            resultsCount.textContent = results.length;
        }
        
        if (results.length === 0) {
            showNoResults();
            return;
        }
        
        // Add markers to map
        results.forEach((feature, index) => {
            const property = feature.properties;
            const coords = feature.geometry.coordinates;
            
            // Create custom icon
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div class="search-marker" data-property-id="${property.id}"></div>`,
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });

            const marker = L.marker([coords[1], coords[0]], { icon: icon });
            
            // Create popup
            const popupContent = createPopupContent(property);
            marker.bindPopup(popupContent, {
                className: 'property-popup',
                maxWidth: 300
            });

            // Add click event
            marker.on('click', function() {
                highlightProperty(property.id);
            });

            markers.addLayer(marker);
        });

        // Display results in panel
        displayResultsList(results);
        
        // Fit map to show all markers
        if (results.length > 0) {
            map.fitBounds(markers.getBounds(), { padding: [20, 20] });
        }
    }

    // Display results list
    function displayResultsList(results) {
        if (!resultsList) return;
        
        resultsList.innerHTML = '';
        results.forEach((feature, index) => {
            const property = feature.properties;
            const resultCard = createResultCard(property, index);
            resultsList.appendChild(resultCard);
        });
        
        // Also update mobile results
        if (mobileResultsList) {
            mobileResultsList.innerHTML = '';
            results.forEach((feature, index) => {
                const property = feature.properties;
                const resultCard = createResultCard(property, index, true);
                mobileResultsList.appendChild(resultCard);
            });
        }
    }

    // Create result card
    function createResultCard(property, index, isMobile = false) {
        const card = document.createElement('div');
        card.className = 'property-result-card';
        card.dataset.propertyId = property.id;
        
        card.innerHTML = `
            <div class="property-result-image-container">
                ${property.image ? `<img src="${property.image}" alt="${property.title}" class="property-result-image" loading="lazy">` : ''}
            </div>
            <div class="property-result-content">
                <h4 class="property-result-title">${property.title}</h4>
                <div class="property-result-price">${formatPrice(property.price)}</div>
                <div class="property-result-details">
                    ${property.type} • ${property.bedrooms} bed • ${property.bathrooms} bath
                    ${property.area ? ` • ${property.area} m²` : ''}
                </div>
                <div class="property-result-features">
                    <span class="property-result-feature">${property.furnishing_status}</span>
                    ${property.is_featured ? '<span class="property-result-feature">Featured</span>' : ''}
                </div>
            </div>
        `;
        
        // Add click event
        card.addEventListener('click', function() {
            highlightProperty(property.id);
            if (isMobile) {
                closeMobileResults();
            }
        });
        
        return card;
    }

    // Highlight property
    function highlightProperty(propertyId) {
        // Remove active class from all cards
        document.querySelectorAll('.property-result-card').forEach(card => {
            card.classList.remove('active');
        });
        
        // Add active class to selected card
        const selectedCard = document.querySelector(`[data-property-id="${propertyId}"]`);
        if (selectedCard) {
            selectedCard.classList.add('active');
        }
        
        // Update marker appearance
        document.querySelectorAll('.search-marker').forEach(marker => {
            marker.classList.remove('active');
        });
        
        const selectedMarker = document.querySelector(`.search-marker[data-property-id="${propertyId}"]`);
        if (selectedMarker) {
            selectedMarker.classList.add('active');
        }
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

    // Format price
    function formatPrice(price) {
        return new Intl.NumberFormat('en-RW', {
            style: 'currency',
            currency: 'RWF',
            minimumFractionDigits: 0
        }).format(price);
    }

    // Add search marker
    function addSearchMarker(lat, lng) {
        // Remove existing search marker
        map.eachLayer(layer => {
            if (layer.options && layer.options.isSearchMarker) {
                map.removeLayer(layer);
            }
        });
        
        // Add new search marker
        const searchMarker = L.marker([lat, lng], {
            isSearchMarker: true
        }).addTo(map);
        
        searchMarker.bindPopup('Search Location').openPopup();
    }

    // Clear search
    function clearSearch() {
        currentSearch.query = '';
        currentSearch.center = null;
        currentSearch.radius = null;
        currentSearch.results = [];
        
        // Clear markers
        markers.clearLayers();
        
        // Remove search marker
        map.eachLayer(layer => {
            if (layer.options && layer.options.isSearchMarker) {
                map.removeLayer(layer);
            }
        });
        
        // Reset map view
        map.setView(mapConfig.center, mapConfig.zoom);
        
        // Clear results
        if (resultsList) {
            resultsList.innerHTML = '';
        }
        if (mobileResultsList) {
            mobileResultsList.innerHTML = '';
        }
        if (resultsCount) {
            resultsCount.textContent = '0';
        }
        
        showNoResults();
    }

    // Show/hide loading
    function showLoading() {
        if (resultsLoading) {
            resultsLoading.classList.remove('hidden');
        }
        if (noResults) {
            noResults.classList.add('hidden');
        }
    }

    function hideLoading() {
        if (resultsLoading) {
            resultsLoading.classList.add('hidden');
        }
    }

    function showNoResults() {
        if (noResults) {
            noResults.classList.remove('hidden');
        }
    }

    // Close mobile results
    function closeMobileResults() {
        if (mobileResultsOverlay) {
            mobileResultsOverlay.classList.add('hidden');
        }
    }

    // Event listeners
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    searchInput.addEventListener('input', function() {
        // Debounce search
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            performSearch();
        }, 500);
    });

    // View toggle
    if (mapViewBtn && listViewBtn) {
        mapViewBtn.addEventListener('click', function() {
            this.classList.add('bg-blue-500', 'text-white');
            this.classList.remove('bg-gray-200', 'text-gray-700');
            listViewBtn.classList.remove('bg-blue-500', 'text-white');
            listViewBtn.classList.add('bg-gray-200', 'text-gray-700');
            
            if (resultsPanel) {
                resultsPanel.classList.remove('show');
            }
        });

        listViewBtn.addEventListener('click', function() {
            this.classList.add('bg-blue-500', 'text-white');
            this.classList.remove('bg-gray-200', 'text-gray-700');
            mapViewBtn.classList.remove('bg-blue-500', 'text-white');
            mapViewBtn.classList.add('bg-gray-200', 'text-gray-700');
            
            if (resultsPanel) {
                resultsPanel.classList.add('show');
            }
        });
    }

    // Close results panel
    if (closeResultsBtn) {
        closeResultsBtn.addEventListener('click', function() {
            if (resultsPanel) {
                resultsPanel.classList.remove('show');
            }
        });
    }

    if (closeMobileResultsBtn) {
        closeMobileResultsBtn.addEventListener('click', closeMobileResults);
    }

    // Initial search if query is provided
    if (currentSearch.query) {
        performSearch();
    }
});
</script>
@endpush
