<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Murugo Property Platform - Find Your Perfect Home in Rwanda')</title>
    <meta name="description" content="@yield('description', 'Discover the best properties for rent and sale in Rwanda. Browse apartments, houses, and commercial spaces with Murugo Property Platform.')">
    <meta name="keywords" content="properties, rent, sale, Rwanda, Kigali, apartments, houses, real estate">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional CSS -->
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .property-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .search-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('public.home') }}" class="flex items-center">
                            <div class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">M</span>
                            </div>
                            <span class="ml-2 text-xl font-bold text-gray-900">Murugo</span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="{{ route('public.home') }}" class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                            Home
                        </a>
                        <a href="{{ route('public.properties') }}" class="text-gray-500 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                            Properties
                        </a>
                        <a href="{{ route('public.search') }}" class="text-gray-500 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                            Search
                        </a>
                    </div>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Authenticated user menu -->
                        <div class="relative">
                            <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                Dashboard
                            </a>
                        </div>
                    @else
                        <!-- Guest user menu -->
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('login') }}" class="text-gray-500 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                Register
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600" onclick="toggleMobileMenu()">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('public.home') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-blue-600">Home</a>
                <a href="{{ route('public.properties') }}" class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-blue-600">Properties</a>
                <a href="{{ route('public.search') }}" class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-blue-600">Search</a>
                @guest
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-blue-600">Login</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-base font-medium text-blue-600">Register</a>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-lg">M</span>
                        </div>
                        <span class="ml-2 text-xl font-bold">Murugo Property Platform</span>
                    </div>
                    <p class="text-gray-300 mb-4">
                        Your trusted partner in finding the perfect property in Rwanda. 
                        Whether you're looking to rent or buy, we connect you with the best properties across the country.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('public.properties') }}" class="text-gray-300 hover:text-white transition-colors">Browse Properties</a></li>
                        <li><a href="{{ route('public.search') }}" class="text-gray-300 hover:text-white transition-colors">Search</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li>Kigali, Rwanda</li>
                        <li>+250 788 123 456</li>
                        <li>info@murugo.rw</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Murugo Property Platform. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobile-menu');
            const button = event.target.closest('button');
            
            if (!menu.contains(event.target) && !button) {
                menu.classList.add('hidden');
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
