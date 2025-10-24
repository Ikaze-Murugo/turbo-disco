@extends('public.layout')

@section('title', 'Murugo Property Platform - Find Your Perfect Home in Rwanda')
@section('description', 'Discover the best properties for rent and sale in Rwanda. Browse apartments, houses, and commercial spaces with Murugo Property Platform.')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Find Your Perfect Home in Rwanda
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-100">
                Discover amazing properties for rent and sale across Rwanda
            </p>
            
            <!-- Search Box -->
            <div class="max-w-4xl mx-auto">
                <div class="search-box rounded-2xl p-6 shadow-2xl">
                    <form action="{{ route('public.search') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Location -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                <input type="text" name="location" placeholder="Kigali, Rwanda" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <!-- Property Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                                <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Any Type</option>
                                    <option value="apartment">Apartment</option>
                                    <option value="house">House</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="land">Land</option>
                                </select>
                            </div>
                            
                            <!-- Purpose -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Purpose</label>
                                <select name="purpose" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Rent or Sale</option>
                                    <option value="rent">For Rent</option>
                                    <option value="sale">For Sale</option>
                                </select>
                            </div>
                            
                            <!-- Search Button -->
                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                    Search Properties
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">{{ number_format($stats['total_properties']) }}</div>
                <div class="text-gray-600">Active Properties</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">{{ number_format($stats['total_landlords']) }}</div>
                <div class="text-gray-600">Trusted Landlords</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">100%</div>
                <div class="text-gray-600">Verified Properties</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties Section -->
@if($featuredProperties->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Properties</h2>
            <p class="text-gray-600">Handpicked properties that stand out from the rest</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredProperties as $property)
                <div class="property-card bg-white rounded-xl shadow-lg overflow-hidden">
                    @if($property->images->count() > 0)
                        <div class="h-48 bg-gray-200 relative">
                            <img src="{{ Storage::url($property->images->first()->path) }}" 
                                 alt="{{ $property->title }}" 
                                 class="w-full h-full object-cover">
                            <div class="absolute top-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Featured
                            </div>
                        </div>
                    @else
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400 text-lg">No Image</span>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $property->title }}</h3>
                        <p class="text-gray-600 mb-3">{{ $property->location }}</p>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ number_format($property->price) }} RWF
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ ucfirst($property->type) }}
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span>{{ $property->bedrooms }} Bedrooms</span>
                            <span>{{ $property->bathrooms }} Bathrooms</span>
                        </div>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('public.property.show', $property) }}" 
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                View Details
                            </a>
                            @guest
                                <a href="{{ route('login') }}" 
                                   class="flex-1 border border-blue-600 text-blue-600 text-center py-2 px-4 rounded-lg hover:bg-blue-50 transition-colors">
                                    Login to Contact
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-12">
            <a href="{{ route('public.properties') }}" 
               class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                View All Properties
            </a>
        </div>
    </div>
</section>
@endif

<!-- Recent Properties Section -->
@if($recentProperties->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Recently Added</h2>
            <p class="text-gray-600">Fresh listings just added to our platform</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($recentProperties as $property)
                <x-property-card 
                    :property="$property"
                    :show-carousel="true"
                    :enable-favorites="auth()->check() && auth()->user()->isRenter()"
                    :enable-comparison="auth()->check() && auth()->user()->isRenter()"
                    :show-actions="true"
                    layout="grid"
                    class="w-full"
                />
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Call to Action Section -->
<section class="py-16 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Find Your Perfect Property?</h2>
        <p class="text-xl mb-8 text-blue-100">
            Join thousands of satisfied customers who found their dream home with Murugo
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @guest
                <a href="{{ route('register') }}" 
                   class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Get Started Free
                </a>
                <a href="{{ route('public.properties') }}" 
                   class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                    Browse Properties
                </a>
            @else
                <a href="{{ route('dashboard') }}" 
                   class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Go to Dashboard
                </a>
                <a href="{{ route('public.properties') }}" 
                   class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                    Browse Properties
                </a>
            @endguest
        </div>
    </div>
</section>
@endsection
