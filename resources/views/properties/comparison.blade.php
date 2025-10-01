@extends('layouts.app')

@section('title', 'Property Comparison - Murugo Property Platform')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Property Comparison</h1>
            <p class="text-gray-600">Compare up to 4 properties side by side</p>
        </div>

        @if($properties->count() > 0)
            <!-- Comparison Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Property
                                </th>
                                @foreach($properties as $property)
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex flex-col items-center">
                                            @if($property->images->count() > 0)
                                                <img src="{{ Storage::url($property->images->first()->path) }}" 
                                                     alt="{{ $property->title }}" 
                                                     class="w-24 h-24 object-cover rounded-lg mb-2">
                                            @else
                                                <div class="w-24 h-24 bg-gray-200 rounded-lg mb-2 flex items-center justify-center">
                                                    <span class="text-gray-400 text-xs">No Image</span>
                                                </div>
                                            @endif
                                            <h3 class="font-semibold text-gray-900 text-sm">{{ $property->title }}</h3>
                                            <p class="text-xs text-gray-600">{{ $property->location }}</p>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Price -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Price
                                </td>
                                @foreach($properties as $property)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                        <div class="text-lg font-bold text-blue-600">
                                            {{ number_format($property->price) }} RWF
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Property Type -->
                            <tr class="bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Property Type
                                </td>
                                @foreach($properties as $property)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                        {{ ucfirst($property->type) }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Bedrooms -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Bedrooms
                                </td>
                                @foreach($properties as $property)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                        {{ $property->bedrooms }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Bathrooms -->
                            <tr class="bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Bathrooms
                                </td>
                                @foreach($properties as $property)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                        {{ $property->bathrooms }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Area -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Area
                                </td>
                                @foreach($properties as $property)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                        {{ $property->area ? $property->area . ' m²' : 'N/A' }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Furnishing Status -->
                            <tr class="bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Furnishing
                                </td>
                                @foreach($properties as $property)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                        {{ $property->furnishing_status ? ucfirst($property->furnishing_status) : 'N/A' }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Parking Spaces -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Parking Spaces
                                </td>
                                @foreach($properties as $property)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                        {{ $property->parking_spaces ?? 'N/A' }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Amenities -->
                            <tr class="bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Amenities
                                </td>
                                @foreach($properties as $property)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                        <div class="space-y-1">
                                            @if($property->has_balcony)
                                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Balcony</span>
                                            @endif
                                            @if($property->has_garden)
                                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Garden</span>
                                            @endif
                                            @if($property->has_pool)
                                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Pool</span>
                                            @endif
                                            @if($property->has_gym)
                                                <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">Gym</span>
                                            @endif
                                            @if($property->has_security)
                                                <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Security</span>
                                            @endif
                                            @if($property->has_elevator)
                                                <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">Elevator</span>
                                            @endif
                                            @if($property->has_air_conditioning)
                                                <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">A/C</span>
                                            @endif
                                            @if($property->has_internet)
                                                <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">Internet</span>
                                            @endif
                                            @if($property->pets_allowed)
                                                <span class="inline-block bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded">Pets Allowed</span>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Landlord -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Landlord
                                </td>
                                @foreach($properties as $property)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                        {{ $property->landlord->name }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Actions -->
                            <tr class="bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Actions
                                </td>
                                @foreach($properties as $property)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <div class="space-y-2">
                                            <a href="{{ route('public.property.show', $property) }}" 
                                               class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                                View Details
                                            </a>
                                            <button onclick="removeFromComparison({{ $property->id }})" 
                                                    class="inline-block bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                                Remove
                                            </button>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-center space-x-4">
                <a href="{{ route('public.search') }}" 
                   class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Search
                </a>
                <button onclick="clearComparison()" 
                        class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors">
                    Clear All
                </button>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="text-gray-400 text-6xl mb-4">📊</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Properties to Compare</h3>
                <p class="text-gray-600 mb-6">Add properties to your comparison list to see them side by side.</p>
                <a href="{{ route('public.search') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Start Searching
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function removeFromComparison(propertyId) {
    if (confirm('Are you sure you want to remove this property from comparison?')) {
        fetch(`/properties/${propertyId}/compare`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function clearComparison() {
    if (confirm('Are you sure you want to clear all properties from comparison?')) {
        // This would need to be implemented in the controller
        // For now, redirect to search page
        window.location.href = '{{ route("public.search") }}';
    }
}
</script>
@endsection
