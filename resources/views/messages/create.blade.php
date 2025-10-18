@extends('layouts.app')

@section('title', 'Contact Landlord - ' . $property->title)
@section('description', 'Send a message to the landlord about ' . $property->title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Contact Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('properties.show', $property) }}" 
                       class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-heading-1 text-gray-900">Contact Landlord</h1>
                        <p class="text-body text-gray-600 mt-1">Send a message about this property</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Property Info Sidebar -->
            <div class="lg:col-span-1">
                <div class="card sticky top-6">
                    <div class="card-header">
                        <h3 class="text-heading-3">Property Details</h3>
                    </div>
                    <div class="card-body">
                        @if($property->images->count() > 0)
                            <img src="{{ Storage::url($property->images->first()->path) }}" 
                                 alt="{{ $property->title }}"
                                 class="w-full h-48 object-cover rounded-lg mb-4">
                        @else
                            <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <h4 class="font-semibold text-gray-900 mb-2">{{ $property->title }}</h4>
                        <p class="text-gray-600 mb-3">{{ $property->location }}</p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Price:</span>
                                <span class="text-sm font-medium text-gray-900">RWF {{ number_format($property->price) }}/month</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Type:</span>
                                <span class="text-sm font-medium text-gray-900 capitalize">{{ $property->type }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Bedrooms:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $property->bedrooms }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Bathrooms:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $property->bathrooms }}</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-primary-600">
                                        {{ substr($property->landlord->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $property->landlord->name }}</p>
                                    <p class="text-xs text-gray-500">Landlord</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Form -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-heading-3">Send Message</h3>
                        <p class="text-body text-gray-600">Write a message to {{ $property->landlord->name }}</p>
                    </div>
                    <div class="card-body">
                        <form x-data="{ isSubmitting: false }" @submit="isSubmitting = true" method="POST" action="{{ route('messages.store', $property) }}" id="message-form">
                            @csrf

                            <!-- Subject -->
                            <div class="form-group mb-6">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" 
                                       id="subject" 
                                       name="subject" 
                                       class="form-input"
                                       value="{{ old('subject', 'Inquiry about ' . $property->title) }}" 
                                       required 
                                       autofocus>
                                @error('subject')
                                    <p class="form-error mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Message Body -->
                            <div class="form-group mb-6">
                                <label for="body" class="form-label">Message</label>
                                <textarea id="body" 
                                          name="body" 
                                          rows="8" 
                                          class="form-input resize-none"
                                          placeholder="Hi {{ $property->landlord->name }}, I'm interested in your property at {{ $property->location }}. Could you please provide more information about..."
                                          required>{{ old('body') }}</textarea>
                                @error('body')
                                    <p class="form-error mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Message Guidelines -->
                            <div class="bg-info-50 border border-info-200 rounded-lg p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-info-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-info-800">Message Guidelines</h4>
                                        <div class="mt-2 text-sm text-info-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li>Be polite and professional in your message</li>
                                                <li>Include your contact information if you want to be reached directly</li>
                                                <li>Ask specific questions about the property</li>
                                                <li>Mention your preferred move-in date if applicable</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('properties.show', $property) }}" class="btn btn-outline">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                                    <span x-show="!isSubmitting" class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        Send Message
                                    </span>
                                    <span x-show="isSubmitting" x-cloak class="flex items-center">
                                        <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Sending...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textarea
    const messageBody = document.getElementById('body');
    messageBody.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 200) + 'px';
    });
    
    // Focus on message body
    messageBody.focus();
    
    // Character count (optional)
    const maxLength = 1000;
    const charCount = document.createElement('div');
    charCount.className = 'text-xs text-gray-500 mt-1';
    messageBody.parentNode.appendChild(charCount);
    
    function updateCharCount() {
        const remaining = maxLength - messageBody.value.length;
        charCount.textContent = `${messageBody.value.length}/${maxLength} characters`;
        if (remaining < 50) {
            charCount.className = 'text-xs text-warning-600 mt-1';
        } else {
            charCount.className = 'text-xs text-gray-500 mt-1';
        }
    }
    
    messageBody.addEventListener('input', updateCharCount);
    updateCharCount();
});
</script>
@endpush
@endsection