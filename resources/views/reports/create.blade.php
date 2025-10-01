@extends('layouts.app')

@section('title', 'Submit Report')
@section('description', 'Report issues with properties, users, or messages')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Report Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('reports.index') }}" 
                       class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-heading-1 text-gray-900">Submit Report</h1>
                        <p class="text-body text-gray-600 mt-1">Help us maintain a safe and trustworthy platform</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Report Guidelines -->
            <div class="lg:col-span-1">
                <div class="card sticky top-6">
                    <div class="card-header">
                        <h3 class="text-heading-3">Report Guidelines</h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Be Specific</h4>
                                    <p class="text-xs text-gray-600">Provide clear details about the issue</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Include Evidence</h4>
                                    <p class="text-xs text-gray-600">Screenshots and documents help us investigate</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Stay Professional</h4>
                                    <p class="text-xs text-gray-600">Use respectful language in your report</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 p-4 bg-info-50 border border-info-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-info-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-info-800">Confidential</h4>
                                    <p class="text-xs text-info-700 mt-1">Your report will be handled confidentially and investigated promptly.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Form -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-heading-3">Report Details</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" id="report-form">
                            @csrf
                            
                            <!-- Report Type (Hidden) -->
                            <input type="hidden" name="report_type" value="{{ $reportType }}">
                            
                            @if($resource)
                                <!-- Resource Information -->
                                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Reporting:</h4>
                                    @if($reportType === 'property')
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                @if($resource->images->count() > 0)
                                                    <img src="{{ Storage::url($resource->images->first()->path) }}" 
                                                         alt="{{ $resource->title }}" 
                                                         class="h-12 w-12 rounded-lg object-cover">
                                                @else
                                                    <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $resource->title }}</p>
                                                <p class="text-sm text-gray-500">{{ $resource->location }}</p>
                                            </div>
                                        </div>
                                        <input type="hidden" name="reported_property_id" value="{{ $resource->id }}">
                                    @elseif($reportType === 'user')
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-12 w-12 bg-primary-100 rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-medium text-primary-600">
                                                        {{ substr($resource->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $resource->name }}</p>
                                                <p class="text-sm text-gray-500">{{ ucfirst($resource->role) }}</p>
                                            </div>
                                        </div>
                                        <input type="hidden" name="reported_user_id" value="{{ $resource->id }}">
                                    @elseif($reportType === 'message')
                                        <div class="p-3 bg-white rounded border">
                                            <p class="text-sm text-gray-900">{{ Str::limit($resource->content, 100) }}</p>
                                            <p class="text-xs text-gray-500 mt-1">From: {{ $resource->sender->name }}</p>
                                        </div>
                                        <input type="hidden" name="reported_message_id" value="{{ $resource->id }}">
                                    @endif
                                </div>
                            @endif

                            <!-- Category Selection -->
                            <div class="form-group mb-6">
                                <label for="category" class="form-label">Report Category</label>
                                <select id="category" name="category" class="form-input" required>
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                                            {{ $category->description }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="form-error mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Title -->
                            <div class="form-group mb-6">
                                <label for="title" class="form-label">Report Title</label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       class="form-input"
                                       value="{{ old('title') }}" 
                                       placeholder="Brief description of the issue"
                                       required>
                                @error('title')
                                    <p class="form-error mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="form-group mb-6">
                                <label for="description" class="form-label">Detailed Description</label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="6" 
                                          class="form-input resize-none"
                                          placeholder="Please provide as much detail as possible about the issue..."
                                          required>{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="form-error mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Evidence Files -->
                            <div class="form-group mb-6">
                                <label for="evidence_files" class="form-label">Evidence (Optional)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors" 
                                     id="file-drop-zone">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="evidence_files" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                                <span>Upload files</span>
                                                <input id="evidence_files" 
                                                       name="evidence_files[]" 
                                                       type="file" 
                                                       multiple
                                                       accept="image/*,.pdf"
                                                       class="sr-only">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, PDF up to 2MB each (max 5 files)</p>
                                    </div>
                                </div>
                                @error('evidence_files')
                                    <p class="form-error mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Priority (for certain categories) -->
                            <div class="form-group mb-6" id="priority-section" style="display: none;">
                                <label for="priority" class="form-label">Priority Level</label>
                                <select id="priority" name="priority" class="form-input">
                                    <option value="medium">Medium (Default)</option>
                                    <option value="low">Low</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                                @error('priority')
                                    <p class="form-error mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('reports.index') }}" class="btn btn-outline">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    Submit Report
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
    // Show priority section for certain categories
    const categorySelect = document.getElementById('category');
    const prioritySection = document.getElementById('priority-section');
    const highPriorityCategories = ['fraud', 'harassment', 'safety'];
    
    categorySelect.addEventListener('change', function() {
        if (highPriorityCategories.includes(this.value)) {
            prioritySection.style.display = 'block';
            document.getElementById('priority').value = 'high';
        } else {
            prioritySection.style.display = 'none';
            document.getElementById('priority').value = 'medium';
        }
    });
    
    // File drop zone functionality
    const dropZone = document.getElementById('file-drop-zone');
    const fileInput = document.getElementById('evidence_files');
    
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('border-primary-400', 'bg-primary-50');
    });
    
    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-primary-400', 'bg-primary-50');
    });
    
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-primary-400', 'bg-primary-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            updateFileDisplay();
        }
    });
    
    fileInput.addEventListener('change', updateFileDisplay);
    
    function updateFileDisplay() {
        const files = fileInput.files;
        if (files.length > 0) {
            const fileList = Array.from(files).map(file => file.name).join(', ');
            dropZone.innerHTML = `
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">${files.length} file(s) selected</p>
                    <p class="text-xs text-gray-500 truncate">${fileList}</p>
                </div>
            `;
        }
    }
    
    // Auto-resize textarea
    const descriptionTextarea = document.getElementById('description');
    descriptionTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 200) + 'px';
    });
});
</script>
@endpush
@endsection