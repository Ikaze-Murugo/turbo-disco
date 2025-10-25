@extends('layouts.app')

@section('title', 'Landlord: ' . $user->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero / Header -->
    <div class="bg-white border-b">
        <div class="container py-8">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="flex items-center gap-4">
                    <img src="{{ $user->getProfilePictureUrl() }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-full object-cover">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->business_name ?: $user->name }}</h1>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mt-1">
                            @if($stats['average_rating'])
                                <span>⭐ {{ number_format($stats['average_rating'], 1) }} ({{ $stats['review_count'] }} reviews)</span>
                            @endif
                            <span>{{ $stats['properties_listed'] }} properties</span>
                            <span>Joined {{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="md:ml-auto flex items-center gap-3">
                    @auth
                        @if(auth()->user()->isRenter())
                            <a href="{{ route('messages.index') }}" class="btn btn-primary">Message Landlord</a>
                        @endif
                        @if($isAdmin)
                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Admin view: includes pending</span>
                        @endif
                    @endauth
                </div>
            </div>

            @if(auth()->check())
                @if(!$isAdmin)
                    @if($user->bio)
                        <p class="mt-4 text-gray-700 max-w-3xl">{{ $user->bio }}</p>
                    @endif
                    <div class="mt-2 text-sm text-gray-600 flex flex-col sm:flex-row sm:items-center gap-2">
                        @if($user->location)
                            <span><strong>Location:</strong> {{ $user->location }}</span>
                        @endif
                        @if($user->website)
                            <span><strong>Website:</strong> <a href="{{ $user->website }}" class="text-primary-600 hover:text-primary-700" target="_blank" rel="noopener">Visit</a></span>
                        @endif
                    </div>
                @else
                    <!-- Admin sees full contact info -->
                    <div class="mt-4 text-sm text-gray-700 flex flex-col gap-1">
                        @if($user->bio)
                            <p>{{ $user->bio }}</p>
                        @endif
                        <span><strong>Email:</strong> {{ $user->email }}</span>
                        @if($user->phone_number)
                            <span><strong>Phone:</strong> {{ $user->phone_number }}</span>
                        @endif
                        @if($user->location)
                            <span><strong>Location:</strong> {{ $user->location }}</span>
                        @endif
                        @if($user->website)
                            <span><strong>Website:</strong> <a href="{{ $user->website }}" class="text-primary-600 hover:text-primary-700" target="_blank" rel="noopener">{{ $user->website }}</a></span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Properties -->
    <div class="container py-10">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Properties by {{ $user->name }}</h2>
        @if($properties->count() === 0)
            <div class="bg-white rounded-lg border p-6 text-gray-600">No properties listed yet.</div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($properties as $property)
                    <x-property-card :property="$property" :showCarousel="false" :enableFavorites="true" :enable-comparison="true" />
                @endforeach
            </div>
            <div class="mt-6">{{ $properties->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection


