<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Write a Review for ') }} "{{ $property->title }}"
            </h2>
            <a href="{{ route('properties.show', $property) }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                Back to Property
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Property Info -->
                    <div class="mb-8 border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $property->title }}</h3>
                        <p class="text-gray-600 mb-2">{{ $property->location }}</p>
                        <p class="text-sm text-gray-500">Landlord: {{ $property->landlord->name }}</p>
                    </div>

                    <form method="POST" action="{{ route('reviews.store', $property) }}">
                        @csrf

                        <!-- Property Rating -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Rate the Property (1-5 stars)
                            </label>
                            <div class="flex space-x-1" id="property-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setPropertyRating({{ $i }})" 
                                            class="star-btn text-2xl text-gray-300 hover:text-yellow-400 transition-colors"
                                            data-rating="{{ $i }}">
                                        ★
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="property_rating" id="property_rating_input" value="0" required>
                            <x-input-error :messages="$errors->get('property_rating')" class="mt-2" />
                        </div>

                        <!-- Property Review Text -->
                        <div class="mb-6">
                            <x-input-label for="property_review" :value="__('Property Review (Optional)')" />
                            <textarea id="property_review" name="property_review" rows="4" 
                                      class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                      placeholder="Share your experience with this property...">{{ old('property_review') }}</textarea>
                            <x-input-error :messages="$errors->get('property_review')" class="mt-2" />
                        </div>

                        <!-- Landlord Rating -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Rate the Landlord (1-5 stars)
                            </label>
                            <div class="flex space-x-1" id="landlord-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setLandlordRating({{ $i }})" 
                                            class="star-btn text-2xl text-gray-300 hover:text-yellow-400 transition-colors"
                                            data-rating="{{ $i }}">
                                        ★
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="landlord_rating" id="landlord_rating_input" value="0" required>
                            <x-input-error :messages="$errors->get('landlord_rating')" class="mt-2" />
                        </div>

                        <!-- Landlord Review Text -->
                        <div class="mb-6">
                            <x-input-label for="landlord_review" :value="__('Landlord Review (Optional)')" />
                            <textarea id="landlord_review" name="landlord_review" rows="4" 
                                      class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                      placeholder="Share your experience with the landlord...">{{ old('landlord_review') }}</textarea>
                            <x-input-error :messages="$errors->get('landlord_review')" class="mt-2" />
                        </div>

                        <!-- Anonymous Option -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_anonymous" value="1" 
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Submit this review anonymously</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <x-primary-button type="submit">
                                {{ __('Submit Review') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let propertyRating = 0;
        let landlordRating = 0;

        function setPropertyRating(rating) {
            propertyRating = rating;
            document.getElementById('property_rating_input').value = rating;
            
            // Update star display
            const stars = document.querySelectorAll('#property-rating .star-btn');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
        }

        function setLandlordRating(rating) {
            landlordRating = rating;
            document.getElementById('landlord_rating_input').value = rating;
            
            // Update star display
            const stars = document.querySelectorAll('#landlord-rating .star-btn');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (propertyRating === 0 || landlordRating === 0) {
                e.preventDefault();
                alert('Please provide ratings for both the property and landlord.');
            }
        });
    </script>
</x-app-layout>
