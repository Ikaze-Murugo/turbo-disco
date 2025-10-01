@extends('layouts.app')

@section('title', $property->title . ' - Property Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('properties.public.index') }}" class="text-gray-400 hover:text-gray-500">
                            Properties
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-500">{{ $property->title }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Property Images -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    @if($property->images->count() > 0)
                        <div class="relative">
                            <div id="mainImage" class="aspect-w-16 aspect-h-9">
                                <img src="{{ asset('storage/' . $property->images->first()->path) }}" 
                                     alt="{{ $property->title }}"
                                     class="w-full h-96 object-cover">
                            </div>
                            
                            <!-- Image Navigation -->
                            @if($property->images->count() > 1)
                                <button id="prevImage" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <button id="nextImage" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            @endif
                            
                            <!-- Image Counter -->
                            @if($property->images->count() > 1)
                                <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                                    <span id="currentImage">1</span> / {{ $property->images->count() }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Thumbnail Gallery -->
                        @if($property->images->count() > 1)
                            <div class="p-4">
                                <div class="flex space-x-2 overflow-x-auto">
                                    @foreach($property->images as $index => $image)
                                        <button class="thumbnail-btn flex-shrink-0 w-20 h-16 rounded-lg overflow-hidden border-2 {{ $index === 0 ? 'border-blue-500' : 'border-gray-200' }}" 
                                                data-index="{{ $index }}">
                                            <img src="{{ asset('storage/' . $image->path) }}" 
                                                 alt="{{ $property->title }}"
                                                 class="w-full h-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="h-96 bg-gray-200 flex items-center justify-center">
                            <div class="text-center text-gray-400">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <p>No images available</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Property Details -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $property->title }}</h1>
                            <div class="flex items-center text-gray-600 mb-4">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $property->address }}, {{ $property->neighborhood }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-blue-600 mb-2">
                                RWF {{ number_format($property->price) }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $property->furnishing_status ? ucfirst(str_replace('-', ' ', $property->furnishing_status)) : 'Unspecified' }}
                            </div>
                        </div>
                    </div>

                    <!-- Property Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ $property->bedrooms }}</div>
                            <div class="text-sm text-gray-600">Bedrooms</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ $property->bathrooms }}</div>
                            <div class="text-sm text-gray-600">Bathrooms</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ $property->area ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-600">m²</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ $property->parking_spaces ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-600">Parking</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Description</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $property->description }}</p>
                    </div>

                    <!-- Amenities -->
                    @if($property->nearbyAmenities->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Amenities</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($property->nearbyAmenities as $amenity)
                                    <div class="flex items-center space-x-2">
                                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-gray-700">{{ $amenity->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Property Features -->
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Property Features</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 {{ $property->has_gym ? 'text-green-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Gym</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 {{ $property->has_security ? 'text-green-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Security</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 {{ $property->has_air_conditioning ? 'text-green-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Air Conditioning</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 {{ $property->has_balcony ? 'text-green-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Balcony</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 {{ $property->has_garden ? 'text-green-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Garden</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 {{ $property->has_pool ? 'text-green-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Pool</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 {{ $property->has_elevator ? 'text-green-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Elevator</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 {{ $property->has_heating ? 'text-green-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Heating</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 {{ $property->has_internet ? 'text-green-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Internet</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews Section -->
                @if($property->reviews->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Reviews</h3>
                        <div class="space-y-4">
                            @foreach($property->reviews->take(3) as $review)
                                <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                {{ substr($review->user->name, 0, 1) }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $review->user->name }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                    <p class="text-sm text-gray-500 mt-2">{{ $review->created_at->format('M d, Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Contact Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 sticky top-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Landlord</h3>
                    
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                            {{ substr($property->landlord->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $property->landlord->name }}</div>
                            <div class="text-sm text-gray-500">Property Owner</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Send Message
                        </button>
                        <button class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                            Call Now
                        </button>
                        <button class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                            Schedule Visit
                        </button>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>Property ID:</span>
                            <span class="font-mono">{{ $property->id }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-500 mt-1">
                            <span>Views:</span>
                            <span>{{ $property->views_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-500 mt-1">
                            <span>Listed:</span>
                            <span>{{ $property->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Share Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Share Property</h3>
                    <div class="flex space-x-3">
                        <button class="flex-1 bg-blue-600 text-white py-2 px-3 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Facebook
                        </button>
                        <button class="flex-1 bg-blue-400 text-white py-2 px-3 rounded-lg hover:bg-blue-500 transition-colors text-sm">
                            Twitter
                        </button>
                        <button class="flex-1 bg-green-600 text-white py-2 px-3 rounded-lg hover:bg-green-700 transition-colors text-sm">
                            WhatsApp
                        </button>
                    </div>
                </div>

                <!-- Related Properties -->
                @if($relatedProperties->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Similar Properties</h3>
                        <div class="space-y-4">
                            @foreach($relatedProperties as $related)
                                <div class="flex space-x-3">
                                    <div class="w-20 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($related->images->count() > 0)
                                            <img src="{{ asset('storage/' . $related->images->first()->path) }}" 
                                                 alt="{{ $related->title }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 line-clamp-1">
                                            <a href="{{ route('properties.public.show', $related->id) }}" class="hover:text-blue-600">
                                                {{ $related->title }}
                                            </a>
                                        </h4>
                                        <p class="text-sm text-gray-500">{{ $related->bedrooms }} bed, {{ $related->bathrooms }} bath</p>
                                        <p class="text-sm font-medium text-blue-600">RWF {{ number_format($related->price) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for image gallery -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const images = @json($property->images->pluck('path'));
    let currentImageIndex = 0;
    
    if (images.length <= 1) return;
    
    const mainImage = document.getElementById('mainImage').querySelector('img');
    const currentImageSpan = document.getElementById('currentImage');
    const prevBtn = document.getElementById('prevImage');
    const nextBtn = document.getElementById('nextImage');
    const thumbnailBtns = document.querySelectorAll('.thumbnail-btn');
    
    function updateImage(index) {
        mainImage.src = `/storage/${images[index]}`;
        currentImageSpan.textContent = index + 1;
        
        // Update thumbnail borders
        thumbnailBtns.forEach((btn, i) => {
            btn.classList.toggle('border-blue-500', i === index);
            btn.classList.toggle('border-gray-200', i !== index);
        });
    }
    
    prevBtn.addEventListener('click', function() {
        currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : images.length - 1;
        updateImage(currentImageIndex);
    });
    
    nextBtn.addEventListener('click', function() {
        currentImageIndex = currentImageIndex < images.length - 1 ? currentImageIndex + 1 : 0;
        updateImage(currentImageIndex);
    });
    
    thumbnailBtns.forEach((btn, index) => {
        btn.addEventListener('click', function() {
            currentImageIndex = index;
            updateImage(currentImageIndex);
        });
    });
});
</script>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
