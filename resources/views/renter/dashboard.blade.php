@extends('layouts.app')

@section('title', 'Renter Dashboard')
@section('description', 'Find your perfect home, manage favorites, and track your applications')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Dashboard Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-heading-1 text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
                    <p class="text-body text-gray-600 mt-1">Find your perfect home and manage your applications</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('properties.public.index') }}" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Browse Properties
                    </a>
                    <a href="{{ route('properties.search-map') }}" class="btn btn-outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Map Search
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Favorites -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Favorites</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_favorites'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-info-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Messages</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_messages'] }}</p>
                            @if($stats['unread_messages'] > 0)
                                <p class="text-xs text-warning-600">{{ $stats['unread_messages'] }} unread</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Submitted -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-warning-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Reports</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['reports_submitted'] + $stats['message_reports_submitted'] }}</p>
                            @if($stats['pending_reports'] > 0)
                                <p class="text-xs text-warning-600">{{ $stats['pending_reports'] }} pending</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Score -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Activity</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $activityData['properties_viewed'] + $activityData['messages_sent'] + $activityData['favorites_added'] }}</p>
                            <p class="text-xs text-gray-500">This month</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Favorites & Recommendations -->
            <div class="lg:col-span-2">
                <!-- Favorites Section -->
                <div class="card mb-8">
                    <div class="card-header">
                        <h3 class="text-heading-3">Your Favorites</h3>
                        <a href="{{ route('favorites.index') }}" class="btn btn-ghost btn-sm">View All</a>
                    </div>
                    <div class="card-body">
                        @if($favorites->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($favorites->take(4) as $favorite)
                                    <div class="property-card">
                                        <div class="relative">
                                            @if($favorite->property->images->count() > 0)
                                                <img src="{{ Storage::url($favorite->property->images->first()->path) }}" 
                                                     alt="{{ $favorite->property->title }}"
                                                     class="property-card-image">
                                            @else
                                                <div class="w-full h-32 bg-gray-200 flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="absolute top-2 right-2">
                                                <span class="badge badge-primary">RWF {{ number_format($favorite->property->price) }}</span>
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <h4 class="font-medium text-gray-900 mb-1">{{ $favorite->property->title }}</h4>
                                            <p class="text-sm text-gray-600 mb-2">{{ $favorite->property->location }}</p>
                                            <div class="flex items-center justify-between text-sm text-gray-500">
                                                <span>{{ $favorite->property->bedrooms }} bed</span>
                                                <span>{{ $favorite->property->bathrooms }} bath</span>
                                                @if($favorite->property->area)
                                                    <span>{{ $favorite->property->area }} m²</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                </svg>
                                <p class="text-gray-500 mb-4">No favorites yet</p>
                                <a href="{{ route('properties.public.index') }}" class="btn btn-primary">Browse Properties</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recommended Properties -->
                @if($recommendedProperties->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-heading-3">Recommended for You</h3>
                            <a href="{{ route('properties.public.index') }}" class="btn btn-ghost btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($recommendedProperties->take(4) as $property)
                                    <x-property-card 
                                        :property="$property"
                                        :show-carousel="true"
                                        :enable-favorites="true"
                                        :enable-comparison="true"
                                        :show-actions="true"
                                        layout="grid"
                                        class="w-full"
                                    />
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-heading-3">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-3">
                            <a href="{{ route('properties.public.index') }}" class="btn btn-primary w-full justify-start">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Browse Properties
                            </a>
                            <a href="{{ route('properties.search-map') }}" class="btn btn-outline w-full justify-start">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Map Search
                            </a>
                            <a href="{{ route('messages.index') }}" class="btn btn-outline w-full justify-start">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Messages
                            </a>
                            <a href="{{ route('reports.my-reports') }}" class="btn btn-outline w-full justify-start">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                My Reports
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-heading-3">Recent Activity</h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-4">
                            @if($recentMessages->count() > 0)
                                <div class="space-y-3">
                                    <h4 class="text-sm font-medium text-gray-900">Recent Messages</h4>
                                    @foreach($recentMessages->take(3) as $message)
                                        <div class="flex items-start space-x-2">
                                            <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-medium text-primary-600">
                                                    {{ substr($message->sender->name ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs text-gray-600 truncate">{{ $message->content }}</p>
                                                <p class="text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($recentReports->count() > 0)
                                <div class="space-y-3">
                                    <h4 class="text-sm font-medium text-gray-900">Recent Reports</h4>
                                    @foreach($recentReports->take(3) as $report)
                                        <div class="flex items-start space-x-2">
                                            <div class="flex-shrink-0 w-6 h-6 bg-warning-100 rounded-full flex items-center justify-center">
                                                <svg class="w-3 h-3 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs text-gray-600 truncate">{{ $report->title }}</p>
                                                <p class="text-xs text-gray-400">{{ $report->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($recentMessages->count() == 0 && $recentReports->count() == 0)
                                <div class="text-center py-4">
                                    <p class="text-sm text-gray-500">No recent activity</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
