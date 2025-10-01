<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $property->title }}
            </h2>
            <div class="flex space-x-2">
                @if(auth()->user()->isLandlord() && $property->landlord_id === auth()->id())
                    <a href="{{ route('properties.edit', $property) }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Edit Property
                    </a>
                    
                    <form method="POST" action="{{ route('properties.destroy', $property) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this property?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors duration-200">
                            Delete Property
                        </button>
                    </form>
                @endif
                
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('properties.edit', $property) }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                        Manage Property
                    </a>
                @endif
                
                @if(auth()->user()->isRenter())
                    <a href="{{ route('reports.create.property', $property) }}" 
                       class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors duration-200">
                        Report Property
                    </a>
                @endif
                
                <a href="{{ route('properties.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    Back to Properties
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Property Images -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        @if($property->images && $property->images->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($property->images->where('image_type', '!=', 'blueprint') as $index => $image)
                                    <div class="{{ $index === 0 ? 'md:col-span-2' : '' }}">
                                        <img src="{{ Storage::url($image->image_path ?? $image->path) }}" 
                                             alt="{{ $image->alt_text ?? $property->title }}"
                                             class="w-full h-64 object-cover {{ $index === 0 ? 'h-96' : 'h-32' }}">
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
                    </div>
                    
                    <!-- Property Details -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <!-- Property Status Information -->
                        @if($property->version_status === 'original' && $property->hasPendingUpdates())
                            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-medium text-yellow-800">Pending Updates</h3>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            You have pending updates for this property that are waiting for admin approval. 
                                            The current approved version is still visible to renters.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($property->version_status === 'pending_update')
                            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-medium text-blue-800">Update Pending Approval</h3>
                                        <p class="text-sm text-blue-700 mt-1">
                                            This is a pending update version. It will be visible to renters once approved by an admin.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <h1 class="text-3xl font-bold mb-4">{{ $property->title }}</h1>
                        <p class="text-gray-600 mb-6">{{ $property->description }}</p>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $property->bedrooms }}</div>
                                <div class="text-sm text-gray-500">Bedrooms</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $property->bathrooms }}</div>
                                <div class="text-sm text-gray-500">Bathrooms</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $property->area }}</div>
                                <div class="text-sm text-gray-500">Sqm</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">RWF {{ number_format($property->price) }}</div>
                                <div class="text-sm text-gray-500">Monthly</div>
                            </div>
                        </div>

                        <!-- Property Features -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Features</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @if($property->has_parking)
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Parking
                                    </div>
                                @endif
                                @if($property->has_security)
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Security
                                    </div>
                                @endif
                                @if($property->has_air_conditioning)
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Air Conditioning
                                    </div>
                                @endif
                                @if($property->has_balcony)
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Balcony
                                    </div>
                                @endif
                                @if($property->has_garden)
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Garden
                                    </div>
                                @endif
                                @if($property->has_pool)
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Pool
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Blueprints -->
                    @if($property->blueprints->count() > 0)
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Ground Blueprints</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($property->blueprints as $blueprint)
                                <div class="border rounded-lg overflow-hidden">
                                    @if(pathinfo($blueprint->image_path ?? $blueprint->path, PATHINFO_EXTENSION) === 'pdf')
                                        <iframe src="{{ Storage::url($blueprint->image_path ?? $blueprint->path) }}" 
                                                class="w-full h-64"></iframe>
                                    @else
                                        <img src="{{ Storage::url($blueprint->image_path ?? $blueprint->path) }}" 
                                             alt="Blueprint" class="w-full h-64 object-cover">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Contact Information -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Contact Landlord</h2>
                        <p class="text-gray-600 mb-4">{{ $property->landlord->name }}</p>
                        <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                            Contact Now
                        </button>
                    </div>
                    
                    <!-- Property Map -->
                    @if($property->hasCoordinates())
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Location</h2>
                        <x-property-map :property="$property" height="300px" :show-amenities="true" />
                    </div>
                    @endif
                    
                    <!-- Nearby Amenities -->
                    @if($nearbyAmenities->count() > 0)
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Nearby Amenities</h2>
                        <div class="space-y-3">
                            @foreach($nearbyAmenities->take(10) as $item)
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-medium">{{ $item->amenity->name }}</div>
                                        <div class="text-sm text-gray-500">{{ ucfirst($item->amenity->type) }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium">{{ $item->distance_km }} km</div>
                                        <div class="text-sm text-gray-500">{{ $item->walking_time_minutes }} min walk</div>
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
</x-app-layout>
