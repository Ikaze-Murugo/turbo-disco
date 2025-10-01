{{-- Notifications Component --}}
@auth
    @php
        try {
            $unreadCount = auth()->user()->unreadReportNotifications()->count() + 
                          auth()->user()->unreadMessageReportNotifications()->count();
        } catch (Exception $e) {
            $unreadCount = 0;
        }
    @endphp

    <div x-data="notifications()" class="relative">
        <!-- Notification Bell -->
        <button @click="toggleNotifications()" 
                class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            </svg>
            
            <!-- Unread Count Badge -->
            @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 rounded-full text-xs text-white flex items-center justify-center font-medium animate-pulse">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </button>

        <!-- Notifications Dropdown -->
        <div x-show="showNotifications" 
             @click.away="showNotifications = false"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
            
            <!-- Header -->
            <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                    @if($unreadCount > 0)
                        <button @click="markAllAsRead()" 
                                class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                            Mark all as read
                        </button>
                    @endif
                </div>
            </div>

            <!-- Notifications List -->
            <div class="max-h-96 overflow-y-auto">
                @if($unreadCount > 0)
                    <!-- Recent Notifications -->
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                        Recent
                    </div>
                    
                    <!-- Sample notification items - replace with actual notifications -->
                    <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">Report Status Update</p>
                                <p class="text-sm text-gray-500">Your property report has been resolved</p>
                                <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="h-2 w-2 bg-red-500 rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">New Message</p>
                                <p class="text-sm text-gray-500">You have a new message from a potential tenant</p>
                                <p class="text-xs text-gray-400 mt-1">4 hours ago</p>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="h-2 w-2 bg-red-500 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- No Notifications -->
                    <div class="px-4 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                        <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            @if($unreadCount > 0)
                <div class="px-4 py-3 border-t border-gray-200">
                    <a href="{{ route('reports.my-reports') }}" 
                       class="block text-center text-sm text-primary-600 hover:text-primary-700 font-medium">
                        View all notifications
                    </a>
                </div>
            @endif
        </div>
    </div>
@endauth

<script>
function notifications() {
    return {
        showNotifications: false,
        
        toggleNotifications() {
            this.showNotifications = !this.showNotifications;
        },
        
        markAllAsRead() {
            // Implement mark all as read functionality
            fetch('/api/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to update notification count
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error marking notifications as read:', error);
            });
        }
    }
}
</script>
