@extends('layouts.app')

@section('title', 'Messages')
@section('description', 'Manage your conversations with landlords and renters')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Messages Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-heading-1 text-gray-900">Messages</h1>
                    <p class="text-body text-gray-600 mt-1">Communicate with landlords and renters</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('properties.public.index') }}" class="btn btn-outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Browse Properties
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($latestMessages->isEmpty())
            <!-- Empty State -->
            <div class="card">
                <div class="card-body text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-heading-3 text-gray-900 mb-2">No conversations yet</h3>
                    <p class="text-body text-gray-600 mb-6">Start a conversation by messaging a landlord about a property you're interested in.</p>
                    <a href="{{ route('properties.public.index') }}" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Browse Properties
                    </a>
                </div>
            </div>
        @else
            <!-- Conversations List -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Conversations Sidebar -->
                <div class="lg:col-span-1">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-heading-3">Conversations</h3>
                            <span class="badge badge-primary">{{ $latestMessages->count() }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="space-y-1">
                                @foreach($latestMessages as $message)
                                    @php
                                        $otherUser = $message->sender_id === auth()->id() ? $message->recipient : $message->sender;
                                        $hasUnread = \App\Models\Message::where('conversation_id', $message->conversation_id)
                                                                       ->where('recipient_id', auth()->id())
                                                                       ->where('is_read', false)
                                                                       ->exists();
                                    @endphp
                                    <a href="{{ route('messages.show', $message) }}" 
                                       class="block p-4 hover:bg-gray-50 transition-colors {{ $hasUnread ? 'bg-blue-50 border-l-4 border-primary-500' : '' }}">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-medium text-primary-600">
                                                        {{ substr($otherUser->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $otherUser->name }}
                                                    </p>
                                                    @if($hasUnread)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                                            New
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-gray-500 mb-1">
                                                    {{ $message->property->title }}
                                                </p>
                                                <p class="text-sm text-gray-600 truncate">
                                                    {{ Str::limit($message->body, 50) }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $message->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conversation Preview -->
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-body text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Select a conversation</h3>
                            <p class="text-gray-500">Choose a conversation from the sidebar to view messages</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection