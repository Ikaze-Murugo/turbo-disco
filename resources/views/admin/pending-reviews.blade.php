<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pending Reviews') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($reviews->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="h-24 w-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No pending reviews</h3>
                        <p class="text-gray-500">All reviews have been processed.</p>
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            Review for "{{ $review->property->title }}"
                                        </h3>
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span>By: {{ $review->is_anonymous ? 'Anonymous' : $review->user->name }}</span>
                                            <span>•</span>
                                            <span>Property: {{ $review->property->location }}</span>
                                            <span>•</span>
                                            <span>Landlord: {{ $review->landlord->name }}</span>
                                            <span>•</span>
                                            <span>{{ $review->created_at->format('M j, Y g:i A') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200"
                                                    onclick="return confirm('Are you sure you want to approve this review?')">
                                                Approve
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors duration-200"
                                                    onclick="return confirm('Are you sure you want to reject this review? This action cannot be undone.')">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Ratings -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h4 class="font-semibold text-gray-900 mb-2">Property Rating</h4>
                                        <div class="flex items-center space-x-2">
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->property_rating)
                                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="text-lg font-semibold text-gray-900">{{ $review->property_rating }}/5</span>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h4 class="font-semibold text-gray-900 mb-2">Landlord Rating</h4>
                                        <div class="flex items-center space-x-2">
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->landlord_rating)
                                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="text-lg font-semibold text-gray-900">{{ $review->landlord_rating }}/5</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Review Text -->
                                @if($review->property_review)
                                    <div class="mb-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">Property Review</h4>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <p class="text-gray-700">{{ $review->property_review }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($review->landlord_review)
                                    <div class="mb-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">Landlord Review</h4>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <p class="text-gray-700">{{ $review->landlord_review }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Property Link -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <a href="{{ route('properties.show', $review->property) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        View Property Details →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
