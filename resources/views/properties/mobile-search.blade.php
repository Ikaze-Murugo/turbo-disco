@extends('layouts.app')

@section('title', 'Search Properties - Find Your Perfect Home')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Mobile Search Header -->
    <div class="mobile-search-bar sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <button 
                id="back-btn" 
                class="p-2 text-gray-600 hover:text-gray-800"
                onclick="history.back()"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    id="mobile-search-input" 
                    placeholder="Search properties..." 
                    class="mobile-search-input"
                    value="{{ request('q', '') }}"
                >
                <button 
                    id="mobile-search-btn" 
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 p-1 text-gray-500 hover:text-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
            
            <button 
                id="filters-btn" 
                class="p-2 text-gray-600 hover:text-gray-800"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Quick Filters -->
    <div class="quick-filters p-4 bg-white border-b border-gray-200">
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

    <!-- Search Results -->
    <div class="search-results">
        <!-- Loading State -->
        <div id="mobile-loading" class="mobile-loading hidden">
            <div class="mobile-loading-spinner"></div>
            <div class="mobile-loading-text">Searching properties...</div>
        </div>

        <!-- Results List -->
        <div id="mobile-results-list" class="p-4 space-y-4">
            <!-- Results will be populated here -->
        </div>

        <!-- No Results -->
        <div id="mobile-no-results" class="mobile-empty hidden">
            <svg class="mobile-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 6.291A7.962 7.962 0 0012 5c-2.34 0-4.29 1.009-5.824 2.709"></path>
            </svg>
            <div class="mobile-empty-title">No properties found</div>
            <div class="mobile-empty-description">Try adjusting your search criteria or filters</div>
        </div>
    </div>

    <!-- Advanced Filters Modal -->
    <div id="mobile-filters" class="mobile-filters">
        <div class="mobile-filters-header">
            <h3 class="mobile-filters-title">Filters</h3>
            <button id="filters-close" class="mobile-filters-close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mobile-filters-content">
            <!-- Price Range -->
            <div class="mobile-filter-section">
                <label class="mobile-filter-label">Price Range (RWF)</label>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-600 mb-2 block">Min Price</label>
                        <input 
                            type="number" 
                            id="filter-min-price" 
                            placeholder="0" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        >
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 mb-2 block">Max Price</label>
                        <input 
                            type="number" 
                            id="filter-max-price" 
                            placeholder="No limit" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        >
                    </div>
                </div>
            </div>

            <!-- Bedrooms -->
            <div class="mobile-filter-section">
                <label class="mobile-filter-label">Bedrooms</label>
                <div class="mobile-filter-chips">
                    <button class="mobile-filter-chip active" data-value="">Any</button>
                    <button class="mobile-filter-chip" data-value="1">1+</button>
                    <button class="mobile-filter-chip" data-value="2">2+</button>
                    <button class="mobile-filter-chip" data-value="3">3+</button>
                    <button class="mobile-filter-chip" data-value="4">4+</button>
                    <button class="mobile-filter-chip" data-value="5">5+</button>
                </div>
            </div>

            <!-- Bathrooms -->
            <div class="mobile-filter-section">
                <label class="mobile-filter-label">Bathrooms</label>
                <div class="mobile-filter-chips">
                    <button class="mobile-filter-chip active" data-value="">Any</button>
                    <button class="mobile-filter-chip" data-value="1">1+</button>
                    <button class="mobile-filter-chip" data-value="2">2+</button>
                    <button class="mobile-filter-chip" data-value="3">3+</button>
                    <button class="mobile-filter-chip" data-value="4">4+</button>
                </div>
            </div>

            <!-- Furnishing Status -->
            <div class="mobile-filter-section">
                <label class="mobile-filter-label">Furnishing</label>
                <div class="mobile-filter-chips">
                    <button class="mobile-filter-chip active" data-value="">Any</button>
                    <button class="mobile-filter-chip" data-value="furnished">Furnished</button>
                    <button class="mobile-filter-chip" data-value="semi-furnished">Semi-furnished</button>
                    <button class="mobile-filter-chip" data-value="unfurnished">Unfurnished</button>
                </div>
            </div>

            <!-- Features -->
            <div class="mobile-filter-section">
                <label class="mobile-filter-label">Features</label>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" id="filter-featured" class="mr-3">
                        <span class="text-sm">Featured Only</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="filter-parking" class="mr-3">
                        <span class="text-sm">Parking</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="filter-garden" class="mr-3">
                        <span class="text-sm">Garden</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="filter-pool" class="mr-3">
                        <span class="text-sm">Swimming Pool</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Filter Actions -->
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex gap-3">
                <button id="clear-filters" class="mobile-btn mobile-btn-secondary flex-1">
                    Clear All
                </button>
                <button id="apply-filters" class="mobile-btn mobile-btn-primary flex-1">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="mobile-nav">
        <a href="{{ route('home') }}" class="mobile-nav-item">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="label">Home</span>
        </a>
        
        <a href="{{ route('properties.public.index') }}" class="mobile-nav-item active">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span class="label">Search</span>
        </a>
        
        <a href="{{ route('properties.map') }}" class="mobile-nav-item">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.632A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
            <span class="label">Map</span>
        </a>
        
        @auth
            <a href="{{ route('favorites.index') }}" class="mobile-nav-item">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <span class="label">Favorites</span>
            </a>
        @endauth
        
        <a href="{{ route('profile.show') }}" class="mobile-nav-item">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="label">Profile</span>
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Search State
    let mobileSearchState = {
        currentFilters: {
            type: '',
            min_price: '',
            max_price: '',
            bedrooms: '',
            bathrooms: '',
            furnishing_status: '',
            featured: false,
            parking: false,
            garden: false,
            pool: false
        },
        isLoading: false,
        currentPage: 1,
        hasMore: true
    };

    // DOM Elements
    const searchInput = document.getElementById('mobile-search-input');
    const searchBtn = document.getElementById('mobile-search-btn');
    const filtersBtn = document.getElementById('filters-btn');
    const filtersModal = document.getElementById('mobile-filters');
    const filtersClose = document.getElementById('filters-close');
    const resultsList = document.getElementById('mobile-results-list');
    const loading = document.getElementById('mobile-loading');
    const noResults = document.getElementById('mobile-no-results');
    const filterChips = document.querySelectorAll('.filter-chip');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const clearFiltersBtn = document.getElementById('clear-filters');

    // Initialize mobile search
    initializeMobileSearch();

    function initializeMobileSearch() {
        // Search functionality
        searchBtn.addEventListener('click', performMobileSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performMobileSearch();
            }
        });

        // Filter chips
        filterChips.forEach(chip => {
            chip.addEventListener('click', function() {
                // Remove active class from all chips
                filterChips.forEach(c => c.classList.remove('active'));
                // Add active class to clicked chip
                this.classList.add('active');
                
                // Update type filter
                const filterType = this.id.replace('filter-', '');
                mobileSearchState.currentFilters.type = filterType === 'all' ? '' : filterType;
                
                // Perform search
                performMobileSearch();
            });
        });

        // Filters modal
        filtersBtn.addEventListener('click', showFiltersModal);
        filtersClose.addEventListener('click', hideFiltersModal);
        applyFiltersBtn.addEventListener('click', applyFilters);
        clearFiltersBtn.addEventListener('click', clearFilters);

        // Filter chip interactions
        document.querySelectorAll('.mobile-filter-chip').forEach(chip => {
            chip.addEventListener('click', function() {
                const section = this.closest('.mobile-filter-section');
                const chips = section.querySelectorAll('.mobile-filter-chip');
                
                // Remove active class from all chips in this section
                chips.forEach(c => c.classList.remove('active'));
                // Add active class to clicked chip
                this.classList.add('active');
            });
        });

        // Load initial results
        performMobileSearch();
    }

    // Search functions
    function performMobileSearch() {
        if (mobileSearchState.isLoading) return;
        
        mobileSearchState.isLoading = true;
        mobileSearchState.currentPage = 1;
        mobileSearchState.hasMore = true;
        
        showLoading();
        
        const params = new URLSearchParams();
        
        // Add search query
        if (searchInput.value.trim()) {
            params.append('q', searchInput.value.trim());
        }
        
        // Add filters
        Object.keys(mobileSearchState.currentFilters).forEach(key => {
            if (mobileSearchState.currentFilters[key]) {
                params.append(key, mobileSearchState.currentFilters[key]);
            }
        });
        
        params.append('page', mobileSearchState.currentPage);
        
        fetch(`/api/search/advanced?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMobileResults(data.data.properties);
                    mobileSearchState.hasMore = data.data.pagination.has_more;
                } else {
                    showNoResults();
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                showNoResults();
            })
            .finally(() => {
                mobileSearchState.isLoading = false;
                hideLoading();
            });
    }

    function displayMobileResults(properties) {
        if (!properties || properties.length === 0) {
            showNoResults();
            return;
        }
        
        resultsList.innerHTML = '';
        
        properties.forEach(property => {
            const propertyCard = createMobilePropertyCard(property);
            resultsList.appendChild(propertyCard);
        });
    }

    function createMobilePropertyCard(property) {
        const card = document.createElement('div');
        card.className = 'mobile-property-card';
        
        card.innerHTML = `
            <div class="mobile-property-image-container">
                ${property.images && property.images.length > 0 ? 
                    `<img src="${property.images[0].url}" alt="${property.title}" class="mobile-property-image" loading="lazy">` : 
                    '<div class="mobile-property-image bg-gray-200 flex items-center justify-center"><svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>'
                }
                ${property.is_featured ? '<div class="mobile-property-badge">Featured</div>' : ''}
                <div class="mobile-property-actions">
                    <button class="mobile-property-action" onclick="toggleFavorite(${property.id})">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                    <button class="mobile-property-action" onclick="shareProperty(${property.id})">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="mobile-property-content">
                <h3 class="mobile-property-title">${property.title}</h3>
                <div class="mobile-property-price">${formatPrice(property.price)}</div>
                <div class="mobile-property-details">
                    ${property.type} • ${property.bedrooms} bed • ${property.bathrooms} bath
                    ${property.area ? ` • ${property.area} m²` : ''}
                </div>
                <div class="mobile-property-features">
                    <span class="mobile-property-feature">${property.furnishing_status}</span>
                    ${property.amenities && property.amenities.length > 0 ? 
                        property.amenities.slice(0, 2).map(amenity => 
                            `<span class="mobile-property-feature">${amenity.name}</span>`
                        ).join('') : ''
                    }
                </div>
                <div class="mobile-property-footer">
                    <div class="mobile-property-location">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        ${property.location}
                    </div>
                    <a href="/properties/${property.id}" class="text-blue-600 text-sm font-medium">
                        View Details
                    </a>
                </div>
            </div>
        `;
        
        return card;
    }

    // Filter functions
    function showFiltersModal() {
        filtersModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function hideFiltersModal() {
        filtersModal.classList.remove('show');
        document.body.style.overflow = '';
    }

    function applyFilters() {
        // Collect filter values
        mobileSearchState.currentFilters.min_price = document.getElementById('filter-min-price').value;
        mobileSearchState.currentFilters.max_price = document.getElementById('filter-max-price').value;
        mobileSearchState.currentFilters.bedrooms = document.querySelector('.mobile-filter-chip[data-value]').dataset.value || '';
        mobileSearchState.currentFilters.bathrooms = document.querySelector('.mobile-filter-chip[data-value]').dataset.value || '';
        mobileSearchState.currentFilters.furnishing_status = document.querySelector('.mobile-filter-chip[data-value]').dataset.value || '';
        mobileSearchState.currentFilters.featured = document.getElementById('filter-featured').checked;
        mobileSearchState.currentFilters.parking = document.getElementById('filter-parking').checked;
        mobileSearchState.currentFilters.garden = document.getElementById('filter-garden').checked;
        mobileSearchState.currentFilters.pool = document.getElementById('filter-pool').checked;
        
        hideFiltersModal();
        performMobileSearch();
    }

    function clearFilters() {
        // Reset all filter inputs
        document.getElementById('filter-min-price').value = '';
        document.getElementById('filter-max-price').value = '';
        document.getElementById('filter-featured').checked = false;
        document.getElementById('filter-parking').checked = false;
        document.getElementById('filter-garden').checked = false;
        document.getElementById('filter-pool').checked = false;
        
        // Reset filter chips
        document.querySelectorAll('.mobile-filter-chip').forEach(chip => {
            chip.classList.remove('active');
        });
        document.querySelectorAll('.mobile-filter-chip[data-value=""]').forEach(chip => {
            chip.classList.add('active');
        });
        
        // Reset state
        mobileSearchState.currentFilters = {
            type: mobileSearchState.currentFilters.type, // Keep current type filter
            min_price: '',
            max_price: '',
            bedrooms: '',
            bathrooms: '',
            furnishing_status: '',
            featured: false,
            parking: false,
            garden: false,
            pool: false
        };
        
        performMobileSearch();
    }

    // UI functions
    function showLoading() {
        loading.classList.remove('hidden');
        noResults.classList.add('hidden');
    }

    function hideLoading() {
        loading.classList.add('hidden');
    }

    function showNoResults() {
        noResults.classList.remove('hidden');
        resultsList.innerHTML = '';
    }

    // Utility functions
    function formatPrice(price) {
        return new Intl.NumberFormat('en-RW', {
            style: 'currency',
            currency: 'RWF',
            minimumFractionDigits: 0
        }).format(price);
    }

    // Global functions for property actions
    window.toggleFavorite = function(propertyId) {
        // Implement favorite toggle
        console.log('Toggle favorite:', propertyId);
    };

    window.shareProperty = function(propertyId) {
        // Implement property sharing
        console.log('Share property:', propertyId);
    };
});
</script>
@endpush
