@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Email Preferences</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('email.preferences.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Email Categories</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Announcements</label>
                            <p class="text-sm text-gray-500">Important platform updates and announcements</p>
                        </div>
                        <input type="checkbox" name="receive_announcements" value="1" 
                               {{ $preferences->receive_announcements ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Promotional Emails</label>
                            <p class="text-sm text-gray-500">Special offers and promotional content</p>
                        </div>
                        <input type="checkbox" name="receive_promotions" value="1" 
                               {{ $preferences->receive_promotions ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700">System Emails</label>
                            <p class="text-sm text-gray-500">Account notifications and system updates</p>
                        </div>
                        <input type="checkbox" name="receive_system_emails" value="1" 
                               {{ $preferences->receive_system_emails ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Newsletters</label>
                            <p class="text-sm text-gray-500">Regular newsletters with platform news and tips</p>
                        </div>
                        <input type="checkbox" name="receive_newsletters" value="1" 
                               {{ $preferences->receive_newsletters ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Property Updates</label>
                            <p class="text-sm text-gray-500">Notifications about your properties and listings</p>
                        </div>
                        <input type="checkbox" name="receive_property_updates" value="1" 
                               {{ $preferences->receive_property_updates ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Message Notifications</label>
                            <p class="text-sm text-gray-500">Notifications about new messages and conversations</p>
                        </div>
                        <input type="checkbox" name="receive_message_notifications" value="1" 
                               {{ $preferences->receive_message_notifications ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Email Frequency</h2>
                
                <div>
                    <label for="frequency" class="block text-sm font-medium text-gray-700 mb-2">How often would you like to receive emails?</label>
                    <select name="frequency" id="frequency" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($frequencyOptions as $value => $label)
                            <option value="{{ $value }}" {{ $preferences->frequency === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Save Preferences
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
