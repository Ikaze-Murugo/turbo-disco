@props([
    'height' => '100vh',
    'showBottomSheet' => true,
    'showFloatingButton' => true,
    'enableGestures' => true,
    'enableHapticFeedback' => true
])

<div class="mobile-map-container" style="height: {{ $height }};">
    <!-- Map Container -->
    <div id="mobile-map" class="mobile-map w-full h-full"></div>
    
    <!-- Floating Action Button -->
    @if($showFloatingButton)
        <div class="floating-action-button">
            <button 
                id="fab-main" 
                class="fab-main bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </button>
            
            <!-- FAB Menu -->
            <div id="fab-menu" class="fab-menu hidden">
                <button 
                    id="fab-search" 
                    class="fab-item bg-white text-gray-700 rounded-full shadow-lg hover:bg-gray-50 transition-all duration-300"
                    title="Search"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                
                <button 
                    id="fab-location" 
                    class="fab-item bg-white text-gray-700 rounded-full shadow-lg hover:bg-gray-50 transition-all duration-300"
                    title="My Location"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
                
                <button 
                    id="fab-filters" 
                    class="fab-item bg-white text-gray-700 rounded-full shadow-lg hover:bg-gray-50 transition-all duration-300"
                    title="Filters"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                </button>
                
                <button 
                    id="fab-layers" 
                    class="fab-item bg-white text-gray-700 rounded-full shadow-lg hover:bg-gray-50 transition-all duration-300"
                    title="Layers"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif
    
    <!-- Bottom Sheet -->
    @if($showBottomSheet)
        <div id="bottom-sheet" class="bottom-sheet">
            <div class="bottom-sheet-handle"></div>
            
            <!-- Search Bar -->
            <div class="search-section p-4 border-b border-gray-200">
                <div class="relative">
                    <input 
                        type="text" 
                        id="mobile-search-input" 
                        placeholder="Search properties..." 
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button 
                        id="mobile-search-btn" 
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 p-2 text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Quick Filters -->
            <div class="quick-filters p-4 border-b border-gray-200">
                <div class="flex gap-2 overflow-x-auto pb-2">
                    <button 
                        id="filter-all" 
                        class="filter-chip active px-4 py-2 bg-blue-600 text-white rounded-full text-sm font-medium whitespace-nowrap"
                    >
                        All
                    </button>
                    <button 
                        id="filter-apartments" 
                        class="filter-chip px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium whitespace-nowrap"
                    >
                        Apartments
                    </button>
                    <button 
                        id="filter-houses" 
                        class="filter-chip px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium whitespace-nowrap"
                    >
                        Houses
                    </button>
                    <button 
                        id="filter-villas" 
                        class="filter-chip px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium whitespace-nowrap"
                    >
                        Villas
                    </button>
                    <button 
                        id="filter-commercial" 
                        class="filter-chip px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium whitespace-nowrap"
                    >
                        Commercial
                    </button>
                </div>
            </div>
            
            <!-- Results List -->
            <div class="results-section flex-1 overflow-y-auto">
                <div id="mobile-results-list" class="p-4 space-y-3">
                    <!-- Results will be populated here -->
                </div>
                
                <!-- Loading State -->
                <div id="mobile-loading" class="hidden text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                    <p class="text-sm text-gray-600">Loading properties...</p>
                </div>
                
                <!-- No Results -->
                <div id="mobile-no-results" class="hidden text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 6.291A7.962 7.962 0 0012 5c-2.34 0-4.29 1.009-5.824 2.709"></path>
                    </svg>
                    <p class="text-gray-600">No properties found</p>
                    <p class="text-sm text-gray-500">Try adjusting your search criteria</p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Touch Gesture Overlay -->
    @if($enableGestures)
        <div id="gesture-overlay" class="gesture-overlay"></div>
    @endif
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<style>
    .mobile-map-container {
        position: relative;
        overflow: hidden;
        touch-action: pan-x pan-y;
    }

    .mobile-map {
        touch-action: pan-x pan-y;
    }

    /* Floating Action Button */
    .floating-action-button {
        position: absolute;
        bottom: 120px;
        right: 20px;
        z-index: 1000;
    }

    .fab-main {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .fab-main:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .fab-main.active {
        transform: rotate(45deg);
    }

    .fab-menu {
        position: absolute;
        bottom: 0;
        right: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 72px;
        animation: fabMenuSlide 0.3s ease-out;
    }

    @keyframes fabMenuSlide {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.8);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .fab-item {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease-out;
    }

    .fab-item:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    /* Bottom Sheet */
    .bottom-sheet {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-radius: 20px 20px 0 0;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        max-height: 70vh;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .bottom-sheet.collapsed {
        transform: translateY(calc(100% - 60px));
    }

    .bottom-sheet.expanded {
        transform: translateY(0);
    }

    .bottom-sheet-handle {
        width: 40px;
        height: 4px;
        background: #d1d5db;
        border-radius: 2px;
        margin: 12px auto 8px;
        cursor: grab;
    }

    .bottom-sheet-handle:active {
        cursor: grabbing;
    }

    /* Quick Filters */
    .filter-chip {
        transition: all 0.2s ease-out;
        border: 1px solid transparent;
    }

    .filter-chip.active {
        background-color: #2563eb;
        color: white;
        border-color: #2563eb;
    }

    .filter-chip:not(.active):hover {
        background-color: #f3f4f6;
        border-color: #d1d5db;
    }

    /* Mobile Property Cards */
    .mobile-property-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease-out;
        cursor: pointer;
    }

    .mobile-property-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .mobile-property-card.active {
        border: 2px solid #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .mobile-property-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }

    .mobile-property-content {
        padding: 12px;
    }

    .mobile-property-title {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .mobile-property-price {
        font-size: 16px;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 4px;
    }

    .mobile-property-details {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .mobile-property-features {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .mobile-property-feature {
        background: #f3f4f6;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        color: #374151;
        font-weight: 500;
    }

    /* Gesture Overlay */
    .gesture-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 500;
        pointer-events: none;
    }

    /* Touch Feedback */
    .touch-feedback {
        position: absolute;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(37, 99, 235, 0.3);
        pointer-events: none;
        z-index: 1001;
        animation: touchRipple 0.6s ease-out;
    }

    @keyframes touchRipple {
        0% {
            transform: scale(0);
            opacity: 1;
        }
        100% {
            transform: scale(1);
            opacity: 0;
        }
    }

    /* Responsive Adjustments */
    @media (max-width: 480px) {
        .floating-action-button {
            bottom: 100px;
            right: 16px;
        }

        .fab-main {
            width: 48px;
            height: 48px;
        }

        .fab-item {
            width: 44px;
            height: 44px;
        }

        .bottom-sheet {
            max-height: 75vh;
        }

        .mobile-property-image {
            height: 100px;
        }
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .bottom-sheet {
            background: #1f2937;
            color: white;
        }

        .mobile-property-card {
            background: #374151;
            color: white;
        }

        .mobile-property-title {
            color: #f9fafb;
        }

        .mobile-property-details {
            color: #d1d5db;
        }

        .filter-chip:not(.active) {
            background: #4b5563;
            color: #d1d5db;
        }
    }

    /* Accessibility */
    @media (prefers-reduced-motion: reduce) {
        .fab-main,
        .fab-item,
        .mobile-property-card,
        .filter-chip {
            transition: none;
        }

        .fab-menu {
            animation: none;
        }

        .touch-feedback {
            animation: none;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Map Configuration
    const mobileMapConfig = {
        center: [-1.9441, 30.0619],
        zoom: 13,
        enableGestures: {{ $enableGestures ? 'true' : 'false' }},
        enableHapticFeedback: {{ $enableHapticFeedback ? 'true' : 'false' }}
    };

    // Initialize mobile map
    const map = L.map('mobile-map', {
        zoomControl: false,
        attributionControl: false,
        dragging: true,
        touchZoom: true,
        doubleClickZoom: true,
        scrollWheelZoom: false,
        boxZoom: false,
        keyboard: false
    }).setView(mobileMapConfig.center, mobileMapConfig.zoom);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Initialize marker cluster group
    const markers = L.markerClusterGroup({
        chunkedLoading: true,
        maxClusterRadius: 40,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true,
        disableClusteringAtZoom: 16
    });
    map.addLayer(markers);

    // Mobile-specific state
    let mobileState = {
        isBottomSheetExpanded: false,
        isFabMenuOpen: false,
        currentFilters: {},
        searchResults: [],
        selectedProperty: null,
        isSearching: false
    };

    // DOM elements
    const fabMain = document.getElementById('fab-main');
    const fabMenu = document.getElementById('fab-menu');
    const fabSearch = document.getElementById('fab-search');
    const fabLocation = document.getElementById('fab-location');
    const fabFilters = document.getElementById('fab-filters');
    const fabLayers = document.getElementById('fab-layers');
    const bottomSheet = document.getElementById('bottom-sheet');
    const bottomSheetHandle = document.querySelector('.bottom-sheet-handle');
    const searchInput = document.getElementById('mobile-search-input');
    const searchBtn = document.getElementById('mobile-search-btn');
    const resultsList = document.getElementById('mobile-results-list');
    const loading = document.getElementById('mobile-loading');
    const noResults = document.getElementById('mobile-no-results');
    const filterChips = document.querySelectorAll('.filter-chip');
    const gestureOverlay = document.getElementById('gesture-overlay');

    // Initialize mobile map
    initializeMobileMap();

    function initializeMobileMap() {
        // FAB functionality
        if (fabMain) {
            fabMain.addEventListener('click', toggleFabMenu);
        }

        if (fabSearch) {
            fabSearch.addEventListener('click', () => {
                expandBottomSheet();
                searchInput.focus();
                closeFabMenu();
            });
        }

        if (fabLocation) {
            fabLocation.addEventListener('click', getCurrentLocation);
        }

        if (fabFilters) {
            fabFilters.addEventListener('click', () => {
                expandBottomSheet();
                closeFabMenu();
            });
        }

        if (fabLayers) {
            fabLayers.addEventListener('click', toggleLayers);
        }

        // Bottom sheet functionality
        if (bottomSheetHandle) {
            bottomSheetHandle.addEventListener('touchstart', handleBottomSheetTouch);
        }

        // Search functionality
        if (searchBtn) {
            searchBtn.addEventListener('click', performMobileSearch);
        }

        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performMobileSearch();
                }
            });
        }

        // Filter chips
        filterChips.forEach(chip => {
            chip.addEventListener('click', function() {
                // Remove active class from all chips
                filterChips.forEach(c => c.classList.remove('active'));
                // Add active class to clicked chip
                this.classList.add('active');
                
                // Update filters
                const filterType = this.id.replace('filter-', '');
                mobileState.currentFilters.type = filterType === 'all' ? '' : filterType;
                
                // Perform search
                performMobileSearch();
            });
        });

        // Touch gestures
        if (mobileMapConfig.enableGestures && gestureOverlay) {
            initializeTouchGestures();
        }

        // Load initial properties
        loadMobileProperties();
    }

    // FAB functionality
    function toggleFabMenu() {
        mobileState.isFabMenuOpen = !mobileState.isFabMenuOpen;
        
        if (fabMenu) {
            fabMenu.classList.toggle('hidden');
        }
        
        if (fabMain) {
            fabMain.classList.toggle('active');
        }
    }

    function closeFabMenu() {
        mobileState.isFabMenuOpen = false;
        
        if (fabMenu) {
            fabMenu.classList.add('hidden');
        }
        
        if (fabMain) {
            fabMain.classList.remove('active');
        }
    }

    // Bottom sheet functionality
    function expandBottomSheet() {
        mobileState.isBottomSheetExpanded = true;
        
        if (bottomSheet) {
            bottomSheet.classList.remove('collapsed');
            bottomSheet.classList.add('expanded');
        }
    }

    function collapseBottomSheet() {
        mobileState.isBottomSheetExpanded = false;
        
        if (bottomSheet) {
            bottomSheet.classList.remove('expanded');
            bottomSheet.classList.add('collapsed');
        }
    }

    function handleBottomSheetTouch(e) {
        let startY = e.touches[0].clientY;
        let currentY = startY;
        
        function handleTouchMove(e) {
            currentY = e.touches[0].clientY;
            const deltaY = currentY - startY;
            
            if (deltaY > 50) {
                collapseBottomSheet();
                document.removeEventListener('touchmove', handleTouchMove);
                document.removeEventListener('touchend', handleTouchEnd);
            }
        }
        
        function handleTouchEnd() {
            document.removeEventListener('touchmove', handleTouchMove);
            document.removeEventListener('touchend', handleTouchEnd);
        }
        
        document.addEventListener('touchmove', handleTouchMove);
        document.addEventListener('touchend', handleTouchEnd);
    }

    // Search functionality
    function performMobileSearch() {
        const query = searchInput?.value.trim();
        
        if (query) {
            // Geocode search
            fetch(`/api/properties/geocode?address=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.latitude && data.longitude) {
                        map.setView([data.latitude, data.longitude], 15);
                        searchPropertiesByLocation(data.latitude, data.longitude);
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        } else {
            // Search with current filters
            searchPropertiesByLocation();
        }
    }

    function searchPropertiesByLocation(lat = null, lng = null) {
        showMobileLoading();
        
        const params = new URLSearchParams();
        
        if (lat && lng) {
            params.append('lat', lat);
            params.append('lng', lng);
            params.append('radius', 10);
        }
        
        // Add filters
        Object.keys(mobileState.currentFilters).forEach(key => {
            if (mobileState.currentFilters[key]) {
                params.append(key, mobileState.currentFilters[key]);
            }
        });
        
        fetch(`/api/properties/geojson?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                mobileState.searchResults = data.features || [];
                displayMobileResults(mobileState.searchResults);
                displayMobileMarkers(mobileState.searchResults);
                hideMobileLoading();
            })
            .catch(error => {
                console.error('Search error:', error);
                showMobileNoResults();
                hideMobileLoading();
            });
    }

    function loadMobileProperties() {
        searchPropertiesByLocation();
    }

    // Display results
    function displayMobileResults(results) {
        if (!resultsList) return;
        
        resultsList.innerHTML = '';
        
        if (results.length === 0) {
            showMobileNoResults();
            return;
        }
        
        results.forEach((feature, index) => {
            const property = feature.properties;
            const resultCard = createMobilePropertyCard(property, index);
            resultsList.appendChild(resultCard);
        });
    }

    function createMobilePropertyCard(property, index) {
        const card = document.createElement('div');
        card.className = 'mobile-property-card';
        card.dataset.propertyId = property.id;
        
        card.innerHTML = `
            <div class="mobile-property-image-container">
                ${property.image ? `<img src="${property.image}" alt="${property.title}" class="mobile-property-image" loading="lazy">` : ''}
            </div>
            <div class="mobile-property-content">
                <h4 class="mobile-property-title">${property.title}</h4>
                <div class="mobile-property-price">${formatPrice(property.price)}</div>
                <div class="mobile-property-details">
                    ${property.type} • ${property.bedrooms} bed • ${property.bathrooms} bath
                    ${property.area ? ` • ${property.area} m²` : ''}
                </div>
                <div class="mobile-property-features">
                    <span class="mobile-property-feature">${property.furnishing_status}</span>
                    ${property.is_featured ? '<span class="mobile-property-feature">Featured</span>' : ''}
                </div>
            </div>
        `;
        
        // Add click event
        card.addEventListener('click', function() {
            selectMobileProperty(property.id);
            highlightMobileProperty(property.id);
            
            // Haptic feedback
            if (mobileMapConfig.enableHapticFeedback && navigator.vibrate) {
                navigator.vibrate(50);
            }
        });
        
        return card;
    }

    function selectMobileProperty(propertyId) {
        mobileState.selectedProperty = propertyId;
        
        // Find property in results
        const property = mobileState.searchResults.find(f => f.properties.id === propertyId);
        if (property) {
            const coords = property.geometry.coordinates;
            map.setView([coords[1], coords[0]], 16);
        }
    }

    function highlightMobileProperty(propertyId) {
        // Remove active class from all cards
        document.querySelectorAll('.mobile-property-card').forEach(card => {
            card.classList.remove('active');
        });
        
        // Add active class to selected card
        const selectedCard = document.querySelector(`[data-property-id="${propertyId}"]`);
        if (selectedCard) {
            selectedCard.classList.add('active');
        }
    }

    // Display markers
    function displayMobileMarkers(results) {
        markers.clearLayers();
        
        results.forEach(feature => {
            const property = feature.properties;
            const coords = feature.geometry.coordinates;
            
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div class="marker-icon" style="background-color: ${getPropertyColor(property)}">
                    <span class="marker-price">${formatPriceShort(property.price)}</span>
                </div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -36]
            });

            const marker = L.marker([coords[1], coords[0]], { icon: icon });
            
            marker.bindPopup(createMobilePopup(property), {
                className: 'mobile-popup',
                maxWidth: 280
            });

            marker.on('click', function() {
                selectMobileProperty(property.id);
                highlightMobileProperty(property.id);
            });

            markers.addLayer(marker);
        });

        if (results.length > 0) {
            map.fitBounds(markers.getBounds(), { padding: [20, 20] });
        }
    }

    function createMobilePopup(property) {
        return `
            <div class="mobile-popup-content">
                ${property.image ? `<img src="${property.image}" alt="${property.title}" class="w-full h-24 object-cover rounded mb-2">` : ''}
                <h3 class="font-semibold text-sm mb-1">${property.title}</h3>
                <div class="text-blue-600 font-bold text-sm mb-1">${formatPrice(property.price)}</div>
                <div class="text-xs text-gray-600 mb-2">
                    ${property.type} • ${property.bedrooms} bed • ${property.bathrooms} bath
                </div>
                <div class="flex gap-1 mb-2">
                    <span class="px-2 py-1 bg-gray-100 text-xs rounded">${property.furnishing_status}</span>
                    ${property.is_featured ? '<span class="px-2 py-1 bg-yellow-100 text-xs rounded">Featured</span>' : ''}
                </div>
                <a href="${property.url}" class="block w-full text-center bg-blue-600 text-white text-xs py-2 rounded hover:bg-blue-700">
                    View Details
                </a>
            </div>
        `;
    }

    // Utility functions
    function getPropertyColor(property) {
        if (property.is_featured) return '#f59e0b';
        if (property.type === 'commercial') return '#8b5cf6';
        return '#3b82f6';
    }

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

    // Loading states
    function showMobileLoading() {
        if (loading) loading.classList.remove('hidden');
        if (noResults) noResults.classList.add('hidden');
    }

    function hideMobileLoading() {
        if (loading) loading.classList.add('hidden');
    }

    function showMobileNoResults() {
        if (noResults) noResults.classList.remove('hidden');
    }

    // Touch gestures
    function initializeTouchGestures() {
        let touchStartX = 0;
        let touchStartY = 0;
        
        gestureOverlay.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            
            // Add touch feedback
            if (mobileMapConfig.enableHapticFeedback && navigator.vibrate) {
                navigator.vibrate(10);
            }
        });
        
        gestureOverlay.addEventListener('touchend', function(e) {
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;
            
            // Swipe detection
            if (Math.abs(deltaX) > Math.abs(deltaY)) {
                if (Math.abs(deltaX) > 50) {
                    if (deltaX > 0) {
                        // Swipe right - close bottom sheet
                        collapseBottomSheet();
                    } else {
                        // Swipe left - expand bottom sheet
                        expandBottomSheet();
                    }
                }
            }
        });
    }

    // Map tool functions
    function getCurrentLocation() {
        if (!navigator.geolocation) {
            showMobileNotification('Geolocation not supported', 'error');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                map.setView([lat, lng], 15);
                searchPropertiesByLocation(lat, lng);
                
                showMobileNotification('Location found!', 'success');
            },
            function(error) {
                showMobileNotification('Unable to get location', 'error');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000
            }
        );
    }

    function toggleLayers() {
        // Toggle between different map layers
        showMobileNotification('Layers toggled', 'info');
    }

    function showMobileNotification(message, type = 'info') {
        // Create mobile notification
        const notification = document.createElement('div');
        notification.className = `fixed top-4 left-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white text-center ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
        }`;
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Export for external use
    window.MobileMap = {
        map,
        markers,
        getState: () => mobileState,
        setState: (newState) => { mobileState = { ...mobileState, ...newState }; },
        expandBottomSheet,
        collapseBottomSheet,
        performMobileSearch,
        showMobileNotification
    };
});
</script>
@endpush
