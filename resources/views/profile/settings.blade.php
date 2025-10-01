<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Account Settings
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Manage your account settings and preferences
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('profile.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    Back to Profile
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                <!-- Change Password -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
                        <form method="POST" action="{{ route('profile.password') }}">
                            @csrf
                            @method('PATCH')
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    @error('current_password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="password" id="password" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                        Update Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notification Preferences -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Preferences</h3>
                        <form method="POST" action="{{ route('profile.preferences') }}">
                            @csrf
                            @method('PATCH')
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="email_notifications" class="text-sm font-medium text-gray-700">Email Notifications</label>
                                        <p class="text-xs text-gray-500">Receive notifications via email</p>
                                    </div>
                                    <input type="checkbox" name="preferences[email_notifications]" id="email_notifications" value="1"
                                           {{ old('preferences.email_notifications', $user->preferences['email_notifications'] ?? true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="property_updates" class="text-sm font-medium text-gray-700">Property Updates</label>
                                        <p class="text-xs text-gray-500">Get notified about new properties matching your criteria</p>
                                    </div>
                                    <input type="checkbox" name="preferences[property_updates]" id="property_updates" value="1"
                                           {{ old('preferences.property_updates', $user->preferences['property_updates'] ?? true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="message_notifications" class="text-sm font-medium text-gray-700">Message Notifications</label>
                                        <p class="text-xs text-gray-500">Get notified when you receive new messages</p>
                                    </div>
                                    <input type="checkbox" name="preferences[message_notifications]" id="message_notifications" value="1"
                                           {{ old('preferences.message_notifications', $user->preferences['message_notifications'] ?? true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="marketing_emails" class="text-sm font-medium text-gray-700">Marketing Emails</label>
                                        <p class="text-xs text-gray-500">Receive promotional content and updates</p>
                                    </div>
                                    <input type="checkbox" name="preferences[marketing_emails]" id="marketing_emails" value="1"
                                           {{ old('preferences.marketing_emails', $user->preferences['marketing_emails'] ?? false) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                        Save Preferences
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Privacy Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Privacy Settings</h3>
                        <form method="POST" action="{{ route('profile.preferences') }}">
                            @csrf
                            @method('PATCH')
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="profile_visibility" class="text-sm font-medium text-gray-700">Profile Visibility</label>
                                        <p class="text-xs text-gray-500">Make your profile visible to other users</p>
                                    </div>
                                    <select name="preferences[profile_visibility]" id="profile_visibility" 
                                            class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="public" {{ old('preferences.profile_visibility', $user->preferences['profile_visibility'] ?? 'public') === 'public' ? 'selected' : '' }}>Public</option>
                                        <option value="private" {{ old('preferences.profile_visibility', $user->preferences['profile_visibility'] ?? 'public') === 'private' ? 'selected' : '' }}>Private</option>
                                    </select>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="contact_preferences" class="text-sm font-medium text-gray-700">Contact Preferences</label>
                                        <p class="text-xs text-gray-500">Allow other users to contact you directly</p>
                                    </div>
                                    <input type="checkbox" name="preferences[contact_preferences]" id="contact_preferences" value="1"
                                           {{ old('preferences.contact_preferences', $user->preferences['contact_preferences'] ?? true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="show_online_status" class="text-sm font-medium text-gray-700">Show Online Status</label>
                                        <p class="text-xs text-gray-500">Display when you were last active</p>
                                    </div>
                                    <input type="checkbox" name="preferences[show_online_status]" id="show_online_status" value="1"
                                           {{ old('preferences.show_online_status', $user->preferences['show_online_status'] ?? true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                        Save Privacy Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Account Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Account Actions</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium text-yellow-800">Download Your Data</h4>
                                    <p class="text-xs text-yellow-600">Request a copy of all your data</p>
                                </div>
                                <button class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors duration-200">
                                    Request Data
                                </button>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium text-red-800">Delete Account</h4>
                                    <p class="text-xs text-red-600">Permanently delete your account and all data</p>
                                </div>
                                <button onclick="confirmDelete()" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors duration-200">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                // Create a form to submit the delete request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("profile.destroy") }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                const passwordField = document.createElement('input');
                passwordField.type = 'password';
                passwordField.name = 'password';
                passwordField.placeholder = 'Enter your password to confirm';
                passwordField.required = true;
                passwordField.className = 'mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                form.appendChild(passwordField);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>