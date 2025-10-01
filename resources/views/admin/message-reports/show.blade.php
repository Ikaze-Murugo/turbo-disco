<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Message Report #{{ $messageReport->id }} - {{ $messageReport->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Submitted by {{ $messageReport->sender->name }} on {{ $messageReport->created_at->format('M d, Y \a\t g:i A') }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.message-reports.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    Back to Message Reports
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
                                <h3 class="text-lg font-medium text-gray-900">Message Report Details</h3>
                                <div class="flex space-x-2">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        @if($messageReport->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($messageReport->status === 'investigating') bg-blue-100 text-blue-800
                                        @elseif($messageReport->status === 'resolved') bg-green-100 text-green-800
                                        @elseif($messageReport->status === 'dismissed') bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($messageReport->status) }}
                                    </span>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        @if($messageReport->priority === 'urgent') bg-red-100 text-red-800
                                        @elseif($messageReport->priority === 'high') bg-orange-100 text-orange-800
                                        @elseif($messageReport->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @elseif($messageReport->priority === 'low') bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($messageReport->priority) }} Priority
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Category</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $messageReport->category }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $messageReport->description }}</p>
                                </div>

                                @if($messageReport->evidence_urls && count(json_decode($messageReport->evidence_urls, true)) > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Evidence URLs</label>
                                    <div class="mt-1 space-y-2">
                                        @foreach(json_decode($messageReport->evidence_urls, true) as $url)
                                            <a href="{{ $url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm break-all">
                                                {{ $url }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Reported Message -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Reported Message</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600">
                                            {{ substr($messageReport->recipient->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $messageReport->recipient->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $messageReport->message->created_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $messageReport->message_content }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Comments</h3>
                            
                            <!-- Add Comment Form -->
                            <form method="POST" action="{{ route('admin.message-reports.comment', $messageReport) }}" class="mb-6">
                                @csrf
                                <div class="mb-4">
                                    <textarea name="comment" rows="3" 
                                             class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                             placeholder="Add a comment..." required>{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex justify-end">
                                    <x-primary-button type="submit">
                                        Add Comment
                                    </x-primary-button>
                                </div>
                            </form>

                            <!-- Comments List -->
                            <div class="space-y-4">
                                @forelse($messageReport->comments as $comment)
                                    <div class="border-l-4 border-blue-200 pl-4 py-2">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $comment->created_at->format('M d, Y \a\t g:i A') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $comment->comment }}</p>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">No comments yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                            
                            <!-- Status Update Form -->
                            <form method="POST" action="{{ route('admin.message-reports.status', $messageReport) }}" class="mb-4">
                                @csrf
                                @method('PATCH')
                                <div class="mb-3">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="pending" {{ $messageReport->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="investigating" {{ $messageReport->status === 'investigating' ? 'selected' : '' }}>Investigating</option>
                                        <option value="resolved" {{ $messageReport->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        <option value="dismissed" {{ $messageReport->status === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                                    <select name="priority" id="priority" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="low" {{ $messageReport->priority === 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ $messageReport->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ $messageReport->priority === 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ $messageReport->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="resolution_notes" class="block text-sm font-medium text-gray-700">Resolution Notes</label>
                                    <textarea name="resolution_notes" id="resolution_notes" rows="3" 
                                             class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                             placeholder="Add resolution notes...">{{ old('resolution_notes', $messageReport->resolution_notes) }}</textarea>
                                </div>
                                <x-primary-button type="submit" class="w-full">
                                    Update Status
                                </x-primary-button>
                            </form>
                        </div>
                    </div>

                    <!-- Report Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Report Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Report ID</label>
                                    <p class="text-sm text-gray-900">#{{ $messageReport->id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Message ID</label>
                                    <p class="text-sm text-gray-900">#{{ $messageReport->message_id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Reporter</label>
                                    <p class="text-sm text-gray-900">{{ $messageReport->sender->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $messageReport->sender->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Reported User</label>
                                    <p class="text-sm text-gray-900">{{ $messageReport->recipient->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $messageReport->recipient->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Submitted</label>
                                    <p class="text-sm text-gray-900">{{ $messageReport->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                                @if($messageReport->resolved_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Resolved</label>
                                    <p class="text-sm text-gray-900">{{ $messageReport->resolved_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Related Reports -->
                    @if($relatedReports && $relatedReports->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Related Reports</h3>
                            <div class="space-y-3">
                                @foreach($relatedReports as $relatedReport)
                                    <div class="border rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <a href="{{ route('admin.message-reports.show', $relatedReport) }}" 
                                               class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                                Report #{{ $relatedReport->id }}
                                            </a>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($relatedReport->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($relatedReport->status === 'investigating') bg-blue-100 text-blue-800
                                                @elseif($relatedReport->status === 'resolved') bg-green-100 text-green-800
                                                @elseif($relatedReport->status === 'dismissed') bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($relatedReport->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600">{{ $relatedReport->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $relatedReport->created_at->format('M d, Y') }}</p>
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
</x-app-layout>
