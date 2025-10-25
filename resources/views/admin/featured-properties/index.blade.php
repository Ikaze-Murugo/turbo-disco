@extends('layouts.app')

@section('title', 'Featured Properties Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Featured Properties Management</h1>
                        <p class="mt-2 text-gray-600">Manage featured properties, set durations, and track performance</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="showAnalytics()" class="btn btn-outline">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Analytics
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Properties</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_properties']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Featured Properties</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['featured_properties']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Expiring Soon</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['expiring_soon']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" class="flex flex-col md:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search properties or landlords..."
                               class="form-input w-full">
                    </div>

                    <!-- Featured Status Filter -->
                    <div class="md:w-48">
                        <select name="featured_status" class="form-input w-full">
                            <option value="">All Properties</option>
                            <option value="featured" {{ request('featured_status') === 'featured' ? 'selected' : '' }}>Featured Only</option>
                            <option value="not_featured" {{ request('featured_status') === 'not_featured' ? 'selected' : '' }}>Not Featured</option>
                        </select>
                    </div>

                    <!-- Priority Filter -->
                    <div class="md:w-48">
                        <select name="priority" class="form-input w-full">
                            <option value="">All Priorities</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High Priority</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium Priority</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low Priority</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </button>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form id="bulk-actions-form" method="POST">
                    @csrf
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300">
                            <label for="select-all" class="text-sm font-medium text-gray-700">Select All</label>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <select name="bulk_action" id="bulk-action" class="form-input" disabled>
                                <option value="">Choose Action</option>
                                <option value="feature">Feature Selected</option>
                                <option value="unfeature">Unfeature Selected</option>
                            </select>
                            
                            <button type="button" id="apply-bulk-action" class="btn btn-primary" disabled>
                                Apply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Properties Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all-header" class="rounded border-gray-300">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Landlord</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured Until</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($properties as $property)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="property_ids[]" value="{{ $property->id }}" class="property-checkbox rounded border-gray-300">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if($property->images->where('is_primary', true)->first())
                                            <img class="h-12 w-12 rounded-lg object-cover" 
                                                 src="{{ Storage::url($property->images->where('is_primary', true)->first()->path) }}" 
                                                 alt="{{ $property->title }}">
                                        @else
                                            <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($property->title, 40) }}</div>
                                        <div class="text-sm text-gray-500">{{ $property->address }}</div>
                                        <div class="text-sm text-gray-500">RWF {{ number_format($property->price) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $property->landlord->name }}</div>
                                <div class="text-sm text-gray-500">{{ $property->landlord->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($property->is_featured)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Featured
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Not Featured
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($property->is_featured && $property->featured_until)
                                    <div class="flex items-center">
                                        <span>{{ $property->featured_until->format('M d, Y') }}</span>
                                        @if($property->featured_until->isPast())
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Expired
                                            </span>
                                        @elseif($property->featured_until->diffInDays() <= 3)
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                Expiring Soon
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($property->is_featured)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($property->priority === 'high') bg-red-100 text-red-800
                                        @elseif($property->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($property->priority) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($property->is_featured)
                                        <button onclick="unfeatureProperty({{ $property->id }})" 
                                                class="text-red-600 hover:text-red-900">
                                            Unfeature
                                        </button>
                                    @else
                                        <button onclick="showFeatureModal({{ $property->id }})" 
                                                class="text-blue-600 hover:text-blue-900">
                                            Feature
                                        </button>
                                    @endif
                                    <a href="{{ route('properties.show', $property) }}" 
                                       class="text-gray-600 hover:text-gray-900">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No properties found</h3>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($properties->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $properties->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Feature Property Modal -->
<div id="feature-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="feature-form" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Feature Property</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                        <select name="duration" class="form-input w-full" required>
                            <option value="7">7 days</option>
                            <option value="14" selected>14 days</option>
                            <option value="30">30 days</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select name="priority" class="form-input w-full" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="btn btn-primary">
                        Feature Property
                    </button>
                    <button type="button" onclick="closeFeatureModal()" class="btn btn-outline mr-3">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Analytics Modal -->
<div id="analytics-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Featured Properties Analytics</h3>
                <div id="analytics-content">
                    <div class="animate-pulse">
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeAnalyticsModal()" class="btn btn-outline">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Bulk selection functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const selectAllHeaderCheckbox = document.getElementById('select-all-header');
    const propertyCheckboxes = document.querySelectorAll('.property-checkbox');
    const bulkActionSelect = document.getElementById('bulk-action');
    const applyBulkButton = document.getElementById('apply-bulk-action');

    // Select all functionality
    function updateSelectAll() {
        const checkedBoxes = document.querySelectorAll('.property-checkbox:checked');
        const totalBoxes = propertyCheckboxes.length;
        
        selectAllCheckbox.checked = checkedBoxes.length === totalBoxes;
        selectAllHeaderCheckbox.checked = checkedBoxes.length === totalBoxes;
        
        bulkActionSelect.disabled = checkedBoxes.length === 0;
        applyBulkButton.disabled = checkedBoxes.length === 0 || !bulkActionSelect.value;
    }

    selectAllCheckbox.addEventListener('change', function() {
        propertyCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectAll();
    });

    selectAllHeaderCheckbox.addEventListener('change', function() {
        propertyCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectAll();
    });

    propertyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAll);
    });

    bulkActionSelect.addEventListener('change', function() {
        applyBulkButton.disabled = document.querySelectorAll('.property-checkbox:checked').length === 0 || !this.value;
    });

    // Bulk action form submission
    applyBulkButton.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.property-checkbox:checked');
        const action = bulkActionSelect.value;
        
        if (checkedBoxes.length === 0 || !action) return;

        const form = document.getElementById('bulk-actions-form');
        const propertyIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        // Add property IDs to form
        propertyIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'property_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        if (action === 'feature') {
            // Show feature modal for bulk action
            showBulkFeatureModal(propertyIds);
        } else if (action === 'unfeature') {
            if (confirm(`Are you sure you want to unfeature ${propertyIds.length} properties?`)) {
                form.action = '{{ route("admin.featured-properties.bulk-unfeature") }}';
                form.method = 'POST';
                form.submit();
            }
        }
    });
});

// Feature property modal
function showFeatureModal(propertyId) {
    const modal = document.getElementById('feature-modal');
    const form = document.getElementById('feature-form');
    form.action = `/admin/featured-properties/${propertyId}/feature`;
    modal.classList.remove('hidden');
}

function closeFeatureModal() {
    document.getElementById('feature-modal').classList.add('hidden');
}

// Unfeature property
function unfeatureProperty(propertyId) {
    if (confirm('Are you sure you want to unfeature this property?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/featured-properties/${propertyId}/unfeature`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

// Analytics modal
function showAnalytics() {
    const modal = document.getElementById('analytics-modal');
    modal.classList.remove('hidden');
    
    // Fetch analytics data
    fetch('/admin/featured-properties/analytics')
        .then(response => response.json())
        .then(data => {
            document.getElementById('analytics-content').innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-medium text-blue-900">Featured Properties</h4>
                        <p class="text-2xl font-bold text-blue-600">${data.featured_count}</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <h4 class="font-medium text-orange-900">Expiring This Week</h4>
                        <p class="text-2xl font-bold text-orange-600">${data.expiring_this_week}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-medium text-green-900">Recently Featured</h4>
                        <p class="text-2xl font-bold text-green-600">${data.recently_featured}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="font-medium text-purple-900">By Priority</h4>
                        <div class="text-sm">
                            <div>High: ${data.by_priority.high || 0}</div>
                            <div>Medium: ${data.by_priority.medium || 0}</div>
                            <div>Low: ${data.by_priority.low || 0}</div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            document.getElementById('analytics-content').innerHTML = '<p class="text-red-600">Error loading analytics</p>';
        });
}

function closeAnalyticsModal() {
    document.getElementById('analytics-modal').classList.add('hidden');
}

// Bulk feature modal
function showBulkFeatureModal(propertyIds) {
    const modal = document.getElementById('feature-modal');
    const form = document.getElementById('feature-form');
    form.action = '{{ route("admin.featured-properties.bulk-feature") }}';
    
    // Add property IDs to form
    propertyIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'property_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    modal.classList.remove('hidden');
}
</script>
@endpush
