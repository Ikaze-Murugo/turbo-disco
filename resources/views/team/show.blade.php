@extends('layouts.app')

@section('title', $member['name'] . ' - Team - Murugo')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/3 mb-8 md:mb-0">
                    <img class="w-48 h-48 rounded-full mx-auto object-cover border-4 border-white shadow-lg" 
                         src="{{ $member['image'] }}" 
                         alt="{{ $member['name'] }}"
                         onerror="this.src='https://via.placeholder.com/300x300?text={{ urlencode($member['name']) }}'">
                </div>
                <div class="md:w-2/3 md:pl-8 text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $member['name'] }}</h1>
                    <p class="text-xl md:text-2xl text-indigo-100 mb-6">{{ $member['position'] }}</p>
                    <p class="text-lg text-indigo-100 max-w-2xl">{{ $member['bio'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">About {{ $member['name'] }}</h2>
                        <div class="prose prose-lg max-w-none">
                            <p class="text-gray-700 leading-relaxed mb-6">
                                {{ $member['full_bio'] ?? $member['bio'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Achievements -->
                    @if(isset($member['achievements']) && count($member['achievements']) > 0)
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Key Achievements</h3>
                        <ul class="space-y-4">
                            @foreach($member['achievements'] as $achievement)
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="ml-3 text-gray-700">{{ $achievement }}</p>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Contact Information -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                        <div class="space-y-3">
                            @if(isset($member['email']))
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:{{ $member['email'] }}" class="text-indigo-600 hover:text-indigo-500">
                                    {{ $member['email'] }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Connect</h3>
                        <div class="flex space-x-4">
                            @if(isset($member['linkedin']))
                            <a href="{{ $member['linkedin'] }}" 
                               class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors"
                               target="_blank" rel="noopener noreferrer">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                            @endif
                            @if(isset($member['twitter']))
                            <a href="{{ $member['twitter'] }}" 
                               class="flex items-center justify-center w-10 h-10 bg-blue-400 text-white rounded-full hover:bg-blue-500 transition-colors"
                               target="_blank" rel="noopener noreferrer">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"></path>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Back to Team -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <a href="{{ route('team.index') }}" 
                           class="inline-flex items-center w-full justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Team
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
