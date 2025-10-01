<nav x-data="{ open: false, mobileMenuOpen: false }" class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <!-- Mobile Navigation -->
    <div class="lg:hidden">
        <div class="flex justify-between items-center px-4 py-3">
            <div class="flex items-center space-x-3">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600">
                    Murugo
                </a>
            </div>
            <div class="flex items-center space-x-2">
                <!-- Notification Bell -->
                @auth
                    @php
                        try {
                            $unreadCount = auth()->user()->unreadReportNotifications()->count() + 
                                          auth()->user()->unreadMessageReportNotifications()->count();
                        } catch (Exception $e) {
                            $unreadCount = 0;
                        }
                    @endphp
                    <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 12l2 2 4-4"></path>
                        </svg>
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">{{ $unreadCount }}</span>
                        @endif
                    </button>
                @endauth
                
                <!-- User Menu -->
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            </button>
                        </x-slot>
                        
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.index')">
                                {{ __('My Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Edit Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.settings')">
                                {{ __('Settings') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.statistics')">
                                {{ __('Statistics') }}
                            </x-dropdown-link>
                            
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                    </div>
                @endauth
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition class="bg-white border-t border-gray-200">
            <div class="px-4 py-2 space-y-1">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : 'nav-link-inactive' }} block">
                    Home
                </a>
                <a href="{{ route('properties.index') }}" class="nav-link {{ request()->routeIs('properties.index') ? 'nav-link-active' : 'nav-link-inactive' }} block">
                    Properties
                </a>
                <a href="{{ route('blog.index') }}" class="nav-link {{ request()->routeIs('blog.*') ? 'nav-link-active' : 'nav-link-inactive' }} block">
                    Blog
                </a>
                <a href="{{ route('team.index') }}" class="nav-link {{ request()->routeIs('team.*') ? 'nav-link-active' : 'nav-link-inactive' }} block">
                    Team
                </a>
                @auth
                    @if(auth()->user()->isRenter())
                        <a href="{{ route('properties.search') }}" class="nav-link {{ request()->routeIs('properties.search') ? 'nav-link-active' : 'nav-link-inactive' }} block">
                            Search
                        </a>
                        <a href="{{ route('favorites.index') }}" class="nav-link {{ request()->routeIs('favorites.*') ? 'nav-link-active' : 'nav-link-inactive' }} block">
                            Favorites
                        </a>
                    @endif
                    @if(auth()->user()->isLandlord())
                        <a href="{{ route('properties.create') }}" class="nav-link {{ request()->routeIs('properties.create') ? 'nav-link-active' : 'nav-link-inactive' }} block">
                            Add Property
                        </a>
                    @endif
                    <a href="{{ route('messages.index') }}" class="nav-link {{ request()->routeIs('messages.*') ? 'nav-link-active' : 'nav-link-inactive' }} block">
                        Messages
                    </a>
                    <a href="{{ route('reports.my-reports') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'nav-link-active' : 'nav-link-inactive' }} block">
                        My Reports
                        @php
                            try {
                                $unreadCount = auth()->user()->unreadReportNotifications()->count() + 
                                              auth()->user()->unreadMessageReportNotifications()->count();
                            } catch (Exception $e) {
                                $unreadCount = 0;
                            }
                        @endphp
                        @if($unreadCount > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $unreadCount }}</span>
                        @endif
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-link nav-link-inactive block">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="nav-link nav-link-inactive block">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </div>
    
    <!-- Desktop Navigation -->
    <div class="hidden lg:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600">
                            Murugo
                        </a>
                    </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        Home
                    </a>
                    <a href="{{ route('properties.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        Properties
                    </a>
                    <a href="{{ route('blog.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        Blog
                    </a>
                    <a href="{{ route('team.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        Team
                    </a>
                    
                    @auth
                        @if(auth()->user()->isRenter())
                            <x-nav-link :href="route('properties.search')" :active="request()->routeIs('properties.search')">
                                {{ __('Search') }}
                            </x-nav-link>
                            <x-nav-link :href="route('favorites.index')" :active="request()->routeIs('favorites.*')">
                                {{ __('Favorites') }}
                            </x-nav-link>
                        @endif
                        
                        @if(auth()->user()->isLandlord())
                            <x-nav-link :href="route('properties.create')" :active="request()->routeIs('properties.create')">
                                {{ __('Add Property') }}
                            </x-nav-link>
                        @endif
                        
                        <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                            {{ __('Messages') }}
                        </x-nav-link>
                        
                <x-nav-link :href="route('reports.my-reports')" :active="request()->routeIs('reports.*')">
                    <div class="flex items-center space-x-2">
                        <span>{{ __('My Reports') }}</span>
                        @php
                            try {
                                $unreadCount = auth()->user()->unreadReportNotifications()->count() + 
                                              auth()->user()->unreadMessageReportNotifications()->count();
                            } catch (Exception $e) {
                                $unreadCount = 0;
                            }
                        @endphp
                        @if($unreadCount > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $unreadCount }}
                        </span>
                        @endif
                    </div>
                </x-nav-link>
                
                <x-nav-link :href="route('message-reports.my-reports')" :active="request()->routeIs('message-reports.*')">
                    <div class="flex items-center space-x-2">
                        <span>{{ __('Message Reports') }}</span>
                        @php
                            $unreadMessageCount = auth()->user()->unreadMessageReportNotifications()->count();
                        @endphp
                        @if($unreadMessageCount > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $unreadMessageCount }}
                        </span>
                        @endif
                    </div>
                </x-nav-link>
                        
                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                                {{ __('Admin Panel') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

                @auth
                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <!-- User Role Badge -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-4
                            @if(auth()->user()->isAdmin()) bg-red-100 text-red-800
                            @elseif(auth()->user()->isLandlord()) bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.index')">
                                    {{ __('My Profile') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Edit Profile') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('profile.settings')">
                                    {{ __('Settings') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('profile.statistics')">
                                    {{ __('Statistics') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <!-- Guest Navigation -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                        <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700">Log in</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>