@extends('layouts.app')

@section('title', 'Landlord: ' . $user->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero / Header -->
    <div class="bg-white border-b">
        <div class="container py-8">
            <div class="flex flex-col md:flex-row md:items-start gap-6">
                <div class="flex items-center gap-4">
                    <img src="{{ $user->getProfilePictureUrl() }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-full object-cover border-2 border-gray-200">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->business_name ?: $user->name }}</h1>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mt-1">
                            @if($stats['average_rating'])
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    {{ number_format($stats['average_rating'], 1) }} ({{ $stats['review_count'] }} reviews)
                                </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ $stats['properties_listed'] }} properties
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Joined {{ $user->created_at->format('M Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="md:ml-auto flex items-center gap-3">
                    @auth
                        @if(auth()->user()->isRenter())
                            <a href="{{ route('messages.index') }}" class="btn btn-primary">
                                <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Message Landlord
                            </a>
                        @endif
                        @if($isAdmin)
                            <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-medium">Admin view: includes pending</span>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Enhanced Profile Information Section -->
            <div class="mt-6 bg-gradient-to-br from-gray-50 to-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Contact Information -->
                    @if($user->phone_number || $user->email)
                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Contact Information
                            </h3>
                            @if($user->phone_number)
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Phone</p>
                                        <a href="tel:{{ $user->phone_number }}" class="text-base font-medium text-gray-900 hover:text-primary-600 transition-colors">
                                            {{ $user->phone_number }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                            @if($isAdmin)
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Email</p>
                                        <a href="mailto:{{ $user->email }}" class="text-base font-medium text-gray-900 hover:text-primary-600 transition-colors">
                                            {{ $user->email }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Location Information -->
                    @if($user->location)
                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Location
                            </h3>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Based in</p>
                                    <p class="text-base font-medium text-gray-900">{{ $user->location }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Website -->
                    @if($user->website)
                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                Website
                            </h3>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Visit Website</p>
                                    <a href="{{ $user->website }}" target="_blank" rel="noopener noreferrer" class="text-base font-medium text-primary-600 hover:text-primary-700 transition-colors inline-flex items-center gap-1">
                                        {{ parse_url($user->website, PHP_URL_HOST) ?: $user->website }}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Bio Section - Full Width -->
                @if($user->bio)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            About
                        </h3>
                        <p class="text-gray-700 leading-relaxed max-w-4xl">{{ $user->bio }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Properties Section -->
    <div class="container py-10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Properties by {{ $user->business_name ?: $user->name }}</h2>
            <span class="text-sm text-gray-600 bg-white px-4 py-2 rounded-full border border-gray-200">
                {{ $stats['properties_listed'] }} {{ Str::plural('property', $stats['properties_listed']) }}
            </span>
        </div>
        
        @if($properties->count() === 0)
            <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="text-gray-600 text-lg">No properties listed yet.</p>
                <p class="text-gray-500 text-sm mt-2">Check back soon for new listings!</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($properties as $property)
                    <x-property-card :property="$property" :showCarousel="false" :enableFavorites="true" :enable-comparison="true" />
                @endforeach
            </div>
            <div class="mt-8">{{ $properties->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
