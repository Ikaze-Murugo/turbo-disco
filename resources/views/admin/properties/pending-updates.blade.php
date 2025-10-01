@extends('layouts.app')

@section('title', 'Pending Property Updates')
@section('description', 'Review and approve property updates from landlords')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Pending Property Updates</h1>
                    <p class="text-gray-600 mt-1">Review and approve property updates from landlords</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.properties.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        All Properties
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($pendingUpdates->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="h-24 w-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-2xl font-medium text-gray-900 mb-4">No Pending Updates</h3>
                <p class="text-gray-600 mb-8">All property updates have been reviewed.</p>
            </div>
        @else
            <!-- Pending Updates List -->
            <div class="space-y-6">
                @foreach($pendingUpdates as $update)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <h3 class="text-xl font-semibold text-gray-900">{{ $update->title }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending Update
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Property Details -->
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 mb-2">Property Details</h4>
                                            <div class="space-y-1 text-sm text-gray-600">
                                                <p><strong>Landlord:</strong> {{ $update->landlord->name }}</p>
                                                <p><strong>Type:</strong> {{ ucfirst($update->type) }}</p>
                                                <p><strong>Price:</strong> RWF {{ number_format($update->price) }}</p>
                                                <p><strong>Location:</strong> {{ $update->location }}</p>
                                                <p><strong>Requested:</strong> {{ $update->update_requested_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Changes Summary -->
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 mb-2">Changes Made</h4>
                                            @if($update->pending_changes)
                                                <div class="space-y-1 text-sm">
                                                    @foreach($update->pending_changes as $field => $value)
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                            <span class="font-medium">{{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-gray-500">No specific changes tracked</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($update->update_notes)
                                        <div class="mt-4 p-3 bg-gray-50 rounded-md">
                                            <h4 class="text-sm font-medium text-gray-900 mb-1">Landlord Notes</h4>
                                            <p class="text-sm text-gray-700">{{ $update->update_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Actions -->
                                <div class="ml-6 flex flex-col space-y-2">
                                    <a href="{{ route('properties.show', $update) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        View Details
                                    </a>
                                    
                                    <form method="POST" action="{{ route('admin.properties.approve-update', $update) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                onclick="return confirm('Are you sure you want to approve this update?')">
                                            Approve Update
                                        </button>
                                    </form>
                                    
                                    <button type="button" 
                                            onclick="showRejectModal({{ $update->id }})"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Reject Update
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $pendingUpdates->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Property Update</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for rejection
                    </label>
                    <textarea id="rejection_reason" 
                              name="rejection_reason" 
                              rows="4" 
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Please explain why this update is being rejected..."
                              required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="hideRejectModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Reject Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal(propertyId) {
    document.getElementById('rejectForm').action = `/admin/properties/${propertyId}/reject-update`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectForm').reset();
}
</script>
@endsection
