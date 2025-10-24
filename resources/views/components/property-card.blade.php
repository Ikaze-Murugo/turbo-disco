{{-- Enhanced Property Card Component - Modern Webflow-inspired design --}}
@props([
    'property' => null,
    'showCarousel' => true,
    'enableFavorites' => false,
    'enableComparison' => false,
    'showActions' => true,
    'layout' => 'grid',
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

<article class="property-card-modern group relative bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden {{ $layout === 'list' ? 'property-card-list' : 'property-card-grid' }} {{ $class }}" 
         onclick="window.location.href='{{ route($showRoute, $property) }}'"
         role="button"
         tabindex="0"
         aria-label="View {{ $property->title }} property details"
         onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();window.location.href='{{ route($showRoute, $property) }}'}">
    
    <!-- Property Image Section with Enhanced Styling -->
    <div class="relative aspect-[4/3] overflow-hidden">
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
        
        <!-- Enhanced Badges -->
        <div class="absolute top-3 left-3 flex gap-2">
            @if($property->is_featured)
                <span class="px-3 py-1 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-semibold rounded-full shadow-lg flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    Featured
                </span>
            @endif
            
            @if($property->created_at->diffInDays(now()) < 7)
                <span class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full shadow-lg">
                    New
                </span>
            @endif
        </div>
        
        <!-- Quick Actions -->
        @if($showActions && auth()->check())
            <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                @if(auth()->user()->isRenter())
                    {{-- Favorite Button --}}
                    <button 
                        onclick="event.stopPropagation(); toggleFavorite({{ $property->id }})"
                        class="favorite-btn p-2 bg-white/90 backdrop-blur-sm rounded-full shadow-lg hover:bg-white transition-all duration-200 {{ $property->isFavoritedBy(auth()->id()) ? 'text-red-500' : 'text-gray-600' }}"
                        data-property-id="{{ $property->id }}"
                        title="Add to favorites">
                        <svg class="w-5 h-5" fill="{{ $property->isFavoritedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                    
                    {{-- Share Button --}}
                    <button 
                        onclick="event.stopPropagation(); shareProperty({{ $property->id }})"
                        class="p-2 bg-white/90 backdrop-blur-sm rounded-full shadow-lg hover:bg-white transition-all duration-200 text-gray-600"
                        title="Share property">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                        </svg>
                    </button>
                    
                    {{-- Compare Button --}}
                    @if($enableComparison)
                        <button 
                            onclick="event.stopPropagation(); addToCompare({{ $property->id }})"
                            class="p-2 bg-white/90 backdrop-blur-sm rounded-full shadow-lg hover:bg-white transition-all duration-200 text-gray-600"
                            title="Add to compare">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </button>
                    @endif
                @endif
            </div>
        @endif
        
        <!-- Price Badge -->
        <div class="absolute bottom-3 left-3">
            <div class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg shadow-lg">
                <div class="text-xs font-medium opacity-90">Price</div>
                <div class="text-lg font-bold">{{ number_format($property->price) }} RWF</div>
                <div class="text-xs opacity-90">per month</div>
            </div>
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
    <div class="p-5">
        <!-- Title -->
        <a href="{{ route($showRoute, $property) }}" class="block group-hover:text-blue-600 transition-colors">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                {{ $property->title }}
            </h3>
        </a>

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
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="flex items-center text-sm text-gray-700">
                <svg class="w-5 h-5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium">{{ $property->bedrooms }}</span>
                <span class="ml-1 text-gray-500">beds</span>
            </div>
            
            <div class="flex items-center text-sm text-gray-700">
                <svg class="w-5 h-5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                </svg>
                <span class="font-medium">{{ $property->bathrooms }}</span>
                <span class="ml-1 text-gray-500">baths</span>
            </div>
            
            @if($property->area)
                <div class="flex items-center text-sm text-gray-700">
                    <svg class="w-5 h-5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                    <span class="font-medium">{{ $property->area }}</span>
                    <span class="ml-1 text-gray-500">m²</span>
                </div>
            @endif
        </div>
        
        <!-- Property Type and Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                {{ ucfirst($property->type) }}
            </span>
            
            <a href="{{ route($showRoute, $property) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                View Details
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
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

// Share functionality
function shareProperty(propertyId) {
    if (navigator.share) {
        navigator.share({
            title: 'Check out this property',
            url: window.location.origin + '/properties/' + propertyId
        });
    } else {
        // Fallback: copy to clipboard
        const url = window.location.origin + '/properties/' + propertyId;
        navigator.clipboard.writeText(url).then(() => {
            if (typeof showNotification === 'function') {
                showNotification('Link copied to clipboard!', 'success');
            } else {
                alert('Link copied to clipboard!');
            }
        });
    }
}

// Comparison functionality
function addToCompare(propertyId) {
    let compareList = JSON.parse(localStorage.getItem('compareProperties') || '[]');
    
    if (compareList.includes(propertyId)) {
        if (typeof showNotification === 'function') {
            showNotification('Property already in comparison list', 'info');
        } else {
            alert('Property already in comparison list');
        }
        return;
    }
    
    if (compareList.length >= 4) {
        if (typeof showNotification === 'function') {
            showNotification('You can only compare up to 4 properties', 'warning');
        } else {
            alert('You can only compare up to 4 properties');
        }
        return;
    }
    
    compareList.push(propertyId);
    localStorage.setItem('compareProperties', JSON.stringify(compareList));
    
    // Update compare counter if it exists
    const compareCounter = document.getElementById('compare-count');
    if (compareCounter) {
        compareCounter.textContent = compareList.length;
    }
    
    if (typeof showNotification === 'function') {
        showNotification('Property added to comparison', 'success');
    } else {
        alert('Property added to comparison');
    }
}
</script>