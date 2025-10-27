@props([
    'showFilters' => true,
    'showSearch' => true,
    'showRadius' => true,
    'showArea' => true,
    'showLayers' => true,
    'showGeolocation' => true,
    'showFullscreen' => true,
    'showMeasure' => true,
    'showDraw' => true
])

<div class="advanced-map-controls">
    <!-- Main Control Panel -->
    <div class="control-panel bg-white rounded-lg shadow-lg border border-gray-200 p-4">
        <!-- Search Section -->
        @if($showSearch)
            <div class="search-section mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Location</label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="advanced-search-input" 
                        placeholder="Search for address, neighborhood, or landmark..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button 
                        id="advanced-search-btn" 
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 p-1 text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Quick Search Buttons -->
        <div class="quick-search mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quick Search</label>
            <div class="grid grid-cols-2 gap-2">
                <button 
                    id="search-kigali" 
                    class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors"
                >
                    Kigali
                </button>
                <button 
                    id="search-nyarutarama" 
                    class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors"
                >
                    Nyarutarama
                </button>
                <button 
                    id="search-kacyiru" 
                    class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors"
                >
                    Kacyiru
                </button>
                <button 
                    id="search-remera" 
                    class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors"
                >
                    Remera
                </button>
            </div>
        </div>

        <!-- Search Tools -->
        @if($showRadius || $showArea)
            <div class="search-tools mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Tools</label>
                <div class="flex gap-2">
                    @if($showRadius)
                        <button 
                            id="radius-tool-btn" 
                            class="flex-1 px-3 py-2 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                        >
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Radius
                        </button>
                    @endif
                    
                    @if($showArea)
                        <button 
                            id="area-tool-btn" 
                            class="flex-1 px-3 py-2 text-sm bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors"
                        >
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.632A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            Area
                        </button>
                    @endif
                </div>
            </div>
        @endif

        <!-- Radius Controls -->
        @if($showRadius)
            <div id="radius-controls" class="radius-controls mb-4 hidden">
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
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>0.5km</span>
                    <span>25km</span>
                    <span>50km</span>
                </div>
            </div>
        @endif

        <!-- Advanced Filters -->
        @if($showFilters)
            <div class="filters-section mb-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-gray-700">Filters</label>
                    <button 
                        id="toggle-filters" 
                        class="text-xs text-blue-600 hover:text-blue-800"
                    >
                        <span id="filters-toggle-text">Show</span> Advanced
                    </button>
                </div>
                
                <div id="advanced-filters" class="hidden space-y-3">
                    <!-- Property Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Property Type</label>
                        <select id="filter-type" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">All Types</option>
                            <option value="apartment">Apartment</option>
                            <option value="house">House</option>
                            <option value="villa">Villa</option>
                            <option value="commercial">Commercial</option>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Price Range (RWF)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input 
                                type="number" 
                                id="filter-min-price" 
                                placeholder="Min" 
                                class="px-2 py-1 border border-gray-300 rounded text-sm"
                            >
                            <input 
                                type="number" 
                                id="filter-max-price" 
                                placeholder="Max" 
                                class="px-2 py-1 border border-gray-300 rounded text-sm"
                            >
                        </div>
                    </div>

                    <!-- Bedrooms -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Bedrooms</label>
                        <select id="filter-bedrooms" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">Any</option>
                            <option value="1">1+</option>
                            <option value="2">2+</option>
                            <option value="3">3+</option>
                            <option value="4">4+</option>
                            <option value="5">5+</option>
                        </select>
                    </div>

                    <!-- Bathrooms -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Bathrooms</label>
                        <select id="filter-bathrooms" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">Any</option>
                            <option value="1">1+</option>
                            <option value="2">2+</option>
                            <option value="3">3+</option>
                            <option value="4">4+</option>
                        </select>
                    </div>

                    <!-- Features -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Features</label>
                        <div class="space-y-1">
                            <label class="flex items-center text-xs">
                                <input type="checkbox" id="filter-featured" class="mr-2">
                                Featured Only
                            </label>
                            <label class="flex items-center text-xs">
                                <input type="checkbox" id="filter-furnished" class="mr-2">
                                Furnished
                            </label>
                            <label class="flex items-center text-xs">
                                <input type="checkbox" id="filter-parking" class="mr-2">
                                Parking
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="action-buttons space-y-2">
            <button 
                id="apply-filters-btn" 
                class="w-full px-3 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
            >
                Apply Filters
            </button>
            <button 
                id="clear-filters-btn" 
                class="w-full px-3 py-2 text-sm bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors"
            >
                Clear All
            </button>
        </div>
    </div>

    <!-- Map Tools Panel -->
    <div class="map-tools-panel bg-white rounded-lg shadow-lg border border-gray-200 p-3">
        <div class="flex flex-wrap gap-2">
            @if($showGeolocation)
                <button 
                    id="geolocation-btn" 
                    class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                    title="My Location"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
            @endif

            @if($showFullscreen)
                <button 
                    id="fullscreen-btn" 
                    class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                    title="Fullscreen"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </button>
            @endif

            @if($showMeasure)
                <button 
                    id="measure-btn" 
                    class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                    title="Measure Distance"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                    </svg>
                </button>
            @endif

            @if($showDraw)
                <button 
                    id="draw-btn" 
                    class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                    title="Draw on Map"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </button>
            @endif

            @if($showLayers)
                <button 
                    id="layers-btn" 
                    class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                    title="Map Layers"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    <!-- Search Results Summary -->
    <div id="search-summary" class="search-summary bg-blue-50 border border-blue-200 rounded-lg p-3 hidden">
        <div class="flex items-center justify-between">
            <div class="text-sm">
                <span id="results-count" class="font-medium text-blue-900">0</span>
                <span class="text-blue-700"> properties found</span>
            </div>
            <button 
                id="close-summary" 
                class="text-blue-600 hover:text-blue-800"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    .advanced-map-controls {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 1000;
        max-width: 320px;
        width: 100%;
    }

    .control-panel {
        margin-bottom: 12px;
    }

    .map-tools-panel {
        margin-bottom: 12px;
    }

    .search-summary {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .advanced-map-controls {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            max-width: none;
            z-index: 1000;
        }

        .control-panel {
            border-radius: 0;
            margin-bottom: 8px;
        }

        .map-tools-panel {
            border-radius: 0;
            margin-bottom: 8px;
        }

        .search-summary {
            border-radius: 0;
        }
    }

    /* Filter Toggle Animation */
    #advanced-filters {
        transition: all 0.3s ease-in-out;
    }

    #advanced-filters.hidden {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
    }

    #advanced-filters:not(.hidden) {
        max-height: 500px;
        opacity: 1;
    }

    /* Button States */
    .active-tool {
        background-color: #3b82f6 !important;
        color: white !important;
    }

    .tool-loading {
        position: relative;
        pointer-events: none;
    }

    .tool-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 16px;
        height: 16px;
        margin: -8px 0 0 -8px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize advanced map controls
    const controls = {
        searchInput: document.getElementById('advanced-search-input'),
        searchBtn: document.getElementById('advanced-search-btn'),
        radiusToolBtn: document.getElementById('radius-tool-btn'),
        areaToolBtn: document.getElementById('area-tool-btn'),
        radiusControls: document.getElementById('radius-controls'),
        radiusSlider: document.getElementById('radius-slider'),
        radiusValue: document.getElementById('radius-value'),
        toggleFilters: document.getElementById('toggle-filters'),
        advancedFilters: document.getElementById('advanced-filters'),
        applyFiltersBtn: document.getElementById('apply-filters-btn'),
        clearFiltersBtn: document.getElementById('clear-filters-btn'),
        geolocationBtn: document.getElementById('geolocation-btn'),
        fullscreenBtn: document.getElementById('fullscreen-btn'),
        measureBtn: document.getElementById('measure-btn'),
        drawBtn: document.getElementById('draw-btn'),
        layersBtn: document.getElementById('layers-btn'),
        searchSummary: document.getElementById('search-summary'),
        resultsCount: document.getElementById('results-count'),
        closeSummary: document.getElementById('close-summary')
    };

    // State management
    let currentState = {
        searchType: null, // 'radius', 'area', 'none'
        searchCenter: null,
        searchRadius: 5,
        searchArea: null,
        filters: {},
        isGeolocationEnabled: false,
        isFullscreen: false,
        isMeasuring: false,
        isDrawing: false
    };

    // Initialize controls
    initializeControls();

    function initializeControls() {
        // Search functionality
        if (controls.searchBtn) {
            controls.searchBtn.addEventListener('click', performAdvancedSearch);
        }
        
        if (controls.searchInput) {
            controls.searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performAdvancedSearch();
                }
            });
        }

        // Quick search buttons
        const quickSearchButtons = [
            { id: 'search-kigali', location: 'Kigali, Rwanda' },
            { id: 'search-nyarutarama', location: 'Nyarutarama, Kigali, Rwanda' },
            { id: 'search-kacyiru', location: 'Kacyiru, Kigali, Rwanda' },
            { id: 'search-remera', location: 'Remera, Kigali, Rwanda' }
        ];

        quickSearchButtons.forEach(button => {
            const element = document.getElementById(button.id);
            if (element) {
                element.addEventListener('click', () => {
                    if (controls.searchInput) {
                        controls.searchInput.value = button.location;
                    }
                    performAdvancedSearch();
                });
            }
        });

        // Search tools
        if (controls.radiusToolBtn) {
            controls.radiusToolBtn.addEventListener('click', toggleRadiusTool);
        }

        if (controls.areaToolBtn) {
            controls.areaToolBtn.addEventListener('click', toggleAreaTool);
        }

        // Radius controls
        if (controls.radiusSlider && controls.radiusValue) {
            controls.radiusSlider.addEventListener('input', function() {
                currentState.searchRadius = parseFloat(this.value);
                controls.radiusValue.textContent = currentState.searchRadius;
            });
        }

        // Filter controls
        if (controls.toggleFilters && controls.advancedFilters) {
            controls.toggleFilters.addEventListener('click', toggleAdvancedFilters);
        }

        if (controls.applyFiltersBtn) {
            controls.applyFiltersBtn.addEventListener('click', applyFilters);
        }

        if (controls.clearFiltersBtn) {
            controls.clearFiltersBtn.addEventListener('click', clearAllFilters);
        }

        // Map tools
        if (controls.geolocationBtn) {
            controls.geolocationBtn.addEventListener('click', getCurrentLocation);
        }

        if (controls.fullscreenBtn) {
            controls.fullscreenBtn.addEventListener('click', toggleFullscreen);
        }

        if (controls.measureBtn) {
            controls.measureBtn.addEventListener('click', toggleMeasureTool);
        }

        if (controls.drawBtn) {
            controls.drawBtn.addEventListener('click', toggleDrawTool);
        }

        if (controls.layersBtn) {
            controls.layersBtn.addEventListener('click', toggleLayers);
        }

        // Close summary
        if (controls.closeSummary) {
            controls.closeSummary.addEventListener('click', hideSearchSummary);
        }
    }

    // Search functions
    function performAdvancedSearch() {
        const query = controls.searchInput?.value.trim();
        if (!query) return;

        // Show loading state
        if (controls.searchBtn) {
            controls.searchBtn.classList.add('tool-loading');
        }

        // Geocode the search query
        fetch(`/api/properties/geocode?address=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.latitude && data.longitude) {
                    currentState.searchCenter = {
                        lat: data.latitude,
                        lng: data.longitude
                    };
                    
                    // Trigger map search event
                    window.dispatchEvent(new CustomEvent('map:search', {
                        detail: {
                            center: currentState.searchCenter,
                            radius: currentState.searchRadius,
                            filters: currentState.filters
                        }
                    }));
                    
                    showSearchSummary(0); // Will be updated with actual count
                } else {
                    showNotification('Location not found. Please try a different address.', 'error');
                }
            })
            .catch(error => {
                console.error('Geocoding error:', error);
                showNotification('Error searching location. Please try again.', 'error');
            })
            .finally(() => {
                if (controls.searchBtn) {
                    controls.searchBtn.classList.remove('tool-loading');
                }
            });
    }

    // Tool toggle functions
    function toggleRadiusTool() {
        if (currentState.searchType === 'radius') {
            currentState.searchType = null;
            controls.radiusToolBtn.classList.remove('active-tool');
            controls.radiusControls.classList.add('hidden');
        } else {
            currentState.searchType = 'radius';
            controls.radiusToolBtn.classList.add('active-tool');
            controls.radiusControls.classList.remove('hidden');
            
            // Deactivate area tool
            if (controls.areaToolBtn) {
                controls.areaToolBtn.classList.remove('active-tool');
            }
        }
        
        // Trigger map update
        window.dispatchEvent(new CustomEvent('map:searchTypeChanged', {
            detail: { searchType: currentState.searchType }
        }));
    }

    function toggleAreaTool() {
        if (currentState.searchType === 'area') {
            currentState.searchType = null;
            controls.areaToolBtn.classList.remove('active-tool');
        } else {
            currentState.searchType = 'area';
            controls.areaToolBtn.classList.add('active-tool');
            
            // Deactivate radius tool
            if (controls.radiusToolBtn) {
                controls.radiusToolBtn.classList.remove('active-tool');
            }
            if (controls.radiusControls) {
                controls.radiusControls.classList.add('hidden');
            }
        }
        
        // Trigger map update
        window.dispatchEvent(new CustomEvent('map:searchTypeChanged', {
            detail: { searchType: currentState.searchType }
        }));
    }

    function toggleAdvancedFilters() {
        if (controls.advancedFilters) {
            controls.advancedFilters.classList.toggle('hidden');
            const toggleText = controls.toggleFilters?.querySelector('#filters-toggle-text');
            if (toggleText) {
                toggleText.textContent = controls.advancedFilters.classList.contains('hidden') ? 'Show' : 'Hide';
            }
        }
    }

    function applyFilters() {
        // Collect filter values
        currentState.filters = {
            type: document.getElementById('filter-type')?.value || '',
            min_price: document.getElementById('filter-min-price')?.value || '',
            max_price: document.getElementById('filter-max-price')?.value || '',
            bedrooms: document.getElementById('filter-bedrooms')?.value || '',
            bathrooms: document.getElementById('filter-bathrooms')?.value || '',
            featured: document.getElementById('filter-featured')?.checked || false,
            furnished: document.getElementById('filter-furnished')?.checked || false,
            parking: document.getElementById('filter-parking')?.checked || false
        };

        // Trigger map update
        window.dispatchEvent(new CustomEvent('map:filtersChanged', {
            detail: { filters: currentState.filters }
        }));

        showNotification('Filters applied successfully', 'success');
    }

    function clearAllFilters() {
        // Reset all filter inputs
        const filterInputs = [
            'filter-type', 'filter-min-price', 'filter-max-price',
            'filter-bedrooms', 'filter-bathrooms', 'filter-featured',
            'filter-furnished', 'filter-parking'
        ];

        filterInputs.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                if (element.type === 'checkbox') {
                    element.checked = false;
                } else {
                    element.value = '';
                }
            }
        });

        // Reset state
        currentState.filters = {};
        currentState.searchType = null;
        currentState.searchCenter = null;
        currentState.searchArea = null;

        // Reset UI
        if (controls.radiusToolBtn) controls.radiusToolBtn.classList.remove('active-tool');
        if (controls.areaToolBtn) controls.areaToolBtn.classList.remove('active-tool');
        if (controls.radiusControls) controls.radiusControls.classList.add('hidden');

        // Trigger map update
        window.dispatchEvent(new CustomEvent('map:clearAll', {}));

        showNotification('All filters cleared', 'info');
    }

    // Map tool functions
    function getCurrentLocation() {
        if (!navigator.geolocation) {
            showNotification('Geolocation is not supported by this browser', 'error');
            return;
        }

        if (controls.geolocationBtn) {
            controls.geolocationBtn.classList.add('tool-loading');
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                currentState.searchCenter = { lat, lng };
                
                // Trigger map update
                window.dispatchEvent(new CustomEvent('map:geolocation', {
                    detail: { center: currentState.searchCenter }
                }));
                
                showNotification('Location found!', 'success');
            },
            function(error) {
                console.error('Geolocation error:', error);
                showNotification('Unable to get your location', 'error');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000
            }
        ).finally(() => {
            if (controls.geolocationBtn) {
                controls.geolocationBtn.classList.remove('tool-loading');
            }
        });
    }

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().then(() => {
                currentState.isFullscreen = true;
                if (controls.fullscreenBtn) {
                    controls.fullscreenBtn.classList.add('active-tool');
                }
            });
        } else {
            document.exitFullscreen().then(() => {
                currentState.isFullscreen = false;
                if (controls.fullscreenBtn) {
                    controls.fullscreenBtn.classList.remove('active-tool');
                }
            });
        }
    }

    function toggleMeasureTool() {
        currentState.isMeasuring = !currentState.isMeasuring;
        
        if (controls.measureBtn) {
            controls.measureBtn.classList.toggle('active-tool');
        }
        
        // Trigger map update
        window.dispatchEvent(new CustomEvent('map:measureToggle', {
            detail: { enabled: currentState.isMeasuring }
        }));
    }

    function toggleDrawTool() {
        currentState.isDrawing = !currentState.isDrawing;
        
        if (controls.drawBtn) {
            controls.drawBtn.classList.toggle('active-tool');
        }
        
        // Trigger map update
        window.dispatchEvent(new CustomEvent('map:drawToggle', {
            detail: { enabled: currentState.isDrawing }
        }));
    }

    function toggleLayers() {
        // Trigger map update
        window.dispatchEvent(new CustomEvent('map:layersToggle', {}));
    }

    // UI helper functions
    function showSearchSummary(count) {
        if (controls.searchSummary && controls.resultsCount) {
            controls.resultsCount.textContent = count;
            controls.searchSummary.classList.remove('hidden');
        }
    }

    function hideSearchSummary() {
        if (controls.searchSummary) {
            controls.searchSummary.classList.add('hidden');
        }
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-md shadow-lg text-white max-w-sm ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
        }`;
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Listen for map events
    window.addEventListener('map:resultsUpdated', function(e) {
        showSearchSummary(e.detail.count);
    });

    // Export controls for external use
    window.AdvancedMapControls = {
        getState: () => currentState,
        setState: (newState) => { currentState = { ...currentState, ...newState }; },
        showNotification,
        showSearchSummary,
        hideSearchSummary
    };
});
</script>
@endpush
