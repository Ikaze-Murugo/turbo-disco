{{-- Main Navigation Links Component --}}
@php
    $currentRoute = function($route) { return request()->routeIs($route); };
@endphp

<!-- Home -->
<a href="{{ route('home') }}" 
   class="nav-link {{ $currentRoute('home') ? 'nav-link-active' : 'nav-link-inactive' }}">
    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    Home
</a>

<!-- Browse Properties (Available to all users) -->
<a href="{{ route('properties.public.index') }}" 
   class="nav-link {{ $currentRoute('properties.public.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
    </svg>
    Browse Properties
</a>

<!-- Blog -->
<a href="{{ route('blog.index') }}" 
   class="nav-link {{ $currentRoute('blog.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
    </svg>
    Blog
</a>

<!-- Team -->
<a href="{{ route('team.index') }}" 
   class="nav-link {{ $currentRoute('team.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
    </svg>
    Team
</a>

@auth
    <!-- Role-specific navigation items -->
    @if(auth()->user()->isRenter())
        <!-- Renter-specific links -->
        <a href="{{ route('favorites.index') }}" 
           class="nav-link {{ $currentRoute('favorites.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            My Favorites
        </a>
    @endif

    @if(auth()->user()->isLandlord())
        <!-- Landlord-specific links -->
        <a href="{{ route('properties.index') }}" 
           class="nav-link {{ $currentRoute('properties.index') || $currentRoute('properties.create') || $currentRoute('properties.edit') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            My Properties
        </a>
    @endif

    @if(auth()->user()->isAdmin())
        <!-- Admin-specific links -->
        <a href="{{ route('admin.dashboard') }}" 
           class="nav-link {{ $currentRoute('admin.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            Admin Panel
        </a>
    @endif

    <!-- Messages (Available to all authenticated users) -->
    <a href="{{ route('messages.index') }}" 
       class="nav-link {{ $currentRoute('messages.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        Messages
    </a>
@endauth

