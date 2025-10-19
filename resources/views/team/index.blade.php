@extends('layouts.app')

@section('title', 'Our Team - Murugo')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="relative bg-slate-900 text-white overflow-hidden" style="min-height: 350px;">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="/images/heroes/team-hero.png" 
                 alt="Meet Our Team - Murugo" 
                 class="w-full h-full object-cover object-center">
            <!-- Dark Overlay for Text Readability -->
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        
        <div class="container py-20 md:py-24 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 drop-shadow-2xl">Meet Our Team</h1>
                <p class="text-lg md:text-xl text-white max-w-3xl mx-auto drop-shadow-lg">
                    The passionate individuals behind Murugo, working to revolutionize real estate in Rwanda
                </p>
            </div>
        </div>
    </div>

    <!-- Company Stats -->
    <div class="section bg-white">
        <div class="container">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary-600 mb-2">{{ number_format($companyStats['properties_listed']) }}+</div>
                    <div class="text-gray-600">Properties Listed</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary-600 mb-2">{{ number_format($companyStats['happy_customers']) }}+</div>
                    <div class="text-gray-600">Happy Customers</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary-600 mb-2">{{ $companyStats['cities_covered'] }}+</div>
                    <div class="text-gray-600">Cities Covered</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary-600 mb-2">{{ $companyStats['years_experience'] }}+</div>
                    <div class="text-gray-600">Years Experience</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members -->
    <div class="section">
        <div class="container">
            <div class="text-center mb-12">
                <h2 class="text-heading-2 mb-4">Our Leadership Team</h2>
                <p class="text-body-lg max-w-2xl mx-auto">
                    Meet the dedicated professionals who are building the future of real estate in Rwanda
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($teamMembers as $member)
                <div class="card hover:shadow-lg transition-shadow duration-300">
                    <div class="aspect-w-3 aspect-h-4">
                        <img class="w-full h-64 object-cover" 
                             src="{{ $member['image'] }}" 
                             alt="{{ $member['name'] }}"
                             onerror="this.src='https://via.placeholder.com/300x400?text={{ urlencode($member['name']) }}'">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $member['name'] }}</h3>
                        <p class="text-primary-600 font-medium mb-3">{{ $member['position'] }}</p>
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($member['bio'], 120) }}</p>
                        
                        <!-- Social Links -->
                        <div class="flex space-x-3">
                            @if(isset($member['linkedin']))
                            <a href="{{ $member['linkedin'] }}" 
                               class="text-gray-400 hover:text-blue-600 transition-colors"
                               target="_blank" rel="noopener noreferrer">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                            @endif
                            @if(isset($member['twitter']))
                            <a href="{{ $member['twitter'] }}" 
                               class="text-gray-400 hover:text-blue-400 transition-colors"
                               target="_blank" rel="noopener noreferrer">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"></path>
                                </svg>
                            </a>
                            @endif
                            @if(isset($member['email']))
                            <a href="mailto:{{ $member['email'] }}" 
                               class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Company Culture Section -->
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Culture & Values</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    The principles that guide everything we do at Murugo
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Innovation</h3>
                    <p class="text-gray-600 text-sm">We constantly seek new ways to improve the real estate experience</p>
                </div>

                <div class="text-center">
                    <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Passion</h3>
                    <p class="text-gray-600 text-sm">We're passionate about helping people find their perfect home</p>
                </div>

                <div class="text-center">
                    <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Trust</h3>
                    <p class="text-gray-600 text-sm">We build trust through transparency and reliability</p>
                </div>

                <div class="text-center">
                    <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Community</h3>
                    <p class="text-gray-600 text-sm">We believe in building strong communities through real estate</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Join Our Team Section -->
    <div class="relative py-16 overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="/images/heroes/team-cta.png" 
                 alt="Join Our Team at Murugo" 
                 class="w-full h-full object-cover object-center">
            <!-- Dark Overlay for Text Readability -->
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl font-bold text-white mb-4 drop-shadow-2xl">Join Our Team</h2>
            <p class="text-xl text-white mb-8 max-w-2xl mx-auto drop-shadow-lg">
                We're always looking for talented individuals who share our passion for revolutionizing real estate
            </p>
            <a href="mailto:careers@murugo.com" 
               class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                View Open Positions
            </a>
        </div>
    </div>
</div>
@endsection
