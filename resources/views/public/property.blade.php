@extends('public.layout')

@section('title', $property->title . ' - Murugo Property Platform')
@section('description', $property->description)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('public.home') }}" class="hover:text-blue-600">Home</a></li>
                <li><span>/</span></li>
                <li><a href="{{ route('public.properties') }}" class="hover:text-blue-600">Properties</a></li>
                <li><span>/</span></li>
                <li class="text-gray-900">{{ $property->title }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Property Images -->
                @if($property->images->count() > 0)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($property->images->take(4) as $index => $image)
                                <div class="{{ $index === 0 ? 'md:col-span-2' : '' }} h-64 md:h-48">
                                    <img src="{{ Storage::url($image->path) }}" 
                                         alt="{{ $property->title }}" 
                                         class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                        @if($property->images->count() > 4)
                            <div class="p-4 text-center">
                                <span class="text-sm text-gray-500">+{{ $property->images->count() - 4 }} more images</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-md h-64 flex items-center justify-center mb-6">
                        <span class="text-gray-400 text-lg">No images available</span>
                    </div>
                @endif

                <!-- Property Details -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $property->title }}</h1>
                    
                    <div class="flex items-center text-gray-600 mb-4">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $property->location }}
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $property->bedrooms }}</div>
                            <div class="text-sm text-gray-600">Bedrooms</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $property->bathrooms }}</div>
                            <div class="text-sm text-gray-600">Bathrooms</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ ucfirst($property->type) }}</div>
                            <div class="text-sm text-gray-600">Property Type</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $property->created_at->diffForHumans() }}</div>
                            <div class="text-sm text-gray-600">Listed</div>
                        </div>
                    </div>

                    <div class="prose max-w-none">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Description</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $property->description }}</p>
                    </div>

                    <!-- Amenities Section -->
                    @if($property->has_balcony || $property->has_garden || $property->has_pool || $property->has_gym || 
                        $property->has_security || $property->has_elevator || $property->has_air_conditioning || 
                        $property->has_heating || $property->has_internet || $property->has_cable_tv || 
                        $property->pets_allowed || $property->smoking_allowed)
                        <div class="mt-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Amenities & Features</h3>
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
                </div>

                <!-- Reviews Section -->
                @if($property->reviews->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Reviews</h3>
                        <div class="space-y-4">
                            @foreach($property->reviews->take(3) as $review)
                                <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                                {{ substr($review->user->name, 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="font-semibold text-gray-900">{{ $review->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                        @if($property->reviews->count() > 3)
                            <div class="mt-4 text-center">
                                <a href="#" class="text-blue-600 hover:text-blue-800">View all {{ $property->reviews->count() }} reviews</a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Price Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 sticky top-24">
                    <div class="text-center mb-6">
                        <div class="text-4xl font-bold text-blue-600 mb-2">
                            {{ number_format($property->price) }} RWF
                        </div>
                        <div class="text-gray-600">per month</div>
                    </div>

                    @guest
                        <div class="space-y-3">
                            <a href="{{ route('login') }}" 
                               class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center block">
                                Login to Contact Landlord
                            </a>
                            <a href="{{ route('register') }}" 
                               class="w-full border border-blue-600 text-blue-600 py-3 px-4 rounded-lg font-semibold hover:bg-blue-50 transition-colors text-center block">
                                Register Free
                            </a>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-600">Join thousands of users finding their perfect home</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            <a href="#" 
                               class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center block">
                                Contact Landlord
                            </a>
                            <a href="#" 
                               class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-semibold hover:bg-gray-50 transition-colors text-center block">
                                Save to Favorites
                            </a>
                            <a href="{{ route('reports.create.property', $property) }}" 
                               class="w-full border border-red-300 text-red-700 py-3 px-4 rounded-lg font-semibold hover:bg-red-50 transition-colors text-center block">
                                Report Property
                            </a>
                        </div>
                    @endguest
                </div>

                <!-- Landlord Info -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Landlord</h3>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                            {{ substr($property->landlord->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <div class="font-semibold text-gray-900">{{ $property->landlord->name }}</div>
                            <div class="text-sm text-gray-600">Verified Landlord</div>
                        </div>
                    </div>
                    @guest
                        <div class="mt-4 text-center">
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                Login to view contact details
                            </a>
                        </div>
                    @endguest
                </div>

                <!-- Share Property -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Share Property</h3>
                    <div class="flex space-x-3">
                        <a href="#" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg text-center hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="flex-1 bg-blue-800 text-white py-2 px-4 rounded-lg text-center hover:bg-blue-900 transition-colors">
                            <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="flex-1 bg-gray-600 text-white py-2 px-4 rounded-lg text-center hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Properties -->
        @if($relatedProperties->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Properties</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProperties as $relatedProperty)
                        <div class="property-card bg-white rounded-lg shadow-md overflow-hidden">
                            @if($relatedProperty->images->count() > 0)
                                <div class="h-40 bg-gray-200">
                                    <img src="{{ Storage::url($relatedProperty->images->first()->path) }}" 
                                         alt="{{ $relatedProperty->title }}" 
                                         class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="h-40 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                            
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-1 truncate">{{ $relatedProperty->title }}</h3>
                                <p class="text-sm text-gray-600 mb-2">{{ $relatedProperty->location }}</p>
                                
                                <div class="flex items-center justify-between mb-3">
                                    <div class="text-lg font-bold text-blue-600">
                                        {{ number_format($relatedProperty->price) }} RWF
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $relatedProperty->bedrooms }} bed, {{ $relatedProperty->bathrooms }} bath
                                    </div>
                                </div>
                                
                                <a href="{{ route('public.property.show', $relatedProperty) }}" 
                                   class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
