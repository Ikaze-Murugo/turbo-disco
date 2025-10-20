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

    @php
        // Fetch recent unread notifications (limit per type) and merge for display
        $reportNotifs = auth()->user()->reportNotifications()->latest()->take(5)->get();
        $msgReportNotifs = auth()->user()->messageReportNotifications()->latest()->take(5)->get();
        $allNotifications = $reportNotifs->concat($msgReportNotifs)->sortByDesc('created_at')->values();
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
                @if($allNotifications->count() > 0)
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">Recent</div>

                    @foreach($allNotifications as $n)
                        @php
                            $isReport = $n instanceof \App\Models\ReportNotification;
                            $isUnread = !$n->is_read;
                            $link = $isReport
                                ? route('reports.show', ['report' => $n->report_id]) . '#notification-' . $n->id
                                : route('message-reports.show', ['messageReport' => $n->message_report_id ?? ($n->metadata['message_report_id'] ?? null)]) . '#notification-' . $n->id;
                            $type = $isReport ? 'report' : 'message_report';
                        @endphp
                        <a href="{{ $link }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 {{ $isUnread ? 'bg-indigo-50' : '' }}"
                           @click.prevent="markOneAsRead('{{ $type }}', {{ $n->id }}, '{{ $link }}')">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 {{ $isReport ? 'bg-blue-100' : 'bg-green-100' }} rounded-full flex items-center justify-center">
                                        <span class="text-sm">{{ $isReport ? 'R' : 'M' }}</span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $n->title ?? 'Notification' }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $n->message }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                                </div>
                                @if($isUnread)
                                <div class="flex-shrink-0">
                                    <div class="h-2 w-2 bg-red-500 rounded-full"></div>
                                </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
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
        markOneAsRead(type, id, link) {
            fetch(`{{ route('notifications.mark-read', ['type' => 'TYPE', 'id' => 'ID']) }}`.replace('TYPE', type).replace('ID', id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(() => {
                window.location.href = link;
            }).catch(() => {
                window.location.href = link;
            });
        },
        
        toggleNotifications() {
            this.showNotifications = !this.showNotifications;
        },
        
        markAllAsRead() {
            fetch(`{{ route('notifications.mark-all-read') }}`, {
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
