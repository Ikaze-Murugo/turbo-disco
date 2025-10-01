{{-- Global Search Bar Component --}}
<div x-data="searchBar()" class="relative">
    <!-- Search Input -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        
        <input type="text" 
               x-model="query"
               @input="search()"
               @focus="showSuggestions = true"
               @blur="hideSuggestions()"
               @keydown.arrow-down="navigateSuggestions('down')"
               @keydown.arrow-up="navigateSuggestions('up')"
               @keydown.enter="selectSuggestion()"
               @keydown.escape="hideSuggestions()"
               placeholder="Search properties, locations..."
               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm">
        
        <!-- Loading Spinner -->
        <div x-show="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    <!-- Search Suggestions Dropdown -->
    <div x-show="showSuggestions && (suggestions.length > 0 || recentSearches.length > 0)" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
        
        <!-- Property Suggestions -->
        <template x-if="suggestions.length > 0">
            <div>
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                    Properties
                </div>
                <template x-for="(suggestion, index) in suggestions" :key="suggestion.id">
                    <a :href="`/listings/${suggestion.id}`" 
                       @click="selectSuggestion(suggestion)"
                       class="flex items-center px-4 py-2 text-sm text-gray-900 hover:bg-gray-100 cursor-pointer"
                       :class="{ 'bg-gray-100': selectedIndex === index }">
                        <div class="flex-shrink-0 h-10 w-10">
                            <img x-show="suggestion.image" 
                                 :src="suggestion.image" 
                                 :alt="suggestion.title"
                                 class="h-10 w-10 rounded-md object-cover">
                            <div x-show="!suggestion.image" 
                                 class="h-10 w-10 rounded-md bg-gray-200 flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm font-medium text-gray-900" x-text="suggestion.title"></div>
                            <div class="text-sm text-gray-500" x-text="suggestion.address"></div>
                            <div class="text-sm text-primary-600 font-medium" x-text="`RWF ${suggestion.price.toLocaleString()}`"></div>
                        </div>
                    </a>
                </template>
            </div>
        </template>

        <!-- Recent Searches -->
        <template x-if="suggestions.length === 0 && recentSearches.length > 0">
            <div>
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                    Recent Searches
                </div>
                <template x-for="(search, index) in recentSearches" :key="index">
                    <button @click="selectRecentSearch(search)"
                            class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-900 hover:bg-gray-100 cursor-pointer"
                            :class="{ 'bg-gray-100': selectedIndex === index }">
                        <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-text="search"></span>
                    </button>
                </template>
            </div>
        </template>

        <!-- No Results -->
        <template x-if="suggestions.length === 0 && recentSearches.length === 0 && query.length > 2">
            <div class="px-4 py-2 text-sm text-gray-500">
                No properties found for "<span x-text="query"></span>"
            </div>
        </template>
    </div>
</div>

<script>
function searchBar() {
    return {
        query: '',
        suggestions: [],
        recentSearches: [],
        loading: false,
        showSuggestions: false,
        selectedIndex: -1,
        searchTimeout: null,

        init() {
            // Load recent searches from localStorage
            this.recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
        },

        search() {
            // Clear previous timeout
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            // Reset selected index
            this.selectedIndex = -1;

            // If query is too short, clear suggestions
            if (this.query.length < 2) {
                this.suggestions = [];
                return;
            }

            // Debounce search
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, 300);
        },

        async performSearch() {
            this.loading = true;
            
            try {
                const response = await fetch(`/api/properties/suggestions?query=${encodeURIComponent(this.query)}`);
                const data = await response.json();
                
                this.suggestions = data.slice(0, 5); // Limit to 5 suggestions
            } catch (error) {
                console.error('Search error:', error);
                this.suggestions = [];
            } finally {
                this.loading = false;
            }
        },

        selectSuggestion(suggestion = null) {
            if (suggestion) {
                // Add to recent searches
                this.addToRecentSearches(suggestion.title);
                // Navigate to property
                window.location.href = `/listings/${suggestion.id}`;
            } else if (this.selectedIndex >= 0 && this.suggestions[this.selectedIndex]) {
                const selected = this.suggestions[this.selectedIndex];
                this.addToRecentSearches(selected.title);
                window.location.href = `/listings/${selected.id}`;
            }
            
            this.hideSuggestions();
        },

        selectRecentSearch(search) {
            this.query = search;
            this.hideSuggestions();
            // Trigger search or navigate to search results
            window.location.href = `/listings/search?search=${encodeURIComponent(search)}`;
        },

        navigateSuggestions(direction) {
            const maxIndex = Math.max(this.suggestions.length - 1, this.recentSearches.length - 1);
            
            if (direction === 'down') {
                this.selectedIndex = Math.min(this.selectedIndex + 1, maxIndex);
            } else if (direction === 'up') {
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
            }
        },

        hideSuggestions() {
            // Delay hiding to allow click events to fire
            setTimeout(() => {
                this.showSuggestions = false;
                this.selectedIndex = -1;
            }, 150);
        },

        addToRecentSearches(search) {
            // Remove if already exists
            this.recentSearches = this.recentSearches.filter(s => s !== search);
            // Add to beginning
            this.recentSearches.unshift(search);
            // Keep only last 5
            this.recentSearches = this.recentSearches.slice(0, 5);
            // Save to localStorage
            localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches));
        }
    }
}
</script>
