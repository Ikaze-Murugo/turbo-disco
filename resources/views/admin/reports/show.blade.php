<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Report #{{ $report->id }} - {{ $report->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Submitted by {{ $report->reporter->name }} on {{ $report->created_at->format('M d, Y \a\t g:i A') }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.reports.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    Back to Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Report Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Report Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Report Details</h3>
                                <div class="flex space-x-2">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        @if($report->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($report->status === 'investigating') bg-blue-100 text-blue-800
                                        @elseif($report->status === 'resolved') bg-green-100 text-green-800
                                        @elseif($report->status === 'dismissed') bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        @if($report->priority === 'urgent') bg-red-100 text-red-800
                                        @elseif($report->priority === 'high') bg-orange-100 text-orange-800
                                        @elseif($report->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @elseif($report->priority === 'low') bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($report->priority) }} Priority
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Report Type</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($report->report_type) }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Category</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($report->category) }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $report->description }}</p>
                                </div>

                                @if($report->evidence_urls && is_array($report->evidence_urls) && count($report->evidence_urls) > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Evidence</label>
                                    <div class="mt-2 space-y-2">
                                        @foreach($report->evidence_urls as $evidence)
                                        <div class="flex items-center space-x-2">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            <a href="{{ $evidence }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                                {{ basename($evidence) }}
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($report->reportedUser)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Reported User</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $report->reportedUser->name }} ({{ $report->reportedUser->email }})
                                    </p>
                                </div>
                                @endif

                                @if($report->reportedProperty)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Reported Property</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $report->reportedProperty->title }} - {{ $report->reportedProperty->address }}
                                    </p>
                                </div>
                                @endif

                                @if($report->reportedMessage)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Reported Message</label>
                                    <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                        <p class="text-sm text-gray-900">{{ $report->reportedMessage->body }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            From: {{ $report->reportedMessage->sender->name }} 
                                            on {{ $report->reportedMessage->created_at->format('M d, Y \a\t g:i A') }}
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Comments & Communication</h3>
                            
                            <!-- Add Comment Form -->
                            <form action="{{ route('admin.reports.comment', $report) }}" method="POST" class="mb-6">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="comment" class="block text-sm font-medium text-gray-700">Add Comment</label>
                                        <textarea name="comment" id="comment" rows="3" 
                                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                  placeholder="Add your comment or response..."></textarea>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">Internal Note (not visible to reporter)</span>
                                        </label>
                                    </div>
                                    <div>
                                        <button type="submit" 
                                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                            Add Comment
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Comments List -->
                            <div class="space-y-4">
                                @forelse($report->comments as $comment)
                                <div class="border-l-4 {{ $comment->is_internal ? 'border-gray-400' : 'border-blue-400' }} pl-4 py-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</span>
                                            @if($comment->is_internal)
                                            <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">Internal</span>
                                            @endif
                                            @if($comment->is_admin_comment)
                                            <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">Admin</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $comment->created_at->format('M d, Y \a\t g:i A') }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-700 whitespace-pre-wrap">{{ $comment->comment }}</p>
                                </div>
                                @empty
                                <p class="text-sm text-gray-500">No comments yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status Management -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status Management</h3>
                            
                            <!-- Update Status Form -->
                            <form action="{{ route('admin.reports.status', $report) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select name="status" id="status" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="investigating" {{ $report->status === 'investigating' ? 'selected' : '' }}>Investigating</option>
                                        <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        <option value="dismissed" {{ $report->status === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                                    <textarea name="reason" id="reason" rows="2" 
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Reason for status change..."></textarea>
                                </div>
                                <button type="submit" 
                                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                    Update Status
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Resolution Actions -->
                    @if($report->status !== 'resolved')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Resolve Report</h3>
                            
                            <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="resolution_actions" class="block text-sm font-medium text-gray-700">Actions Taken</label>
                                    <div id="actions-container" class="mt-1 space-y-2">
                                        <input type="text" name="resolution_actions[]" 
                                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Action taken...">
                                    </div>
                                    <button type="button" onclick="addActionField()" 
                                            class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                                        + Add Another Action
                                    </button>
                                </div>
                                <div>
                                    <label for="resolution_notes" class="block text-sm font-medium text-gray-700">Resolution Notes</label>
                                    <textarea name="resolution_notes" id="resolution_notes" rows="3" 
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Detailed resolution notes..."></textarea>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="notify_reporter" value="1" checked 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Notify reporter</span>
                                </div>
                                <button type="submit" 
                                        class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200">
                                    Resolve Report
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- Report Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Report Information</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Report ID:</span>
                                    <span class="font-medium">#{{ $report->id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Created:</span>
                                    <span class="font-medium">{{ $report->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Last Updated:</span>
                                    <span class="font-medium">{{ $report->updated_at->format('M d, Y') }}</span>
                                </div>
                                @if($report->resolved_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Resolved:</span>
                                    <span class="font-medium">{{ $report->resolved_at->format('M d, Y') }}</span>
                                </div>
                                @endif
                                @if($report->resolvedBy)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Resolved By:</span>
                                    <span class="font-medium">{{ $report->resolvedBy->name }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status History -->
                    @if($report->statusHistory && $report->statusHistory->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status History</h3>
                            <div class="space-y-3">
                                @foreach($report->statusHistory as $history)
                                <div class="border-l-2 border-gray-200 pl-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ ucfirst($history->old_status) }} → {{ ucfirst($history->new_status) }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $history->created_at->format('M d, g:i A') }}</span>
                                    </div>
                                    @if($history->reason)
                                    <p class="text-xs text-gray-600 mt-1">{{ $history->reason }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500">By: {{ $history->changedBy->name }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function addActionField() {
            const container = document.getElementById('actions-container');
            const newField = document.createElement('input');
            newField.type = 'text';
            newField.name = 'resolution_actions[]';
            newField.className = 'block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500';
            newField.placeholder = 'Action taken...';
            container.appendChild(newField);
        }
    </script>
</x-app-layout>
