<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pending Properties') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($properties->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="h-24 w-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No pending properties</h3>
                        <p class="text-gray-500">All properties have been reviewed. Check back later for new submissions.</p>
                    </div>
                </div>
            @else
                <!-- Pending Properties List -->
                <div class="space-y-6">
                    @foreach($properties as $property)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <h3 class="text-xl font-semibold text-gray-900">{{ $property->title }}</h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending Review
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Price:</strong> {{ number_format($property->price) }} RWF/month
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Location:</strong> {{ $property->location }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Bedrooms:</strong> {{ $property->bedrooms }} | 
                                                    <strong>Bathrooms:</strong> {{ $property->bathrooms }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Landlord:</strong> {{ $property->landlord->name }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Email:</strong> {{ $property->landlord->email }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Submitted:</strong> {{ $property->created_at->format('M d, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600 mb-2"><strong>Description:</strong></p>
                                            <p class="text-gray-700 text-sm bg-gray-50 p-3 rounded-md">
                                                {{ Str::limit($property->description, 200) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex space-x-3 pt-4 border-t border-gray-200">
                                    <a href="{{ route('properties.show', $property) }}" 
                                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                        View Details
                                    </a>
                                    
                                    <form method="POST" action="{{ route('admin.properties.approve', $property) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200"
                                                onclick="return confirm('Are you sure you want to approve this property?')">
                                            Approve Property
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('admin.properties.reject', $property) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors duration-200"
                                                onclick="return confirm('Are you sure you want to reject this property?')">
                                            Reject Property
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
