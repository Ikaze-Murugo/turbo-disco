<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Murugo - Find Your Perfect Home in Rwanda</title>
    <meta name="description" content="Discover exceptional rental properties across Rwanda. Connect with trusted landlords and find your ideal home with Murugo's modern real estate platform.">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Navigation -->
    <nav class="nav">
        <div class="container">
            <div class="flex items-center justify-between" style="min-height: 64px;">
                <div class="flex items-center">
                    <h1 class="text-heading-3" style="color: var(--color-accent);">Murugo</h1>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="nav-link">Sign in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Get started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="section-lg">
        <div class="container text-center">
            <h1 class="text-heading-1 mb-6">
                Find your perfect home<br>
                in Rwanda
            </h1>
            <p class="text-body-large mb-8" style="max-width: 600px; margin: 0 auto;">
                Discover exceptional rental properties across Rwanda. Connect with trusted landlords 
                and find your ideal home with our modern, user-friendly platform.
            </p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Start searching</a>
                <a href="{{ route('properties.index') }}" class="btn btn-secondary btn-lg">Browse properties</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section" style="background-color: var(--bg-secondary);">
        <div class="container">
            <div class="text-center mb-16">
                <h2 class="text-heading-2 mb-4">Why choose Murugo?</h2>
                <p class="text-body-large" style="color: var(--text-secondary);">
                    Your trusted partner in finding the perfect rental property
                </p>
            </div>
            
            <div class="grid grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-6 flex items-center justify-center" 
                         style="background-color: rgba(79, 70, 229, 0.1); border-radius: var(--radius-lg);">
                        <svg class="w-8 h-8" style="color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-heading-4 mb-3">Verified properties</h3>
                    <p class="text-body" style="color: var(--text-secondary);">
                        Every property is carefully reviewed and verified by our team to ensure quality and authenticity.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-6 flex items-center justify-center" 
                         style="background-color: rgba(79, 70, 229, 0.1); border-radius: var(--radius-lg);">
                        <svg class="w-8 h-8" style="color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-heading-4 mb-3">Trusted landlords</h3>
                    <p class="text-body" style="color: var(--text-secondary);">
                        Connect directly with verified property owners and landlords across Rwanda.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-6 flex items-center justify-center" 
                         style="background-color: rgba(79, 70, 229, 0.1); border-radius: var(--radius-lg);">
                        <svg class="w-8 h-8" style="color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-heading-4 mb-3">Local expertise</h3>
                    <p class="text-body" style="color: var(--text-secondary);">
                        Built specifically for Rwanda with deep local knowledge and dedicated support.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-16">
                <h2 class="text-heading-2 mb-4">How it works</h2>
                <p class="text-body-large" style="color: var(--text-secondary);">
                    Finding your perfect home is simple with Murugo
                </p>
            </div>
            
            <div class="grid grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-6 flex items-center justify-center text-heading-3" 
                         style="background-color: var(--color-accent); color: white; border-radius: var(--radius-lg);">
                        1
                    </div>
                    <h3 class="text-heading-4 mb-3">Search & discover</h3>
                    <p class="text-body" style="color: var(--text-secondary);">
                        Browse through hundreds of verified properties using our advanced search filters.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-6 flex items-center justify-center text-heading-3" 
                         style="background-color: var(--color-accent); color: white; border-radius: var(--radius-lg);">
                        2
                    </div>
                    <h3 class="text-heading-4 mb-3">Connect & communicate</h3>
                    <p class="text-body" style="color: var(--text-secondary);">
                        Message landlords directly through our secure platform to ask questions and schedule viewings.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-6 flex items-center justify-center text-heading-3" 
                         style="background-color: var(--color-accent); color: white; border-radius: var(--radius-lg);">
                        3
                    </div>
                    <h3 class="text-heading-4 mb-3">Move in</h3>
                    <p class="text-body" style="color: var(--text-secondary);">
                        Complete your rental agreement and move into your new home with confidence.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section" style="background-color: var(--text-primary);">
        <div class="container text-center">
            <h2 class="text-heading-2 mb-4" style="color: white;">
                Ready to find your home?
            </h2>
            <p class="text-body-large mb-8" style="color: rgba(255, 255, 255, 0.8);">
                Join thousands of satisfied renters and landlords on Murugo
            </p>
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                Get started today
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background-color: var(--bg-secondary); padding: var(--space-12) 0;">
        <div class="container">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-heading-4" style="color: var(--color-accent);">Murugo</h3>
                    <p class="text-body-small mt-2">Find your perfect home in Rwanda</p>
                </div>
                <div class="flex gap-8">
                    <div>
                        <h4 class="text-body font-medium mb-3">Platform</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('properties.index') }}" class="text-body-small" style="color: var(--text-secondary);">Browse properties</a></li>
                            <li><a href="{{ route('register') }}" class="text-body-small" style="color: var(--text-secondary);">Sign up</a></li>
                            <li><a href="{{ route('login') }}" class="text-body-small" style="color: var(--text-secondary);">Sign in</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-body font-medium mb-3">Support</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-body-small" style="color: var(--text-secondary);">Help center</a></li>
                            <li><a href="#" class="text-body-small" style="color: var(--text-secondary);">Contact us</a></li>
                            <li><a href="#" class="text-body-small" style="color: var(--text-secondary);">Privacy policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-8" style="border-top: 1px solid var(--border-light);">
                <p class="text-body-small text-center" style="color: var(--text-secondary);">
                    © 2024 Murugo Real Estate Platform. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
