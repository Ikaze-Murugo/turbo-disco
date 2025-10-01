@extends('layouts.app')

@section('title', 'Conversation with ' . $otherParticipant->name)
@section('description', 'Chat with ' . $otherParticipant->name . ' about property inquiries')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Chat Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('messages.index') }}" 
                       class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-primary-600">
                                {{ substr($otherParticipant->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h1 class="text-heading-2 text-gray-900">{{ $otherParticipant->name }}</h1>
                            <p class="text-sm text-gray-500">
                                {{ $conversationMessages->count() }} message{{ $conversationMessages->count() !== 1 ? 's' : '' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button class="btn btn-ghost btn-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 12l2 2 4-4"></path>
                        </svg>
                        More
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="card">
            <!-- Chat Messages -->
            <div class="card-body p-0">
                <div class="h-96 overflow-y-auto p-6 space-y-4" id="chat-messages">
                    @foreach($conversationMessages as $msg)
                        <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs lg:max-w-md">
                                <!-- Message Header -->
                                <div class="flex items-center space-x-2 mb-2 {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="w-8 h-8 {{ $msg->sender_id === auth()->id() ? 'bg-primary-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium {{ $msg->sender_id === auth()->id() ? 'text-primary-600' : 'text-gray-600' }}">
                                            {{ substr($msg->sender->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-900">{{ $msg->sender->name }}</span>
                                        <span class="text-gray-500 ml-2">{{ $msg->created_at->format('M j, g:i A') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Message Bubble -->
                                <div class="relative">
                                    <div class="px-4 py-3 rounded-2xl {{ $msg->sender_id === auth()->id() 
                                        ? 'bg-primary-600 text-white rounded-br-md' 
                                        : 'bg-gray-100 text-gray-900 rounded-bl-md' }}">
                                        <p class="whitespace-pre-wrap">{{ $msg->body }}</p>
                                        
                                        <!-- Property Reference -->
                                        @if($msg->property)
                                            <div class="mt-3 pt-3 border-t {{ $msg->sender_id === auth()->id() ? 'border-primary-400' : 'border-gray-200' }}">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4 {{ $msg->sender_id === auth()->id() ? 'text-primary-200' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                    <span class="text-xs {{ $msg->sender_id === auth()->id() ? 'text-primary-200' : 'text-gray-500' }}">
                                                        About: 
                                                        <a href="{{ route('properties.show', $msg->property) }}" 
                                                           class="underline hover:no-underline">
                                                            {{ $msg->property->title }}
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Report Button -->
                                    @if($msg->sender_id !== auth()->id())
                                        <div class="mt-2 flex justify-start">
                                            <a href="{{ route('message-reports.create', $msg) }}" 
                                               class="inline-flex items-center px-2 py-1 text-xs text-gray-500 hover:text-red-600 transition-colors"
                                               title="Report this message">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                Report
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Reply Form -->
                <div class="border-t border-gray-200 p-6">
                    <form method="POST" action="{{ route('messages.reply', $conversationMessages->first()) }}" 
                          class="flex space-x-4" id="message-form">
                        @csrf
                        <div class="flex-1">
                            <textarea name="body" 
                                      rows="3" 
                                      class="form-input resize-none"
                                      placeholder="Type your message here..."
                                      required
                                      id="message-input">{{ old('body') }}</textarea>
                            @error('body')
                                <p class="form-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex-shrink-0">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to bottom of chat
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // Auto-resize textarea
    const messageInput = document.getElementById('message-input');
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    
    // Focus on message input
    messageInput.focus();
    
    // Auto-scroll on new messages
    const messageForm = document.getElementById('message-form');
    messageForm.addEventListener('submit', function() {
        setTimeout(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 100);
    });
});
</script>
@endpush
@endsection