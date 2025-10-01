<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $property->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reports.create.property', $property) }}" 
                   class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors duration-200">
                    Report Property
                </a>
                <a href="{{ route('properties.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    Back to Properties
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Property Images -->
                @if($property->images && $property->images->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                        @foreach($property->images as $image)
                            <div class="relative group">
                                <img src="{{ Storage::url($image->path) }}" 
                                     alt="{{ $image->alt_text ?? $property->title }}"
                                     class="w-full h-48 object-cover rounded-lg">
                                @if($image->is_primary)
                                    <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                        Primary
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-96 bg-gray-300 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="h-24 w-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500">No images available</p>
                        </div>
                    </div>
                @endif

                <div class="p-6">
                    <!-- Property Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $property->title }}</h1>
                            <p class="text-gray-600 flex items-center">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $property->location }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-blue-600">{{ number_format($property->price) }} RWF</p>
                            <p class="text-gray-500">per month</p>
                        </div>
                    </div>

                    <!-- Property Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <svg class="h-8 w-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M10.5 3L12 2l1.5 1M21 3l-9 9-9-9"></path>
                            </svg>
                            <p class="text-2xl font-bold text-gray-900">{{ $property->bedrooms }}</p>
                            <p class="text-gray-600">Bedrooms</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <svg class="h-8 w-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21l4-4 4 4m-4-4v3"></path>
                            </svg>
                            <p class="text-2xl font-bold text-gray-900">{{ $property->bathrooms }}</p>
                            <p class="text-gray-600">Bathrooms</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <svg class="h-8 w-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-2xl font-bold text-gray-900">
                                @if($property->is_available)
                                    <span class="text-green-600">Yes</span>
                                @else
                                    <span class="text-red-600">No</span>
                                @endif
                            </p>
                            <p class="text-gray-600">Available</p>
                        </div>
                    </div>

                    <!-- Amenities Section -->
                    @if($property->has_balcony || $property->has_garden || $property->has_pool || $property->has_gym || 
                        $property->has_security || $property->has_elevator || $property->has_air_conditioning || 
                        $property->has_heating || $property->has_internet || $property->has_cable_tv || 
                        $property->pets_allowed || $property->smoking_allowed)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Amenities & Features</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                @if($property->has_balcony)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Balcony</span>
                                    </div>
                                @endif
                                @if($property->has_garden)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Garden</span>
                                    </div>
                                @endif
                                @if($property->has_pool)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Swimming Pool</span>
                                    </div>
                                @endif
                                @if($property->has_gym)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Gym</span>
                                    </div>
                                @endif
                                @if($property->has_security)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Security</span>
                                    </div>
                                @endif
                                @if($property->has_elevator)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Elevator</span>
                                    </div>
                                @endif
                                @if($property->has_air_conditioning)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Air Conditioning</span>
                                    </div>
                                @endif
                                @if($property->has_heating)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Heating</span>
                                    </div>
                                @endif
                                @if($property->has_internet)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Internet</span>
                                    </div>
                                @endif
                                @if($property->has_cable_tv)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Cable TV</span>
                                    </div>
                                @endif
                                @if($property->pets_allowed)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Pets Allowed</span>
                                    </div>
                                @endif
                                @if($property->smoking_allowed)
                                    <div class="flex items-center space-x-2 text-green-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">Smoking Allowed</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Ratings Section -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Property & Landlord Ratings</h3>
                                @php
                                    $averageRating = $property->getAverageRating();
                                    $reviewCount = $property->getReviewCount();
                                    $landlordAverageRating = $property->landlord->getAverageLandlordRating();
                                    $landlordReviewCount = $property->landlord->getLandlordReviewCount();
                                @endphp
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">Property Rating</p>
                                        @if($averageRating)
                                            <div class="flex items-center space-x-2">
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= round($averageRating))
                                                            <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">{{ number_format($averageRating, 1) }}</span>
                                                <span class="text-sm text-gray-500">({{ $reviewCount }} review{{ $reviewCount !== 1 ? 's' : '' }})</span>
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500">No ratings yet</p>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">Landlord Rating</p>
                                        @if($landlordAverageRating)
                                            <div class="flex items-center space-x-2">
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= round($landlordAverageRating))
                                                            <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">{{ number_format($landlordAverageRating, 1) }}</span>
                                                <span class="text-sm text-gray-500">({{ $landlordReviewCount }} review{{ $landlordReviewCount !== 1 ? 's' : '' }})</span>
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500">No ratings yet</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('reviews.index', $property) }}" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                    View Reviews
                                </a>
                                @if(auth()->user()->isRenter() && !\App\Models\Review::hasUserReviewedProperty(auth()->id(), $property->id))
                                    <a href="{{ route('reviews.create', $property) }}" 
                                       class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200">
                                        Write Review
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status Badges -->
                    <div class="flex space-x-3 mb-6">
                        @if($property->status === 'active')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                ✓ Active
                            </span>
                        @elseif($property->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                ⏳ Pending Approval
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                ✗ Rejected
                            </span>
                        @endif
                        
                        @if($property->is_available)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                🏠 Available for Rent
                            </span>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Description</h3>
                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-line">{{ $property->description }}</p>
                        </div>
                    </div>

                    <!-- Landlord Information -->
                    @if(!auth()->user()->isLandlord() || $property->landlord_id !== auth()->id())
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Landlord Information</h3>
                            <p class="text-gray-700">
                                <strong>Name:</strong> {{ $property->landlord->name }}
                            </p>
                            <p class="text-gray-700">
                                <strong>Email:</strong> {{ $property->landlord->email }}
                            </p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        @if(auth()->user()->isLandlord() && $property->landlord_id === auth()->id())
                            <a href="{{ route('properties.edit', $property) }}" 
                               class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                Edit Property
                            </a>
                            
                            <form method="POST" action="{{ route('properties.destroy', $property) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this property?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition-colors duration-200">
                                    Delete Property
                                </button>
                            </form>
                        @endif
                        
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('properties.edit', $property) }}" 
                               class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                                Manage Property
                            </a>
                        @endif
                        
                        @if(auth()->user()->isRenter())
                            <div class="flex space-x-3">
                                <a href="{{ route('messages.create', $property) }}" 
                                   class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors duration-200">
                                    Contact Landlord
                                </a>
                                <a href="{{ route('reports.create.property', $property) }}" 
                                   class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition-colors duration-200">
                                    Report Property
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>