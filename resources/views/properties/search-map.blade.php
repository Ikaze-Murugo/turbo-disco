@extends('layouts.app')

@section('title', 'Search Properties - Find Your Perfect Home')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Search Properties</h1>
                    <p class="mt-2 text-gray-600">Find your perfect home with our advanced search tools</p>
                </div>
                <div class="mt-4 md:mt-0 flex gap-3">
                    <a href="{{ route('properties.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        List View
                    </a>
                    <a href="{{ route('properties.map') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.632A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        Map View
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Map Container -->
    <div class="h-screen">
        <x-map.search-map 
            height="100%"
            :show-results="true"
            :results-count="0"
            :search-query="request('q', '')"
            :filters="request()->only(['type', 'min_price', 'max_price', 'bedrooms', 'bathrooms'])"
        />
    </div>
</div>
@endsection