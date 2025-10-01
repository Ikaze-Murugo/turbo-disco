{{-- Anthropic-Inspired Navigation Header --}}
<nav class="nav" x-data="navigation()" role="navigation" aria-label="Main navigation">
    <!-- Skip Link for Accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 btn btn-primary">
        Skip to main content
    </a>
    
    <!-- Mobile Navigation -->
    <div class="xl:hidden">
        <!-- Mobile Header -->
        <div class="container">
            <div class="flex justify-between items-center" style="min-height: 64px;">
                <!-- Left: Hamburger + Logo -->
                <div class="flex items-center gap-3">
                    <button @click="toggleMobileMenu()" 
                            class="p-2 rounded-md transition-all"
                            style="color: var(--text-secondary); min-height: 44px; min-width: 44px;"
                            aria-label="Toggle mobile menu"
                            :aria-expanded="mobileMenuOpen">
                        <svg class="w-6 h-6 transition-transform duration-200" 
                             :class="{ 'rotate-90': mobileMenuOpen }" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <a href="{{ route('home') }}" 
                       class="text-heading-4"
                       style="color: var(--color-accent);">
                        Murugo
                    </a>
                </div>

                <!-- Right: User Actions -->
                <div class="flex items-center gap-2">
                    @auth
                        <!-- Notifications -->
                        <button @click="toggleNotifications()" 
                                class="p-2 rounded-md transition-all"
                                style="color: var(--text-secondary); min-height: 44px; min-width: 44px;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 19H6a2 2 0 01-2-2V7a2 2 0 012-2h5m5 0v5"></path>
                            </svg>
                        </button>
                        
                        <!-- User Menu -->
                        <button @click="toggleUserMenu()" 
                                class="p-2 rounded-md transition-all"
                                style="color: var(--text-secondary); min-height: 44px; min-width: 44px;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </button>
                    @else
                        <!-- Guest Actions -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="nav-link">Sign in</a>
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get started</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             style="background-color: var(--bg-primary); border-top: 1px solid var(--border-light);">
            <div class="container py-4">
                <!-- Main Navigation Links -->
                <div class="space-y-1 mb-6">
                    <a href="{{ route('properties.index') }}" class="mobile-nav-link">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        <span>Browse Properties</span>
                    </a>
                    
                    @auth
                        @if(auth()->user()->role === 'landlord')
                            <a href="{{ route('landlord.dashboard') }}" class="mobile-nav-link">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span>Dashboard</span>
                            </a>
                        @elseif(auth()->user()->role === 'renter')
                            <a href="{{ route('renter.dashboard') }}" class="mobile-nav-link">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span>My Favorites</span>
                            </a>
                        @endif
                        
                        <a href="{{ route('messages.index') }}" class="mobile-nav-link">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span>Messages</span>
                        </a>
                    @endauth
                </div>
                
                <!-- Search Bar for Mobile -->
                <div style="border-top: 1px solid var(--border-light); padding-top: var(--space-4);">
                    <form action="{{ route('properties.search') }}" method="GET" class="form-group">
                        <div style="position: relative;">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Search properties..." 
                                   class="form-input"
                                   style="padding-left: var(--space-10);">
                            <svg class="w-5 h-5" 
                                 style="position: absolute; left: var(--space-3); top: 50%; transform: translateY(-50%); color: var(--text-tertiary);" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Desktop Navigation -->
    <div class="hidden xl:block">
        <div class="container">
            <div class="flex justify-between items-center" style="min-height: 64px;">
                <!-- Left: Logo + Main Navigation -->
                <div class="flex items-center gap-8">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" 
                       class="text-heading-4"
                       style="color: var(--color-accent);">
                        Murugo
                    </a>

                    <!-- Main Navigation Links -->
                    <div class="flex gap-6">
                        <a href="{{ route('properties.index') }}" class="nav-link">Browse Properties</a>
                        
                        @auth
                            @if(auth()->user()->role === 'landlord')
                                <a href="{{ route('landlord.dashboard') }}" class="nav-link">Dashboard</a>
                                <a href="{{ route('landlord.properties.create') }}" class="nav-link">Add Property</a>
                            @elseif(auth()->user()->role === 'renter')
                                <a href="{{ route('renter.dashboard') }}" class="nav-link">My Favorites</a>
                                <a href="{{ route('renter.saved-searches') }}" class="nav-link">Saved Searches</a>
                            @endif
                            
                            <a href="{{ route('messages.index') }}" class="nav-link">Messages</a>
                        @endauth
                    </div>
                </div>
                
                <!-- Center: Search Bar -->
                <div class="flex-1 max-w-lg mx-8">
                    <form action="{{ route('properties.search') }}" method="GET">
                        <div style="position: relative;">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Search properties..." 
                                   class="form-input w-full"
                                   style="padding-left: var(--space-10);">
                            <svg class="w-5 h-5" 
                                 style="position: absolute; left: var(--space-3); top: 50%; transform: translateY(-50%); color: var(--text-tertiary);" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </form>
                </div>
                
                <!-- Right: User Actions -->
                <div class="flex items-center gap-4">
                    @auth
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="p-2 rounded-md transition-all"
                                    style="color: var(--text-secondary); min-height: 44px; min-width: 44px;">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 19H6a2 2 0 01-2-2V7a2 2 0 012-2h5m5 0v5"></path>
                                </svg>
                            </button>
                            
                            <!-- Notifications Dropdown -->
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-80 card"
                                 style="z-index: 50;">
                                <div class="card-header">
                                    <h3 class="text-body font-medium">Notifications</h3>
                                </div>
                                <div class="card-body">
                                    <p class="text-body-small" style="color: var(--text-secondary);">No new notifications</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center gap-2 p-2 rounded-md transition-all"
                                    style="color: var(--text-secondary);">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                     style="background-color: var(--color-accent); color: white;">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- User Dropdown -->
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-48 card"
                                 style="z-index: 50;">
                                <div class="card-body">
                                    <div class="space-y-1">
                                        <a href="{{ route('profile.edit') }}" class="block nav-link">Profile</a>
                                        <a href="{{ route('settings') }}" class="block nav-link">Settings</a>
                                        <hr style="border-color: var(--border-light);">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left nav-link">Sign out</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Guest Actions -->
                        <div class="flex items-center gap-4">
                            <a href="{{ route('login') }}" class="nav-link">Sign in</a>
                            <a href="{{ route('register') }}" class="btn btn-primary">Get started</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
function navigation() {
    return {
        mobileMenuOpen: false,
        
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        }
    }
}
</script>
