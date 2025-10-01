<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Message Report Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Report Header -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $messageReport->title }}</h1>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($messageReport->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($messageReport->status === 'investigating') bg-blue-100 text-blue-800
                                    @elseif($messageReport->status === 'resolved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($messageReport->status) }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($messageReport->priority === 'urgent') bg-red-100 text-red-800
                                    @elseif($messageReport->priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($messageReport->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($messageReport->priority) }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            <span>Category: {{ $messageReport->category }}</span>
                            <span class="mx-2">•</span>
                            <span>Created: {{ $messageReport->created_at->format('M j, Y g:i A') }}</span>
                            @if($messageReport->assignedTo)
                                <span class="mx-2">•</span>
                                <span>Assigned to: {{ $messageReport->assignedTo->name }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Reported Message -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Reported Message</h3>
                        <div class="text-sm text-gray-600 mb-2">
                            <strong>From:</strong> {{ $messageReport->sender->name }}<br>
                            <strong>To:</strong> {{ $messageReport->recipient->name }}<br>
                            <strong>Date:</strong> {{ $messageReport->message->created_at->format('M j, Y g:i A') }}
                        </div>
                        <div class="bg-white p-3 rounded border">
                            <p class="text-gray-800">{{ $messageReport->message->body }}</p>
                        </div>
                    </div>

                    <!-- Report Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Report Description</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-800 whitespace-pre-wrap">{{ $messageReport->description }}</p>
                        </div>
                    </div>

                    <!-- Evidence -->
                    @if($messageReport->evidence_urls && count($messageReport->evidence_urls) > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">Evidence</h3>
                            <div class="space-y-2">
                                @foreach($messageReport->evidence_urls as $url)
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ $url }}" target="_blank" class="text-indigo-600 hover:text-indigo-500 text-sm">
                                            {{ $url }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Resolution Details -->
                    @if($messageReport->isResolved())
                        <div class="mb-6 p-4 bg-green-50 rounded-lg">
                            <h3 class="text-lg font-semibold mb-2 text-green-800">Resolution Details</h3>
                            <div class="text-sm text-green-700">
                                <p><strong>Resolved by:</strong> {{ $messageReport->resolvedBy->name }}</p>
                                <p><strong>Resolved on:</strong> {{ $messageReport->resolved_at->format('M j, Y g:i A') }}</p>
                                @if($messageReport->resolution_notes)
                                    <p class="mt-2"><strong>Notes:</strong></p>
                                    <p class="whitespace-pre-wrap">{{ $messageReport->resolution_notes }}</p>
                                @endif
                                @if($messageReport->resolution_actions && count($messageReport->resolution_actions) > 0)
                                    <p class="mt-2"><strong>Actions taken:</strong></p>
                                    <ul class="list-disc list-inside">
                                        @foreach($messageReport->resolution_actions as $action)
                                            <li>{{ $action }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Comments Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Comments</h3>
                        
                        <!-- Add Comment Form -->
                        @if($messageReport->isActive())
                            <form method="POST" action="{{ route('message-reports.comment', $messageReport) }}" class="mb-6">
                                @csrf
                                <div>
                                    <label for="comment" class="block text-sm font-medium text-gray-700">Add a comment</label>
                                    <textarea id="comment" name="comment" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                        Add Comment
                                    </button>
                                </div>
                            </form>
                        @endif

                        <!-- Comments List -->
                        <div class="space-y-4">
                            @forelse($messageReport->getPublicComments() as $comment)
                                <div class="border-l-4 border-indigo-200 pl-4">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="font-medium text-gray-900">{{ $comment->user->name }}</span>
                                        <span class="text-sm text-gray-500">{{ $comment->created_at->format('M j, Y g:i A') }}</span>
                                        @if($comment->is_admin_comment)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Admin
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-800 whitespace-pre-wrap">{{ $comment->comment }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500 italic">No comments yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Follow-up Request -->
                    @if($messageReport->isResolved() || $messageReport->isDismissed())
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Request Follow-up</h3>
                            <form method="POST" action="{{ route('message-reports.follow-up', $messageReport) }}">
                                @csrf
                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700">Additional information or concerns</label>
                                    <textarea id="message" name="message" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                        Request Follow-up
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Status History -->
                    @if($messageReport->statusHistory->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Status History</h3>
                            <div class="space-y-3">
                                @foreach($messageReport->statusHistory as $history)
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-indigo-500 rounded-full mt-2"></div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium text-gray-900">{{ ucfirst($history->new_status) }}</span>
                                                <span class="text-sm text-gray-500">{{ $history->created_at->format('M j, Y g:i A') }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600">Changed by {{ $history->changedBy->name }}</p>
                                            @if($history->reason)
                                                <p class="text-sm text-gray-500 mt-1">{{ $history->reason }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6 border-t">
                        <a href="{{ route('message-reports.my-reports') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Back to My Reports
                        </a>
                        @if($messageReport->hasUnreadNotifications())
                            <form method="POST" action="{{ route('message-reports.mark-read', $messageReport) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Mark Notifications as Read
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
