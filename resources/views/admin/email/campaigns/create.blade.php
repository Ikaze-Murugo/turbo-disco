@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Create Email Campaign</h1>

    <form action="{{ route('admin.email.campaigns.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Campaign Name</label>
                    <input type="text" name="name" id="name" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="template_id" class="block text-sm font-medium text-gray-700">Template (Optional)</label>
                    <select name="template_id" id="template_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">No Template (Custom)</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('template_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                <input type="text" name="subject" id="subject" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       value="{{ old('subject') }}">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                <textarea name="content" id="content" rows="10" required
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('content') }}</textarea>
                <p class="mt-2 text-sm text-gray-500">
                    Available variables: {{user_name}}, {{user_email}}, {{platform_name}}, {{unsubscribe_link}}, {{current_date}}, {{current_year}}
                </p>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label for="target_audience" class="block text-sm font-medium text-gray-700">Target Audience</label>
                <select name="target_audience" id="target_audience" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Target Audience</option>
                    <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>All Users</option>
                    <option value="landlords" {{ old('target_audience') == 'landlords' ? 'selected' : '' }}>Landlords Only</option>
                    <option value="renters" {{ old('target_audience') == 'renters' ? 'selected' : '' }}>Renters Only</option>
                    <option value="admin" {{ old('target_audience') == 'admin' ? 'selected' : '' }}>Admins Only</option>
                    <option value="custom" {{ old('target_audience') == 'custom' ? 'selected' : '' }}>Custom Criteria</option>
                </select>
                @error('target_audience')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Schedule (Optional)</label>
                <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       value="{{ old('scheduled_at') }}">
                <p class="mt-2 text-sm text-gray-500">Leave empty to save as draft</p>
                @error('scheduled_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.email.campaigns.index') }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Create Campaign
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
