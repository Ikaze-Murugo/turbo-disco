{{-- Modern Navigation Header Component - Mobile Optimized --}}
<nav x-data="navigation()" class="bg-white/95 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-50 shadow-sm" role="navigation" aria-label="Main navigation">
    <!-- Skip Link for Accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Mobile Navigation -->
    <div class="xl:hidden">
        <!-- Mobile Header -->
        <div class="flex justify-between items-center px-3 py-2">
            <!-- Left: Hamburger + Logo -->
            <div class="flex items-center space-x-3">
                <button @click="toggleMobileMenu()" 
                        class="touch-target p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500"
                        aria-label="Toggle mobile menu"
                        aria-expanded="false"
                        :aria-expanded="mobileMenuOpen">
                    <svg class="h-6 w-6 transition-transform duration-200" 
                         :class="{ 'rotate-90': mobileMenuOpen }" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <a href="{{ route('home') }}" 
                   class="flex items-center"
                   aria-label="Murugo - Go to homepage">
                    <img src="{{ asset('images/murugo-logo.png') }}" 
                         alt="Murugo - Find Your Perfect Home" 
                         class="h-10 w-auto object-contain">
                </a>
            </div>

            <!-- Right: Comparison + Notifications + User Actions -->
            <div class="flex items-center justify-center space-x-1">
                <!-- Comparison Counter -->
                <a href="{{ route('compare.index') }}" 
                   class="relative touch-target p-1.5 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 flex items-center justify-center"
                   aria-label="Property comparison">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span id="compare-count-mobile" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center hidden text-[10px]">0</span>
                </a>
                
                @auth
                    <!-- Notifications -->
                    <x-navigation.notifications />
                    
                    <!-- User Menu -->
                    <x-navigation.user-dropdown />
                @else
                    <!-- Guest Actions -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}" 
                           class="touch-target text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                           aria-label="Login to your account">
                            Login
                        </a>
                        <a href="{{ route('register') }}" 
                           class="touch-target bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 text-sm font-medium transition-colors shadow-sm"
                           aria-label="Create a new account">
                            Register
                        </a>
                    </div>
                @endauth
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
             class="bg-white border-t border-gray-200"
             role="menu"
             aria-label="Mobile navigation menu">
            <div class="px-4 py-2 space-y-1">
                <!-- Main Navigation Links -->
                <x-navigation.mobile-links />
                
                <!-- Search Bar for Mobile -->
                <div class="pt-2 border-t border-gray-200">
                    <x-navigation.search-bar />
                </div>
            </div>
        </div>
    </div>
    
    <!-- Desktop Navigation -->
    <div class="hidden xl:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Left: Logo + Main Navigation -->
                <div class="flex items-center space-x-8">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <img src="{{ asset('images/murugo-logo.png') }}" 
                                 alt="Murugo - Find Your Perfect Home" 
                                 class="h-24 w-auto object-contain">
                        </a>
                    </div>

                    <!-- Main Navigation Links -->
                    <div class="hidden space-x-6 lg:flex">
                        <x-navigation.main-links />
                    </div>
                </div>
                
                <!-- Center: Search Bar (Expandable on large desktop) -->
                <div class="flex-1 xl:flex-initial mx-4">
                    <x-navigation.search-bar />
                </div>
                
                <!-- Right: Comparison + User Actions -->
                <div class="flex items-center space-x-2">
                    <!-- Comparison Counter -->
                    <a href="{{ route('compare.index') }}" 
                       class="relative p-1.5 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500"
                       aria-label="Property comparison">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span id="compare-count-desktop" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center hidden text-[10px]">0</span>
                    </a>
                    
                    @auth
                        <!-- Notifications -->
                        <x-navigation.notifications />
                        
                        <!-- User Menu -->
                        <x-navigation.user-dropdown />
                    @else
                        <!-- Guest Actions -->
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" 
                               class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Login
                            </a>
                            <a href="{{ route('register') }}" 
                               class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 text-sm font-medium transition-colors shadow-sm">
                                Register
                            </a>
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
        userMenuOpen: false,
        notificationsOpen: false,
        
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        },
        
        toggleUserMenu() {
            this.userMenuOpen = !this.userMenuOpen;
        },
        
        toggleNotifications() {
            this.notificationsOpen = !this.notificationsOpen;
        },
        
        closeAllMenus() {
            this.mobileMenuOpen = false;
            this.userMenuOpen = false;
            this.notificationsOpen = false;
        }
    }
}
</script>
