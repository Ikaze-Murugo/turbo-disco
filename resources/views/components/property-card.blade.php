{{-- Enhanced Property Card Component - Modern Webflow-inspired design --}}
@props([
    'property' => null,
    'showCarousel' => true,
    'enableFavorites' => false,
    'enableComparison' => false,
    'showActions' => true,
    'class' => ''
])

@php
    // Determine the correct route based on user role and property ownership
    $showRoute = 'properties.public.show';
    if (auth()->check()) {
        if (auth()->user()->isLandlord() && $property->landlord_id === auth()->id()) {
            $showRoute = 'properties.show';
        } elseif (auth()->user()->isAdmin()) {
            $showRoute = 'properties.show';
        }
    }
@endphp

<article class="property-card-enhanced group cursor-pointer {{ $class }}" 
         onclick="window.location.href='{{ route($showRoute, $property) }}'"
         role="button"
         tabindex="0"
         aria-label="View {{ $property->title }} property details"
         onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();window.location.href='{{ route($showRoute, $property) }}'}">
    
    <!-- Property Image Section with Carousel -->
    <div class="property-image-container relative overflow-hidden rounded-t-xl h-48">
        @if($property->images->count() > 0)
            <div class="property-image-carousel relative">
                @if($showCarousel && $property->images->count() > 1)
                    <!-- Image Carousel -->
                    <div class="carousel-container relative">
                        <div class="carousel-images flex transition-transform duration-300 ease-in-out" 
                             x-data="propertyCarousel({{ $property->images->count() }})"
                             x-init="init()">
                            @foreach($property->images as $index => $image)
                                <div class="carousel-slide w-full flex-shrink-0" 
                                     x-show="currentSlide === {{ $index }}"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95">
                                    <img src="{{ Storage::url($image->path) }}" 
                                         alt="{{ $property->title }} - Image {{ $index + 1 }}"
                                         class="property-card-image w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
                                         loading="lazy">
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Carousel Navigation Dots -->
                        @if($property->images->count() > 1)
                            <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-1">
                                @foreach($property->images as $index => $image)
                                    <button class="w-2 h-2 rounded-full transition-all duration-200"
                                            :class="currentSlide === {{ $index }} ? 'bg-white' : 'bg-white/50'"
                                            @click.stop="goToSlide({{ $index }})"
                                            aria-label="Go to image {{ $index + 1 }}">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Single Image -->
                    <img src="{{ Storage::url($property->images->first()->path) }}" 
                         alt="{{ $property->title }} property image"
                         class="property-card-image w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
                         loading="lazy">
                @endif
            </div>
        @else
            <!-- Fallback Image -->
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center" role="img" aria-label="No property image available">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        @endif
        
        <!-- Price Badge -->
        <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold text-primary-600 shadow-sm" 
             aria-label="Price: RWF {{ number_format($property->price) }}">
            RWF {{ number_format($property->price) }}
        </div>
        
        <!-- Status Badge -->
        @if($property->status === 'active')
            @if($property->version_status === 'original' && $property->hasPendingUpdates())
                <div class="absolute top-4 left-4 bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-medium" 
                     aria-label="Property has pending updates">
                    Pending Updates
                </div>
            @else
                <div class="absolute top-4 left-4 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium" 
                     aria-label="Property is available">
                    Available
                </div>
            @endif
        @elseif($property->status === 'pending')
            @if($property->version_status === 'pending_update')
                <div class="absolute top-4 left-4 bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-medium" 
                     aria-label="Property update pending approval">
                    Update Pending
                </div>
            @else
                <div class="absolute top-4 left-4 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-medium" 
                     aria-label="Property is pending approval">
                    Pending
                </div>
            @endif
        @elseif($property->status === 'featured')
            <div class="absolute top-4 left-4 bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-medium" 
                 aria-label="Featured property">
                Featured
            </div>
        @endif
        
    </div>
    
    <!-- Property Details Section -->
    <div class="property-details p-4 flex flex-col h-full">
        <!-- Title -->
        <h3 class="text-lg font-semibold mb-2 text-gray-900 group-hover:text-primary-600 transition-colors">
            {{ $property->title }}
        </h3>

        <!-- Landlord (link to profile) -->
        <p class="text-sm text-gray-600 mb-2">
            By 
            <a href="{{ route('landlords.show', ['user' => $property->landlord_id, 'slug' => str($property->landlord->business_name ?: $property->landlord->name)->slug('-')]) }}"
               class="text-primary-600 hover:text-primary-700 underline-offset-2 hover:underline"
               onclick="event.stopPropagation()">
                {{ $property->landlord->business_name ?: $property->landlord->name }}
            </a>
        </p>
        
        <!-- Location -->
        <p class="text-gray-600 mb-3 flex items-center text-sm">
            <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="truncate">{{ $property->neighborhood }}, {{ $property->location }}</span>
        </p>
        
        <!-- Property Features -->
        <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
            <div class="flex items-center space-x-3">
                <span class="flex items-center" aria-label="{{ $property->bedrooms }} bedrooms">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    </svg>
                    {{ $property->bedrooms }} bed
                </span>
                <span class="flex items-center" aria-label="{{ $property->bathrooms }} bathrooms">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7"></path>
                    </svg>
                    {{ $property->bathrooms }} bath
                </span>
                @if($property->area)
                    <span class="flex items-center" aria-label="{{ $property->area }} square meters">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        {{ $property->area }} m²
                    </span>
                @endif
            </div>
            <span class="badge badge-primary capitalize">{{ $property->type }}</span>
        </div>
        
        <!-- Price -->
        <div class="text-lg font-bold text-primary-600 mb-3">
            RWF {{ number_format($property->price) }}
        </div>
        
        <!-- Quick Actions (only for renters) -->
        @if(auth()->check() && auth()->user()->isRenter())
            <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-100">
                <div class="flex items-center space-x-2">
                    <button class="favorite-btn p-2 rounded-full bg-gray-100 hover:bg-red-100 transition-colors"
                            onclick="event.stopPropagation(); toggleFavorite({{ $property->id }})"
                            data-property-id="{{ $property->id }}"
                            data-favorited="{{ $property->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}"
                            aria-label="Toggle favorite">
                        <svg class="w-5 h-5 transition-colors {{ $property->isFavoritedBy(auth()->id()) ? 'text-red-500 fill-current' : 'text-gray-600' }}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                    @if($enableComparison)
                        <button class="compare-btn p-2 rounded-full bg-gray-100 hover:bg-blue-100 transition-colors"
                                onclick="event.stopPropagation(); addToComparison({{ $property->id }})"
                                aria-label="Add to comparison">
                            <svg class="w-5 h-5 text-gray-600 hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </button>
                    @endif
                </div>
                <span class="text-xs text-gray-500">Click to view details</span>
            </div>
        @endif
    </div>
</article>

<script>
// Property Carousel Alpine.js Component
function propertyCarousel(totalSlides) {
    return {
        currentSlide: 0,
        totalSlides: totalSlides,
        
        init() {
            // Auto-advance carousel every 5 seconds
            setInterval(() => {
                this.nextSlide();
            }, 5000);
        },
        
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        },
        
        prevSlide() {
            this.currentSlide = this.currentSlide === 0 ? this.totalSlides - 1 : this.currentSlide - 1;
        },
        
        goToSlide(index) {
            this.currentSlide = index;
        }
    }
}

// Favorites functionality - using existing system
function toggleFavorite(propertyId) {
    const favoriteBtn = document.querySelector(`[data-property-id="${propertyId}"]`);
    if (!favoriteBtn) return;
    
    const isCurrentlyFavorited = favoriteBtn.getAttribute('data-favorited') === 'true';
    const method = isCurrentlyFavorited ? 'DELETE' : 'POST';
    
    fetch(`/properties/${propertyId}/favorite`, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button state
            const newFavoritedState = !isCurrentlyFavorited;
            favoriteBtn.setAttribute('data-favorited', newFavoritedState.toString());
            
            // Update visual state
            const icon = favoriteBtn.querySelector('svg');
            if (newFavoritedState) {
                icon.classList.add('text-red-500', 'fill-current');
                icon.classList.remove('text-gray-600');
            } else {
                icon.classList.remove('text-red-500', 'fill-current');
                icon.classList.add('text-gray-600');
            }
            
            // Show success message
            if (typeof showNotification === 'function') {
                showNotification(data.message, 'success');
            }
        } else {
            // Show error message
            if (typeof showNotification === 'function') {
                showNotification(data.message, 'error');
            } else {
                alert(data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error toggling favorite:', error);
        if (typeof showNotification === 'function') {
            showNotification('Failed to update favorite. Please try again.', 'error');
        } else {
            alert('Failed to update favorite. Please try again.');
        }
    });
}

// Check if property is favorited (for Alpine.js)
function isFavorited(propertyId) {
    const favoriteBtn = document.querySelector(`[data-property-id="${propertyId}"]`);
    return favoriteBtn ? favoriteBtn.getAttribute('data-favorited') === 'true' : false;
}

// Comparison functionality
function addToComparison(propertyId) {
    // Add to comparison logic
    console.log('Adding property to comparison:', propertyId);
}
</script>