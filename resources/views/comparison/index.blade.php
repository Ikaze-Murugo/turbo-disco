@extends('layouts.app')

@section('title', 'Property Comparison - Murugo')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Property Comparison</h1>
                    <p class="mt-2 text-gray-600">Compare up to 4 properties side by side</p>
                </div>
                
                @if(!$isEmpty)
                <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-3">
                    <button onclick="clearComparison()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear All
                    </button>
                    
                    <button onclick="shareComparison()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            title="Share this comparison">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        Share Comparison
                    </button>
                    
                    <button onclick="printComparison()" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                    
                    <button onclick="exportComparison()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                </div>
                @endif
            </div>
        </div>

        @if($isEmpty)
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="mx-auto h-24 w-24 text-gray-400">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No properties to compare</h3>
            <p class="mt-2 text-gray-500">Add properties to your comparison list to see them side by side.</p>
            <div class="mt-6">
                <a href="{{ route('properties.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Browse Properties
                </a>
            </div>
        </div>
        @else
        <!-- Comparison Content -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Property Cards Header (Desktop) -->
            <div class="hidden lg:block">
                <div class="grid grid-cols-4 gap-4 p-6 border-b border-gray-200">
                    @foreach($properties as $index => $property)
                    <div class="comparison-property-card" data-property-id="{{ $property->id }}">
                        <!-- Property Image -->
                        <div class="relative aspect-[4/3] mb-4 rounded-lg overflow-hidden bg-gray-100">
                            @if($property->images->isNotEmpty())
                                <img src="{{ Storage::url($property->images->first()->path) }}" 
                                     alt="{{ $property->title }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Action Buttons -->
                            <div class="absolute top-2 right-2 flex gap-1">
                                <!-- Share Button -->
                                <button onclick="shareProperty({{ $property->id }})" 
                                        class="bg-blue-500 text-white rounded-full p-1 hover:bg-blue-600 transition-colors"
                                        title="Share this property">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                    </svg>
                                </button>
                                
                                <!-- Remove Button -->
                                <button onclick="removeFromComparison({{ $property->id }})" 
                                        class="bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                                        title="Remove from comparison">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Property Info -->
                        <div class="space-y-2">
                            <h3 class="font-semibold text-gray-900 line-clamp-2">{{ $property->title }}</h3>
                            <p class="text-sm text-gray-600">{{ $property->neighborhood }}</p>
                            <p class="text-lg font-bold text-blue-600">RWF {{ number_format($property->price) }}</p>
                            <div class="flex items-center text-sm text-gray-500">
                                <span>{{ $property->bedrooms }} bed</span>
                                <span class="mx-1">•</span>
                                <span>{{ $property->bathrooms }} bath</span>
                                <span class="mx-1">•</span>
                                <span>{{ $property->area }}m²</span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="mt-4 space-y-2">
                            <a href="{{ route('properties.show', $property) }}" 
                               class="block w-full text-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Mobile Property Cards -->
            <div class="lg:hidden">
                <div class="p-4 space-y-4">
                    @foreach($properties as $property)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start space-x-4">
                            <!-- Property Image -->
                            <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden bg-gray-100">
                                @if($property->images->isNotEmpty())
                                    <img src="{{ Storage::url($property->images->first()->path) }}" 
                                         alt="{{ $property->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Property Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 line-clamp-2">{{ $property->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $property->neighborhood }}</p>
                                <p class="text-lg font-bold text-blue-600">RWF {{ number_format($property->price) }}</p>
                                <div class="flex items-center text-sm text-gray-500 mt-1">
                                    <span>{{ $property->bedrooms }} bed</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ $property->bathrooms }} bath</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ $property->area }}m²</span>
                                </div>
                            </div>
                            
                            <!-- Remove Button -->
                            <button onclick="removeFromComparison({{ $property->id }})" 
                                    class="flex-shrink-0 text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Action Button -->
                        <div class="mt-3">
                            <a href="{{ route('properties.show', $property) }}" 
                               class="block w-full text-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Comparison Table -->
            @if($properties->count() > 1)
            <div class="border-t border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Feature</th>
                                @foreach($properties as $property)
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="truncate">{{ Str::limit($property->title, 20) }}</div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($comparisonData as $sectionKey => $section)
                            <!-- Section Header -->
                            <tr class="bg-gray-50">
                                <td colspan="{{ $properties->count() + 1 }}" class="px-6 py-3 text-sm font-medium text-gray-900">
                                    {{ $section['title'] }}
                                </td>
                            </tr>
                            
                            <!-- Section Fields -->
                            @foreach($section['fields'] as $fieldKey => $field)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $field['label'] }}
                                </td>
                                @foreach($properties as $property)
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    @if(isset($section['values'][$property->id][$fieldKey]))
                                        @if($field['type'] === 'boolean')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $section['values'][$property->id][$fieldKey] === 'Yes' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $section['values'][$property->id][$fieldKey] }}
                                            </span>
                                        @else
                                            {{ $section['values'][$property->id][$fieldKey] }}
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Add More Properties -->
        <div class="mt-8 text-center">
            <a href="{{ route('properties.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add More Properties
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Loading Overlay -->
<div id="comparison-loading" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Processing...</h3>
            <p class="text-sm text-gray-500 mt-1">Please wait while we update your comparison.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Comparison Management Functions
function removeFromComparison(propertyId) {
    showLoading();
    
    fetch(`/compare/remove/${propertyId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            // Remove property card from DOM
            const propertyCard = document.querySelector(`[data-property-id="${propertyId}"]`);
            if (propertyCard) {
                propertyCard.remove();
            }
            
            // Update comparison count in navigation
            updateComparisonCount(data.count);
            
            // Show success message
            showNotification(data.message, 'success');
            
            // If no properties left, reload page to show empty state
            if (data.count === 0) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } else {
            showNotification(data.message || 'Error removing property', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('Error removing property from comparison', 'error');
    });
}

function clearComparison() {
    if (!confirm('Are you sure you want to clear all properties from comparison?')) {
        return;
    }
    
    showLoading();
    
    fetch('/compare/clear', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            // Update comparison count in navigation
            updateComparisonCount(0);
            
            // Show success message
            showNotification(data.message, 'success');
            
            // Reload page to show empty state
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Error clearing comparison', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('Error clearing comparison', 'error');
    });
}

function printComparison() {
    window.print();
}

// Share individual property
function shareProperty(propertyId) {
    if (navigator.share) {
        navigator.share({
            title: 'Check out this property',
            url: window.location.origin + '/listings/' + propertyId
        })
        .then(() => {
            showNotification('Property shared successfully!', 'success');
        })
        .catch((error) => {
            if (error.name !== 'AbortError') {
                console.error('Error sharing property:', error);
                showNotification('Failed to share property', 'error');
            } else {
                console.log('Property share cancelled by user');
            }
        });
    } else {
        // Fallback: copy to clipboard
        const url = window.location.origin + '/listings/' + propertyId;
        navigator.clipboard.writeText(url).then(() => {
            showNotification('Property link copied to clipboard!', 'success');
        }).catch(() => {
            showNotification('Failed to copy link', 'error');
        });
    }
}

function shareComparison() {
    const comparisonData = {
        title: 'Property Comparison - Murugo',
        text: 'Check out this property comparison on Murugo',
        url: window.location.href
    };

    if (navigator.share) {
        navigator.share(comparisonData)
            .then(() => {
                showNotification('Comparison shared successfully!', 'success');
                // Track sharing analytics
                trackComparisonAction('share');
            })
            .catch((error) => {
                // Only show fallback for actual errors, not user cancellation
                if (error.name !== 'AbortError') {
                    console.error('Error sharing:', error);
                    fallbackShare();
                } else {
                    // User cancelled the share dialog - this is normal behavior
                    console.log('Share cancelled by user');
                }
            });
    } else {
        fallbackShare();
    }
}

function fallbackShare() {
    // Copy URL to clipboard
    navigator.clipboard.writeText(window.location.href)
        .then(() => {
            showNotification('Comparison link copied to clipboard!', 'success');
            // Track sharing analytics
            trackComparisonAction('share');
        })
        .catch(() => {
            // Show share modal as last resort
            showShareModal();
        });
}

function showShareModal() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Share Comparison</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Share Link:</label>
                    <input type="text" id="share-url" value="${window.location.href}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" readonly>
                </div>
                <div class="flex justify-end space-x-3">
                    <button onclick="this.closest('.fixed').remove()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Close
                    </button>
                    <button onclick="copyShareUrl()" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Copy Link
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function copyShareUrl() {
    const shareUrl = document.getElementById('share-url');
    shareUrl.select();
    document.execCommand('copy');
    showNotification('Link copied to clipboard!', 'success');
    trackComparisonAction('share');
    document.querySelector('.fixed.inset-0').remove();
}

function exportComparison() {
    const exportData = generateExportData();
    
    // Create downloadable JSON file
    const dataStr = JSON.stringify(exportData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    
    const link = document.createElement('a');
    link.href = url;
    link.download = `property-comparison-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    showNotification('Comparison exported successfully!', 'success');
    trackComparisonAction('export');
}

function generateExportData() {
    const properties = @json($properties);
    const comparisonData = @json($comparisonData);
    
    return {
        exportDate: new Date().toISOString(),
        source: 'Murugo Property Platform',
        properties: properties.map(property => ({
            id: property.id,
            title: property.title,
            price: property.price,
            type: property.type,
            bedrooms: property.bedrooms,
            bathrooms: property.bathrooms,
            area: property.area,
            neighborhood: property.neighborhood,
            address: property.address,
            features: {
                has_balcony: property.has_balcony,
                has_garden: property.has_garden,
                has_pool: property.has_pool,
                has_gym: property.has_gym,
                has_security: property.has_security,
                has_elevator: property.has_elevator,
                has_air_conditioning: property.has_air_conditioning,
                has_heating: property.has_heating,
                has_internet: property.has_internet,
                has_cable_tv: property.has_cable_tv,
                pets_allowed: property.pets_allowed,
                smoking_allowed: property.smoking_allowed,
                parking_spaces: property.parking_spaces
            }
        })),
        comparisonData: comparisonData,
        summary: {
            totalProperties: properties.length,
            priceRange: {
                min: Math.min(...properties.map(p => p.price)),
                max: Math.max(...properties.map(p => p.price))
            },
            averagePrice: properties.reduce((sum, p) => sum + p.price, 0) / properties.length,
            propertyTypes: [...new Set(properties.map(p => p.type))]
        }
    };
}

function trackComparisonAction(action) {
    fetch('/compare/track-completion', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            conversion_type: action
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Analytics tracked:', action);
        }
    })
    .catch(error => {
        console.error('Error tracking analytics:', error);
    });
}

function updateComparisonCount(count) {
    // Update comparison counter in navigation if it exists
    const compareCounter = document.getElementById('compare-count');
    if (compareCounter) {
        compareCounter.textContent = count;
        compareCounter.style.display = count > 0 ? 'inline' : 'none';
    }
    
    // Update comparison button text if it exists
    const compareButton = document.querySelector('[data-comparison-count]');
    if (compareButton) {
        compareButton.setAttribute('data-comparison-count', count);
        if (count > 0) {
            compareButton.textContent = `Compare (${count})`;
        } else {
            compareButton.textContent = 'Compare';
        }
    }
}

function showLoading() {
    document.getElementById('comparison-loading').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('comparison-loading').classList.add('hidden');
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Initialize comparison count on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update comparison count from server
    fetch('/compare/count')
        .then(response => response.json())
        .then(data => {
            updateComparisonCount(data.count);
        })
        .catch(error => {
            console.error('Error fetching comparison count:', error);
        });
});
</script>
@endpush

@push('styles')
<style>
/* Print Styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .comparison-property-card {
        break-inside: avoid;
    }
    
    body {
        font-size: 12px;
    }
    
    .bg-gray-50 {
        background: white !important;
    }
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .comparison-property-card {
        margin-bottom: 1rem;
    }
    
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }
}

/* Loading Animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Smooth Transitions */
.comparison-property-card {
    transition: all 0.3s ease;
}

.comparison-property-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
@endpush
