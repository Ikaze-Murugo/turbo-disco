<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Property') }}
            </h2>
            <a href="{{ route('properties.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                Back to Properties
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form x-data="{ isSubmitting: false }" @submit="isSubmitting = true" method="POST" action="{{ route('properties.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Property Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" 
                                         :value="old('title')" required autofocus 
                                         placeholder="e.g., 2BR Apartment in Kigali" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" 
                                     class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                     placeholder="Describe your property, amenities, nearby facilities..."
                                     required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Price -->
                        <div class="mb-4">
                            <x-input-label for="price" :value="__('Monthly Rent (RWF)')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" 
                                         :value="old('price')" required min="0" step="1000" 
                                         placeholder="e.g., 150000" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <x-input-label for="address" :value="__('Full Address')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" 
                                         :value="old('address')" required 
                                         placeholder="e.g., KG 123 St, Remera, Gasabo District, Kigali" />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">Enter the complete address for automatic location detection</p>
                        </div>

                        <!-- Bedrooms and Bathrooms -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="bedrooms" :value="__('Bedrooms')" />
                                <select id="bedrooms" name="bedrooms" 
                                       class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       required>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('bedrooms') == $i ? 'selected' : '' }}>
                                            {{ $i }} Bedroom{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                    <option value="6" {{ old('bedrooms') == 6 ? 'selected' : '' }}>6+ Bedrooms</option>
                                </select>
                                <x-input-error :messages="$errors->get('bedrooms')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="bathrooms" :value="__('Bathrooms')" />
                                <select id="bathrooms" name="bathrooms" 
                                       class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       required>
                                    @for($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" {{ old('bathrooms') == $i ? 'selected' : '' }}>
                                            {{ $i }} Bathroom{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                    <option value="5" {{ old('bathrooms') == 5 ? 'selected' : '' }}>5+ Bathrooms</option>
                                </select>
                                <x-input-error :messages="$errors->get('bathrooms')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Property Images -->
                        <div class="mb-4">
                            <x-input-label for="images" :value="__('Property Images')" />
                            <input id="images" type="file" name="images[]" multiple 
                                   accept="image/*" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            <p class="text-sm text-gray-500 mt-1">Upload multiple images (max 2MB each, JPEG, PNG, JPG, GIF)</p>
                            <x-input-error :messages="$errors->get('images')" class="mt-2" />
                        </div>

                        <!-- Property Type -->
                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Property Type')" />
                            <select id="type" name="type" 
                                   class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                   required>
                                <option value="">Select Property Type</option>
                                <option value="house" {{ old('type') == 'house' ? 'selected' : '' }}>House</option>
                                <option value="apartment" {{ old('type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                <option value="studio" {{ old('type') == 'studio' ? 'selected' : '' }}>Studio</option>
                                <option value="condo" {{ old('type') == 'condo' ? 'selected' : '' }}>Condo</option>
                                <option value="villa" {{ old('type') == 'villa' ? 'selected' : '' }}>Villa</option>
                                <option value="townhouse" {{ old('type') == 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                                <option value="commercial" {{ old('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Area and Parking -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="area" :value="__('Area (sq m)')" />
                                <x-text-input id="area" class="block mt-1 w-full" type="number" name="area" 
                                             :value="old('area')" min="0" step="0.1" 
                                             placeholder="e.g., 120.5" />
                                <x-input-error :messages="$errors->get('area')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="parking_spaces" :value="__('Parking Spaces')" />
                                <select id="parking_spaces" name="parking_spaces" 
                                       class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="0" {{ old('parking_spaces') == 0 ? 'selected' : '' }}>No Parking</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('parking_spaces') == $i ? 'selected' : '' }}>
                                            {{ $i }} Space{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error :messages="$errors->get('parking_spaces')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Furnishing Status -->
                        <div class="mb-4">
                            <x-input-label for="furnishing_status" :value="__('Furnishing Status')" />
                            <select id="furnishing_status" name="furnishing_status" 
                                   class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Furnishing Status</option>
                                <option value="furnished" {{ old('furnishing_status') == 'furnished' ? 'selected' : '' }}>Furnished</option>
                                <option value="semi_furnished" {{ old('furnishing_status') == 'semi_furnished' ? 'selected' : '' }}>Semi-Furnished</option>
                                <option value="unfurnished" {{ old('furnishing_status') == 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                            </select>
                            <x-input-error :messages="$errors->get('furnishing_status')" class="mt-2" />
                        </div>

                        <!-- Amenities -->
                        <div class="mb-4">
                            <x-input-label :value="__('Amenities & Features')" />
                            <div class="grid grid-cols-2 gap-4 mt-2">
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_balcony" value="1" {{ old('has_balcony') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Balcony</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_garden" value="1" {{ old('has_garden') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Garden</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_pool" value="1" {{ old('has_pool') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Swimming Pool</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_gym" value="1" {{ old('has_gym') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Gym</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_security" value="1" {{ old('has_security') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Security</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_elevator" value="1" {{ old('has_elevator') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Elevator</span>
                                    </label>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_air_conditioning" value="1" {{ old('has_air_conditioning') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Air Conditioning</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_heating" value="1" {{ old('has_heating') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Heating</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_internet" value="1" {{ old('has_internet') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Internet</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_cable_tv" value="1" {{ old('has_cable_tv') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Cable TV</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="pets_allowed" value="1" {{ old('pets_allowed') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Pets Allowed</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="smoking_allowed" value="1" {{ old('smoking_allowed') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Smoking Allowed</span>
                                    </label>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('amenities')" class="mt-2" />
                        </div>

                        <!-- Information Notice -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Property Review Process</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>Your property will be submitted for admin review before being published. This usually takes 24-48 hours.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end">
                            <button type="submit" :disabled="isSubmitting" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting">{{ __('Create Property') }}</span>
                                <span x-show="isSubmitting" x-cloak class="flex items-center">
                                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>