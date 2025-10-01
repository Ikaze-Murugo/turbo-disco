<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Favorites') }}
            </h2>
            <button onclick="showCreateWishlistModal()" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                Create New Wishlist
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Wishlist Tabs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex space-x-1 border-b border-gray-200">
                        @foreach($wishlists as $wishlist)
                            <button onclick="switchWishlist('{{ $wishlist }}')" 
                                    class="px-4 py-2 text-sm font-medium {{ $listName === $wishlist ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                                {{ ucfirst($wishlist) }}
                                <span class="ml-2 bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                    {{ $favorites->where('list_name', $wishlist)->count() }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Favorites Grid -->
            @if($favorites->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="h-24 w-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No favorites yet</h3>
                        <p class="text-gray-500 mb-4">Start exploring properties and add them to your favorites!</p>
                        <a href="{{ route('properties.search') }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                            Browse Properties
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($favorites as $favorite)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="h-48 bg-gray-300 flex items-center justify-center relative overflow-hidden">
                                @if($favorite->property->primaryImage)
                                    <img src="{{ Storage::url($favorite->property->primaryImage->path) }}" 
                                         alt="{{ $favorite->property->primaryImage->alt_text ?? $favorite->property->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    </svg>
                                @endif
                                
                                <!-- Favorite Actions -->
                                <div class="absolute top-2 right-2 flex space-x-1">
                                    <button onclick="removeFavorite({{ $favorite->property->id }})" 
                                            class="bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h3 class="font-semibold text-lg text-gray-900 mb-2">
                                    <a href="{{ route('properties.show', $favorite->property) }}" 
                                       class="hover:text-blue-600 transition-colors">
                                        {{ $favorite->property->title }}
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 text-sm mb-2">{{ $favorite->property->location }}</p>
                                
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-lg font-bold text-green-600">
                                        {{ number_format($favorite->property->price) }} RWF/month
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ $favorite->property->bedrooms }} bed • {{ $favorite->property->bathrooms }} bath
                                    </span>
                                </div>
                                
                                @if($favorite->notes)
                                    <div class="bg-gray-50 p-2 rounded text-sm text-gray-600 mb-3">
                                        <strong>Notes:</strong> {{ $favorite->notes }}
                                    </div>
                                @endif
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('properties.show', $favorite->property) }}" 
                                       class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                        View Details
                                    </a>
                                    <button onclick="editFavorite({{ $favorite->property->id }}, '{{ $favorite->list_name }}', '{{ $favorite->notes }}')" 
                                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                                        Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Create Wishlist Modal -->
    <div id="createWishlistModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Create New Wishlist</h3>
                    <form id="createWishlistForm">
                        @csrf
                        <div class="mb-4">
                            <label for="list_name" class="block text-sm font-medium text-gray-700 mb-2">Wishlist Name</label>
                            <input type="text" id="list_name" name="list_name" 
                                   class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                   placeholder="e.g., Budget Properties, Luxury Homes" required>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="hideCreateWishlistModal()" 
                                    class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Favorite Modal -->
    <div id="editFavoriteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Favorite</h3>
                    <form id="editFavoriteForm">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label for="edit_list_name" class="block text-sm font-medium text-gray-700 mb-2">Wishlist</label>
                            <select id="edit_list_name" name="list_name" 
                                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach($wishlists as $wishlist)
                                    <option value="{{ $wishlist }}">{{ ucfirst($wishlist) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea id="edit_notes" name="notes" rows="3" 
                                      class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                      placeholder="Add personal notes about this property..."></textarea>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="hideEditFavoriteModal()" 
                                    class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPropertyId = null;

        function switchWishlist(listName) {
            window.location.href = `{{ route('favorites.index') }}?list=${listName}`;
        }

        function showCreateWishlistModal() {
            document.getElementById('createWishlistModal').classList.remove('hidden');
        }

        function hideCreateWishlistModal() {
            document.getElementById('createWishlistModal').classList.add('hidden');
            document.getElementById('createWishlistForm').reset();
        }

        function showEditFavoriteModal() {
            document.getElementById('editFavoriteModal').classList.remove('hidden');
        }

        function hideEditFavoriteModal() {
            document.getElementById('editFavoriteModal').classList.add('hidden');
            currentPropertyId = null;
        }

        function editFavorite(propertyId, listName, notes) {
            currentPropertyId = propertyId;
            document.getElementById('edit_list_name').value = listName;
            document.getElementById('edit_notes').value = notes;
            showEditFavoriteModal();
        }

        function removeFavorite(propertyId) {
            if (confirm('Are you sure you want to remove this property from your favorites?')) {
                fetch(`/properties/${propertyId}/favorite`, {
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
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing the favorite.');
                });
            }
        }

        // Create wishlist form submission
        document.getElementById('createWishlistForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("favorites.wishlist.create") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideCreateWishlistModal();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the wishlist.');
            });
        });

        // Edit favorite form submission
        document.getElementById('editFavoriteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentPropertyId) return;
            
            const formData = new FormData(this);
            
            fetch(`/properties/${currentPropertyId}/favorite`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideEditFavoriteModal();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the favorite.');
            });
        });
    </script>
</x-app-layout>
