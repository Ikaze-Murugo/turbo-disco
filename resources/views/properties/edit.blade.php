<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Property') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('properties.show', $property) }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    View Property
                </a>
                <a href="{{ route('properties.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    Back to Properties
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
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

                    <form method="POST" action="{{ route('properties.update', $property) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Title -->
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Property Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" 
                                         :value="old('title', $property->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" 
                                     class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                     required>{{ old('description', $property->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Price -->
                        <div class="mb-4">
                            <x-input-label for="price" :value="__('Monthly Rent (RWF)')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" 
                                         :value="old('price', $property->price)" required min="0" step="1000" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div class="mb-4">
                            <x-input-label for="location" :value="__('Location')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" 
                                         :value="old('location', $property->location)" required />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <!-- Bedrooms and Bathrooms -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="bedrooms" :value="__('Bedrooms')" />
                                <select id="bedrooms" name="bedrooms" 
                                       class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       required>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('bedrooms', $property->bedrooms) == $i ? 'selected' : '' }}>
                                            {{ $i }} Bedroom{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                    <option value="6" {{ old('bedrooms', $property->bedrooms) == 6 ? 'selected' : '' }}>6+ Bedrooms</option>
                                </select>
                                <x-input-error :messages="$errors->get('bedrooms')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="bathrooms" :value="__('Bathrooms')" />
                                <select id="bathrooms" name="bathrooms" 
                                       class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       required>
                                    @for($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" {{ old('bathrooms', $property->bathrooms) == $i ? 'selected' : '' }}>
                                            {{ $i }} Bathroom{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                    <option value="5" {{ old('bathrooms', $property->bathrooms) == 5 ? 'selected' : '' }}>5+ Bathrooms</option>
                                </select>
                                <x-input-error :messages="$errors->get('bathrooms')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Property Type -->
                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Property Type')" />
                            <select id="type" name="type" 
                                   class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                   required>
                                <option value="">Select Property Type</option>
                                <option value="house" {{ old('type', $property->type) == 'house' ? 'selected' : '' }}>House</option>
                                <option value="apartment" {{ old('type', $property->type) == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                <option value="studio" {{ old('type', $property->type) == 'studio' ? 'selected' : '' }}>Studio</option>
                                <option value="condo" {{ old('type', $property->type) == 'condo' ? 'selected' : '' }}>Condo</option>
                                <option value="villa" {{ old('type', $property->type) == 'villa' ? 'selected' : '' }}>Villa</option>
                                <option value="townhouse" {{ old('type', $property->type) == 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Area and Parking -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="area" :value="__('Area (sq m)')" />
                                <x-text-input id="area" class="block mt-1 w-full" type="number" name="area" 
                                             :value="old('area', $property->area)" min="0" step="0.1" 
                                             placeholder="e.g., 120.5" />
                                <x-input-error :messages="$errors->get('area')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="parking_spaces" :value="__('Parking Spaces')" />
                                <select id="parking_spaces" name="parking_spaces" 
                                       class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="0" {{ old('parking_spaces', $property->parking_spaces) == 0 ? 'selected' : '' }}>No Parking</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('parking_spaces', $property->parking_spaces) == $i ? 'selected' : '' }}>
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
                                <option value="furnished" {{ old('furnishing_status', $property->furnishing_status) == 'furnished' ? 'selected' : '' }}>Furnished</option>
                                <option value="semi_furnished" {{ old('furnishing_status', $property->furnishing_status) == 'semi_furnished' ? 'selected' : '' }}>Semi-Furnished</option>
                                <option value="unfurnished" {{ old('furnishing_status', $property->furnishing_status) == 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                            </select>
                            <x-input-error :messages="$errors->get('furnishing_status')" class="mt-2" />
                        </div>

                        <!-- Update Notes (only for approved properties) -->
                        @if($property->status === 'active' && $property->version_status === 'original')
                        <div class="mb-4">
                            <x-input-label for="update_notes" :value="__('Update Notes (Optional)')" />
                            <textarea id="update_notes" name="update_notes" rows="3" 
                                     class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                     placeholder="Explain what changes you made and why...">{{ old('update_notes') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">These notes will be visible to admins when reviewing your update request.</p>
                            <x-input-error :messages="$errors->get('update_notes')" class="mt-2" />
                        </div>
                        @endif

                        <!-- Amenities -->
                        <div class="mb-4">
                            <x-input-label :value="__('Amenities & Features')" />
                            <div class="grid grid-cols-2 gap-4 mt-2">
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_balcony" value="1" {{ old('has_balcony', $property->has_balcony) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Balcony</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_garden" value="1" {{ old('has_garden', $property->has_garden) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Garden</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_pool" value="1" {{ old('has_pool', $property->has_pool) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Swimming Pool</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_gym" value="1" {{ old('has_gym', $property->has_gym) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Gym</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_security" value="1" {{ old('has_security', $property->has_security) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Security</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_elevator" value="1" {{ old('has_elevator', $property->has_elevator) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Elevator</span>
                                    </label>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_air_conditioning" value="1" {{ old('has_air_conditioning', $property->has_air_conditioning) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Air Conditioning</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_heating" value="1" {{ old('has_heating', $property->has_heating) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Heating</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_internet" value="1" {{ old('has_internet', $property->has_internet) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Internet</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_cable_tv" value="1" {{ old('has_cable_tv', $property->has_cable_tv) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Cable TV</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="pets_allowed" value="1" {{ old('pets_allowed', $property->pets_allowed) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Pets Allowed</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="smoking_allowed" value="1" {{ old('smoking_allowed', $property->smoking_allowed) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Smoking Allowed</span>
                                    </label>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('amenities')" class="mt-2" />
                        </div>

                        <!-- Current Images -->
                        @if($property->images && $property->images->count() > 0)
                            <div class="mb-4">
                                <x-input-label :value="__('Current Images')" />
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                                    @foreach($property->images as $image)
                                        <div class="relative group">
                                            <img src="{{ Storage::url($image->path) }}" 
                                                 alt="{{ $image->alt_text ?? $property->title }}"
                                                 class="w-full h-32 object-cover rounded-lg">
                                            @if($image->is_primary)
                                                <div class="absolute top-1 left-1 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                                    Primary
                                                </div>
                                            @endif
                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button type="button" 
                                                        onclick="deleteImage({{ $image->id }})"
                                                        class="bg-red-600 text-white p-1 rounded-full hover:bg-red-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Add More Images -->
                        <div class="mb-4">
                            <x-input-label for="new_images" :value="__('Add More Images')" />
                            <input id="new_images" type="file" name="new_images[]" multiple 
                                   accept="image/*" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            <p class="text-sm text-gray-500 mt-1">Upload additional images (max 2MB each, JPEG, PNG, JPG, GIF)</p>
                            <x-input-error :messages="$errors->get('new_images')" class="mt-2" />
                        </div>

                        <!-- Availability Status -->
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_available" value="1" 
                                       {{ old('is_available', $property->is_available) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Property is available for rent</span>
                            </label>
                        </div>

                        @if(auth()->user()->isAdmin())
                            <!-- Admin Status Control -->
                            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                                <x-input-label for="status" :value="__('Admin: Property Status')" />
                                <select id="status" name="status" 
                                       class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="pending" {{ old('status', $property->status) == 'pending' ? 'selected' : '' }}>
                                        Pending Review
                                    </option>
                                    <option value="active" {{ old('status', $property->status) == 'active' ? 'selected' : '' }}>
                                        Active (Visible to renters)
                                    </option>
                                    <option value="rejected" {{ old('status', $property->status) == 'rejected' ? 'selected' : '' }}>
                                        Rejected
                                    </option>
                                </select>
                                <p class="text-sm text-gray-600 mt-1">Only admins can change the property approval status.</p>
                            </div>
                        @else
                            <!-- Status Display for Non-Admins -->
                            <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-md">
                                <p class="text-sm text-gray-700">
                                    <strong>Current Status:</strong>
                                    @if($property->status === 'active')
                                        <span class="text-green-600 font-medium">✓ Active</span>
                                    @elseif($property->status === 'pending')
                                        <span class="text-yellow-600 font-medium">⏳ Pending Review</span>
                                    @else
                                        <span class="text-red-600 font-medium">✗ Rejected</span>
                                    @endif
                                </p>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end">
                            <x-primary-button class="ml-4">
                                {{ __('Update Property') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteImage(imageId) {
            if (confirm('Are you sure you want to delete this image?')) {
                fetch(`/images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting image');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting image');
                });
            }
        }
    </script>
</x-app-layout>