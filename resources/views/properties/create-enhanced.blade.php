<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">List Your Property</h1>
            
            <form method="POST" action="{{ route('properties.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                {{-- Basic Information --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Title *</label>
                            <input type="text" name="title" value="{{ old('title') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   placeholder="e.g., Beautiful 3-bedroom house in Kacyiru" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Type *</label>
                            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Type</option>
                                <option value="house" {{ old('type') == 'house' ? 'selected' : '' }}>House</option>
                                <option value="apartment" {{ old('type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                <option value="studio" {{ old('type') == 'studio' ? 'selected' : '' }}>Studio</option>
                                <option value="condo" {{ old('type') == 'condo' ? 'selected' : '' }}>Condo</option>
                                <option value="villa" {{ old('type') == 'villa' ? 'selected' : '' }}>Villa</option>
                                <option value="townhouse" {{ old('type') == 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                                <option value="commercial" {{ old('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Rent (RWF) *</label>
                            <input type="number" name="price" value="{{ old('price') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   placeholder="e.g., 500000" required>
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Area (sqm)</label>
                            <input type="number" name="area" value="{{ old('area') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   placeholder="e.g., 120">
                            @error('area')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bedrooms *</label>
                            <input type="number" name="bedrooms" value="{{ old('bedrooms') }}" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('bedrooms')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bathrooms *</label>
                            <input type="number" name="bathrooms" value="{{ old('bathrooms') }}" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('bathrooms')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Parking Spaces</label>
                            <input type="number" name="parking_spaces" value="{{ old('parking_spaces') }}" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('parking_spaces')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Furnishing Status</label>
                            <select name="furnishing_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Status</option>
                                <option value="furnished" {{ old('furnishing_status') == 'furnished' ? 'selected' : '' }}>Furnished</option>
                                <option value="semi_furnished" {{ old('furnishing_status') == 'semi_furnished' ? 'selected' : '' }}>Semi-furnished</option>
                                <option value="unfurnished" {{ old('furnishing_status') == 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                            </select>
                            @error('furnishing_status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea name="description" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                  placeholder="Describe your property in detail..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                {{-- Location Information --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Location</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Address *</label>
                            <input type="text" name="address" value="{{ old('address') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   placeholder="e.g., KG 123 St, Kacyiru, Kigali, Rwanda" required>
                            <p class="text-sm text-gray-500 mt-1">Enter the complete address for automatic location detection</p>
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Neighborhood</label>
                            <input type="text" name="neighborhood" value="{{ old('neighborhood') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., Kacyiru, Nyarutarama, Kimisagara">
                            @error('neighborhood')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Latitude (Optional)</label>
                            <input type="number" name="latitude" value="{{ old('latitude') }}" step="any"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., -1.9441">
                            @error('latitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Longitude (Optional)</label>
                            <input type="number" name="longitude" value="{{ old('longitude') }}" step="any"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., 30.0619">
                            @error('longitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                {{-- Images --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Property Images</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Images</label>
                            <input type="file" name="images[]" multiple accept="image/*" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Upload multiple images (JPG, PNG, max 5MB each)</p>
                            @error('images.*')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div id="image-types-container" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image Types</label>
                            <div id="image-types-list" class="space-y-2">
                                <!-- Image type selectors will be added here by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Blueprints (Optional) --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Ground Blueprints (Optional)</h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Blueprints</label>
                        <input type="file" name="blueprints[]" multiple accept=".pdf,.jpg,.jpeg,.png" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Upload floor plans, site plans, or other blueprints (PDF, JPG, PNG, max 10MB each)</p>
                        @error('blueprints.*')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                {{-- Amenities --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Amenities</h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="has_parking" value="1" {{ old('has_parking') ? 'checked' : '' }} class="mr-2">
                            <span>Parking</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_security" value="1" {{ old('has_security') ? 'checked' : '' }} class="mr-2">
                            <span>Security</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_air_conditioning" value="1" {{ old('has_air_conditioning') ? 'checked' : '' }} class="mr-2">
                            <span>Air Conditioning</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_balcony" value="1" {{ old('has_balcony') ? 'checked' : '' }} class="mr-2">
                            <span>Balcony</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_garden" value="1" {{ old('has_garden') ? 'checked' : '' }} class="mr-2">
                            <span>Garden</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_pool" value="1" {{ old('has_pool') ? 'checked' : '' }} class="mr-2">
                            <span>Pool</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_gym" value="1" {{ old('has_gym') ? 'checked' : '' }} class="mr-2">
                            <span>Gym</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_elevator" value="1" {{ old('has_elevator') ? 'checked' : '' }} class="mr-2">
                            <span>Elevator</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_heating" value="1" {{ old('has_heating') ? 'checked' : '' }} class="mr-2">
                            <span>Heating</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_internet" value="1" {{ old('has_internet') ? 'checked' : '' }} class="mr-2">
                            <span>Internet</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_cable_tv" value="1" {{ old('has_cable_tv') ? 'checked' : '' }} class="mr-2">
                            <span>Cable TV</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="pets_allowed" value="1" {{ old('pets_allowed') ? 'checked' : '' }} class="mr-2">
                            <span>Pets Allowed</span>
                        </label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('properties.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        List Property
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Handle image type selection
        document.querySelector('input[name="images[]"]').addEventListener('change', function(e) {
            const files = e.target.files;
            const container = document.getElementById('image-types-container');
            const list = document.getElementById('image-types-list');
            
            if (files.length > 0) {
                container.classList.remove('hidden');
                list.innerHTML = '';
                
                Array.from(files).forEach((file, index) => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center space-x-2';
                    div.innerHTML = `
                        <span class="text-sm text-gray-600 w-32 truncate">${file.name}</span>
                        <select name="image_types[${index}]" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="interior">Interior</option>
                            <option value="exterior">Exterior</option>
                            <option value="kitchen">Kitchen</option>
                            <option value="bathroom">Bathroom</option>
                            <option value="bedroom">Bedroom</option>
                            <option value="living_room">Living Room</option>
                            <option value="garden">Garden</option>
                            <option value="parking">Parking</option>
                        </select>
                    `;
                    list.appendChild(div);
                });
            } else {
                container.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
