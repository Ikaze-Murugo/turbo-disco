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

<article class="property-card-modern group relative bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden {{ $layout === 'list' ? 'property-card-list' : 'property-card-grid' }} {{ $class }} mobile-optimized" 
         onclick="window.location.href='{{ route($showRoute, $property) }}'"
         role="button"
         tabindex="0"
         aria-label="View {{ $property->title }} property details"
         onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();window.location.href='{{ route($showRoute, $property) }}'}">
    
    <!-- Property Image Section with Enhanced Styling -->
    <div class="relative aspect-[4/3] md:aspect-[4/3] sm:aspect-[3/2] overflow-hidden">
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
        
        <!-- Unified Badge System -->
        <div class="absolute top-3 left-3 flex flex-col gap-1 max-w-[120px]">
            @php
                $badges = [];
                
                // Priority 1: Featured (highest priority)
                if($property->is_featured) {
                    $badges[] = [
                        'text' => 'Featured',
                        'class' => 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white',
                        'icon' => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>'
                    ];
                }
                
                // Priority 2: Status badges
                if($property->status === 'active') {
                    if($property->version_status === 'original' && $property->hasPendingUpdates()) {
                        $badges[] = ['text' => 'Pending Updates', 'class' => 'bg-orange-500 text-white', 'icon' => ''];
                    } else {
                        $badges[] = ['text' => 'Available', 'class' => 'bg-green-500 text-white', 'icon' => ''];
                    }
                } elseif($property->status === 'pending') {
                    if($property->version_status === 'pending_update') {
                        $badges[] = ['text' => 'Update Pending', 'class' => 'bg-blue-500 text-white', 'icon' => ''];
                    } else {
                        $badges[] = ['text' => 'Pending', 'class' => 'bg-yellow-500 text-white', 'icon' => ''];
                    }
                }
                
                // Priority 3: New badge (only if not featured to avoid clutter)
                if(!$property->is_featured && $property->created_at->diffInDays(now()) < 7) {
                    $badges[] = ['text' => 'New', 'class' => 'bg-green-500 text-white', 'icon' => ''];
                }
                
                // Limit to 2 badges maximum to prevent clutter
                $badges = array_slice($badges, 0, 2);
            @endphp
            
            @foreach($badges as $badge)
                <span class="px-2 py-1 {{ $badge['class'] }} text-xs font-medium rounded-full shadow-sm flex items-center gap-1 truncate">
                    @if($badge['icon'])
                        {!! $badge['icon'] !!}
                    @endif
                    <span class="truncate">{{ $badge['text'] }}</span>
                </span>
            @endforeach
        </div>
        
        <!-- Quick Actions -->
        @if($showActions)
            <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
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
                        data-property-id="{{ $property->id }}"
                        data-action="compare"
                        class="p-2 bg-white/90 backdrop-blur-sm rounded-full shadow-lg hover:bg-white transition-all duration-200 text-gray-600"
                        title="Add to compare">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </button>
                @endif
                
                {{-- Favorite Button (only for authenticated renters) --}}
                @if(auth()->check() && auth()->user()->isRenter() && $enableFavorites)
                    <button 
                        onclick="event.stopPropagation(); toggleFavorite({{ $property->id }})"
                        class="favorite-btn p-2 bg-white/90 backdrop-blur-sm rounded-full shadow-lg hover:bg-white transition-all duration-200 {{ $property->isFavoritedBy(auth()->id()) ? 'text-red-500' : 'text-gray-600' }}"
                        data-property-id="{{ $property->id }}"
                        title="Add to favorites">
                        <svg class="w-5 h-5" fill="{{ $property->isFavoritedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
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
        
        <!-- Status badges are now handled in the unified badge system above -->
        
    </div>
    
    <!-- Property Details Section -->
    <div class="p-3 md:p-5">
        <!-- Title -->
        <a href="{{ route($showRoute, $property) }}" class="block group-hover:text-blue-600 transition-colors">
            <h3 class="text-sm md:text-lg font-semibold text-gray-900 mb-1 md:mb-2 line-clamp-2">
                {{ $property->title }}
            </h3>
        </a>

        <!-- Landlord (link to profile) -->
        <p class="text-xs md:text-sm text-gray-600 mb-1 md:mb-2">
            By 
            <a href="{{ route('landlords.show', ['user' => $property->landlord_id, 'slug' => str($property->landlord->business_name ?: $property->landlord->name)->slug('-')]) }}"
               class="text-primary-600 hover:text-primary-700 underline-offset-2 hover:underline"
               onclick="event.stopPropagation()">
                {{ $property->landlord->business_name ?: $property->landlord->name }}
            </a>
        </p>
        
        <!-- Location -->
        <p class="text-gray-600 mb-2 md:mb-3 flex items-center text-xs md:text-sm">
            <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="truncate">{{ $property->neighborhood }}, {{ $property->location }}</span>
        </p>
        
        <!-- Property Features -->
        <div class="grid grid-cols-3 gap-2 md:gap-3 mb-3 md:mb-4">
            <div class="flex items-center text-xs md:text-sm text-gray-700">
                <svg class="w-4 h-4 md:w-5 md:h-5 mr-1 md:mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium">{{ $property->bedrooms }}</span>
                <span class="ml-1 text-gray-500">beds</span>
            </div>
            
            <div class="flex items-center text-xs md:text-sm text-gray-700">
                <svg class="w-4 h-4 md:w-5 md:h-5 mr-1 md:mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                </svg>
                <span class="font-medium">{{ $property->bathrooms }}</span>
                <span class="ml-1 text-gray-500">baths</span>
            </div>
            
            @if($property->area)
                <div class="flex items-center text-xs md:text-sm text-gray-700">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-1 md:mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                    <span class="font-medium">{{ $property->area }}</span>
                    <span class="ml-1 text-gray-500">m²</span>
                </div>
            @endif
        </div>
        
        <!-- Property Type and Actions -->
        <div class="flex items-center justify-between pt-3 md:pt-4 border-t border-gray-100">
            <span class="inline-flex items-center px-2 md:px-3 py-0.5 md:py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                {{ ucfirst($property->type) }}
            </span>
            
            <a href="{{ route($showRoute, $property) }}" class="inline-flex items-center text-xs md:text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                View Details
                <svg class="w-3 h-3 md:w-4 md:h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            url: window.location.origin + '/listings/' + propertyId
        });
    } else {
        // Fallback: copy to clipboard
        const url = window.location.origin + '/listings/' + propertyId;
        navigator.clipboard.writeText(url).then(() => {
            if (typeof showNotification === 'function') {
                showNotification('Link copied to clipboard!', 'success');
            } else {
                alert('Link copied to clipboard!');
            }
        });
    }
}

// Enhanced Comparison functionality with server sync
function addToCompare(propertyId) {
    // Show loading state
    const compareButton = document.querySelector(`[data-property-id="${propertyId}"][data-action="compare"]`);
    if (!compareButton) {
        console.error('Compare button not found for property:', propertyId);
        return;
    }
    
    const originalContent = compareButton.innerHTML;
    compareButton.innerHTML = '<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    compareButton.disabled = true;
    
    // Send to server
    fetch('/compare/add', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            property_id: propertyId
        })
    })
    .then(response => response.json())
    .then(data => {
        // Restore button state
        compareButton.innerHTML = originalContent;
        compareButton.disabled = false;
        
        if (data.success) {
            // Update comparison counters
            updateComparisonCounters(data.count);
            
            // Update button state
            compareButton.innerHTML = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Added';
            compareButton.classList.add('bg-green-500', 'text-white');
            compareButton.classList.remove('bg-gray-100', 'text-gray-600');
            
            // Show success message
            showNotification(data.message, 'success');
            
            // Reset button after 2 seconds
            setTimeout(() => {
                compareButton.innerHTML = originalContent;
                compareButton.classList.remove('bg-green-500', 'text-white');
                compareButton.classList.add('bg-gray-100', 'text-gray-600');
            }, 2000);
        } else {
            showNotification(data.message || 'Error adding property to comparison', 'error');
        }
    })
    .catch(error => {
        // Restore button state
        compareButton.innerHTML = originalContent;
        compareButton.disabled = false;
        
        console.error('Error:', error);
        showNotification('Error adding property to comparison', 'error');
    });
}

// Update all comparison counters
function updateComparisonCounters(count) {
    const counters = [
        'compare-count',
        'compare-count-mobile', 
        'compare-count-desktop'
    ];
    
    counters.forEach(id => {
        const counter = document.getElementById(id);
        if (counter) {
            counter.textContent = count;
            counter.style.display = count > 0 ? 'inline' : 'none';
        }
    });
    
    // Update comparison button text if it exists
    const compareButton = document.querySelector('[data-comparison-count]');
    if (compareButton) {
        compareButton.setAttribute('data-comparison-count', count);
        if (count > 0) {
            compareButton.textContent = `Compare (${count})`;
        } else {
            compareButton.textContent = 'Compare';
        }
    }
}

// Enhanced notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.comparison-notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `comparison-notification fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white max-w-sm transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' : 
                  type === 'error' ? '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>' :
                  '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    // Remove notification after 4 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 4000);
}

// Initialize comparison counters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update comparison count from server
    fetch('/compare/count')
        .then(response => response.json())
        .then(data => {
            updateComparisonCounters(data.count);
        })
        .catch(error => {
            console.error('Error fetching comparison count:', error);
        });
});
</script>