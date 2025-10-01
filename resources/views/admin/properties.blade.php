<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('All Properties') }}
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No properties found</h3>
                        <p class="text-gray-500">No active properties are currently available.</p>
                    </div>
                </div>
            @else
                <!-- Properties List -->
                <div class="space-y-6">
                    @foreach($properties as $property)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <h3 class="text-xl font-semibold text-gray-900">{{ $property->title }}</h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $property->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                                   ($property->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($property->priority) }} Priority
                                            </span>
                                            @if($property->is_featured)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Featured
                                                </span>
                                            @endif
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
                                                    <strong>Type:</strong> {{ ucfirst($property->type) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Bedrooms:</strong> {{ $property->bedrooms }} | 
                                                    <strong>Bathrooms:</strong> {{ $property->bathrooms }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Landlord:</strong> {{ $property->landlord->name }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Views:</strong> {{ $property->view_count }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <p class="text-sm text-gray-700 mb-4">{{ Str::limit($property->description, 200) }}</p>
                                        
                                        <div class="flex items-center text-sm text-gray-500">
                                            <span>Listed: {{ $property->created_at->format('M d, Y') }}</span>
                                            @if($property->featured_until)
                                                <span class="ml-4">Featured until: {{ $property->featured_until->format('M d, Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Priority Management -->
                                    <div class="ml-6">
                                        <form method="POST" action="{{ route('admin.properties.priority', $property) }}" class="space-y-3">
                                            @csrf
                                            @method('PATCH')
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                                <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                    <option value="low" {{ $property->priority === 'low' ? 'selected' : '' }}>Low</option>
                                                    <option value="medium" {{ $property->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="high" {{ $property->priority === 'high' ? 'selected' : '' }}>High</option>
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="is_featured" value="1" 
                                                           {{ $property->is_featured ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700">Featured</span>
                                                </label>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Featured Until</label>
                                                <input type="date" name="featured_until" 
                                                       value="{{ $property->featured_until ? $property->featured_until->format('Y-m-d') : '' }}"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            </div>
                                            
                                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                                Update Priority
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
