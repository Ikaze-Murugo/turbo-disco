<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Reviews for ') }} "{{ $property->title }}"
            </h2>
            <div class="flex space-x-2">
                @if(auth()->user()->isRenter() && !$hasReviewed)
                    <a href="{{ route('reviews.create', $property) }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Write Review
                    </a>
                @endif
                <a href="{{ route('properties.show', $property) }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    Back to Property
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Property Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $property->title }}</h3>
                            <p class="text-gray-600">{{ $property->location }}</p>
                            <p class="text-sm text-gray-500">By {{ $property->landlord->name }}</p>
                        </div>
                        <div class="text-right">
                            @if($averageRating)
                                <div class="flex items-center space-x-2">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($averageRating))
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
                                    <span class="text-lg font-semibold text-gray-900">{{ number_format($averageRating, 1) }}</span>
                                </div>
                                <p class="text-sm text-gray-500">{{ $reviewCount }} review{{ $reviewCount !== 1 ? 's' : '' }}</p>
                            @else
                                <p class="text-gray-500">No reviews yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            @if($reviews->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="h-24 w-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews yet</h3>
                        <p class="text-gray-500 mb-4">Be the first to review this property!</p>
                        @if(auth()->user()->isRenter() && !$hasReviewed)
                            <a href="{{ route('reviews.create', $property) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                Write First Review
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-600">
                                                {{ $review->is_anonymous ? 'A' : substr($review->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">
                                                {{ $review->is_anonymous ? 'Anonymous' : $review->user->name }}
                                            </h4>
                                            <p class="text-sm text-gray-500">{{ $review->created_at->format('M j, Y') }}</p>
                                        </div>
                                    </div>
                                    
                                    @if(auth()->user()->id === $review->user_id || auth()->user()->isAdmin())
                                        <div class="flex space-x-2">
                                            @if(auth()->user()->id === $review->user_id && !$review->is_approved)
                                                <a href="{{ route('reviews.edit', $review) }}" 
                                                   class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                                            @endif
                                            @if(auth()->user()->id === $review->user_id || auth()->user()->isAdmin())
                                                <form method="POST" action="{{ route('reviews.destroy', $review) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 text-sm"
                                                            onclick="return confirm('Are you sure you want to delete this review?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Ratings -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-700 mb-1">Property Rating</h5>
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->property_rating)
                                                    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endif
                                            @endfor
                                            <span class="ml-2 text-sm text-gray-600">{{ $review->property_rating }}/5</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-700 mb-1">Landlord Rating</h5>
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->landlord_rating)
                                                    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endif
                                            @endfor
                                            <span class="ml-2 text-sm text-gray-600">{{ $review->landlord_rating }}/5</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Review Text -->
                                @if($review->property_review)
                                    <div class="mb-4">
                                        <h5 class="text-sm font-medium text-gray-700 mb-2">Property Review</h5>
                                        <p class="text-gray-600">{{ $review->property_review }}</p>
                                    </div>
                                @endif

                                @if($review->landlord_review)
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-700 mb-2">Landlord Review</h5>
                                        <p class="text-gray-600">{{ $review->landlord_review }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
