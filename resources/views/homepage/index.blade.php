@extends('layouts.app')

@section('title', 'Find Your Perfect Home in Rwanda')
@section('description', 'Discover thousands of rental properties across Rwanda. Connect with landlords, find your dream home, or list your property on Murugo.')

@section('content')
<div id="main-content">
        <!-- Hero Section -->
    <section class="min-h-screen flex items-center relative overflow-hidden bg-slate-900">
        <div class="absolute inset-0 bg-slate-800 opacity-50"></div>
        <div class="container relative z-10">
            <div class="text-center text-white">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    Find Your Perfect Home in 
                    <span class="text-yellow-300">Rwanda</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-gray-100 max-w-3xl mx-auto">
                    Discover thousands of rental properties across Kigali and beyond. 
                    Connect with trusted landlords and find your dream home today.
                </p>
                
                <!-- Hero Search Form -->
                <div class="max-w-4xl mx-auto">
                    <div class="card p-8 bg-white/95 backdrop-blur-sm">
                        <form action="{{ route('homepage.search') }}" method="GET" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Location Search -->
                                <div class="md:col-span-2">
                                    <label class="form-label">Location</label>
                                    <input type="text" 
                                           name="location" 
                                           placeholder="Enter city, district, or neighborhood"
                                           class="form-input"
                                           value="{{ request('location') }}">
                                </div>
                                
                                <!-- Property Type -->
                                <div>
                                    <label class="form-label">Property Type</label>
                                    <select name="property_type" class="form-input">
                                        <option value="">Any Type</option>
                                        <option value="house" {{ request('property_type') == 'house' ? 'selected' : '' }}>House</option>
                                        <option value="apartment" {{ request('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                        <option value="studio" {{ request('property_type') == 'studio' ? 'selected' : '' }}>Studio</option>
                                        <option value="condo" {{ request('property_type') == 'condo' ? 'selected' : '' }}>Condo</option>
                                        <option value="villa" {{ request('property_type') == 'villa' ? 'selected' : '' }}>Villa</option>
                                    </select>
                                </div>
                                
                                <!-- Price Range -->
                                <div>
                                    <label class="form-label">Max Price</label>
                                    <select name="max_price" class="form-input">
                                        <option value="">Any Price</option>
                                        <option value="50000" {{ request('max_price') == '50000' ? 'selected' : '' }}>RWF 50,000</option>
                                        <option value="100000" {{ request('max_price') == '100000' ? 'selected' : '' }}>RWF 100,000</option>
                                        <option value="200000" {{ request('max_price') == '200000' ? 'selected' : '' }}>RWF 200,000</option>
                                        <option value="500000" {{ request('max_price') == '500000' ? 'selected' : '' }}>RWF 500,000</option>
                                        <option value="1000000" {{ request('max_price') == '1000000' ? 'selected' : '' }}>RWF 1,000,000+</option>
                                    </select>
                                </div>
                            </div>
                            
                                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Search Properties
                                    </button>
                                    <a href="{{ route('properties.search-map') }}" class="btn btn-secondary btn-lg">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Map Search
                                    </a>
                                    <a href="{{ route('register') }}" class="btn btn-outline btn-lg">
                                        List Your Property
                                    </a>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

        <!-- Market Statistics Section -->
        <section class="section bg-gray-50">
            <div class="container">
                <div class="text-center mb-12">
                    <h2 class="text-heading-2 mb-4">Rwanda's Leading Property Platform</h2>
                    <p class="text-body-lg max-w-2xl mx-auto">
                        Join thousands of satisfied users who have found their perfect home through Murugo
                    </p>
                </div>
            
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600 mb-2">{{ number_format($marketStats['total_properties']) }}</div>
                    <div class="text-gray-600">Active Properties</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600 mb-2">{{ number_format($marketStats['total_landlords']) }}</div>
                    <div class="text-gray-600">Trusted Landlords</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600 mb-2">{{ number_format($marketStats['total_renters']) }}</div>
                    <div class="text-gray-600">Happy Renters</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600 mb-2">RWF {{ number_format($marketStats['average_rent']) }}</div>
                    <div class="text-gray-600">Average Rent</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600 mb-2">{{ number_format($marketStats['properties_this_month']) }}</div>
                    <div class="text-gray-600">New This Month</div>
                </div>
            </div>
        </div>
    </section>

        <!-- Featured Properties Section -->
        <section class="section">
            <div class="container">
                <div class="text-center mb-12">
                    <h2 class="text-heading-2 mb-4">Featured Properties</h2>
                    <p class="text-body-lg max-w-2xl mx-auto">
                        Handpicked premium properties in the best locations across Rwanda
                    </p>
                </div>
            
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($featuredProperties as $property)
                        <div class="property-card group cursor-pointer">
                            <div class="relative overflow-hidden rounded-t-xl">
                            @if($property->images->count() > 0)
                                <img src="{{ Storage::url($property->images->first()->path) }}" 
                                     alt="{{ $property->title }}"
                                     class="property-card-image">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-medium text-primary-600">
                                RWF {{ number_format($property->price) }}
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2 text-gray-900">{{ $property->title }}</h3>
                            <p class="text-gray-600 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $property->location }}
                            </p>
                            
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <span>{{ $property->bedrooms }} bed</span>
                                <span>{{ $property->bathrooms }} bath</span>
                                @if($property->area)
                                    <span>{{ $property->area }} m²</span>
                                @endif
                            </div>
                            
                                <a href="{{ route('properties.show', $property) }}" 
                                   class="btn btn-primary w-full text-center">
                                    View Details
                                </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg">No featured properties available at the moment.</p>
                    </div>
                @endforelse
            </div>
            
                <div class="text-center mt-12">
                    <a href="{{ route('properties.index') }}" class="btn btn-secondary">
                        View All Properties
                    </a>
                </div>
        </div>
    </section>

        <!-- For Landlords Section -->
        <section class="section bg-gray-50">
            <div class="container">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-heading-2 mb-6">For Property Owners</h2>
                        <p class="text-body mb-6">
                        Join thousands of successful landlords who trust Murugo to help them find quality tenants 
                        and maximize their rental income. Our platform provides all the tools you need to manage 
                        your properties effectively.
                    </p>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                <svg class="w-3 h-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Reach Qualified Renters</h3>
                                <p class="text-gray-600">Connect with thousands of verified renters actively looking for properties</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                <svg class="w-3 h-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Professional Management Tools</h3>
                                <p class="text-gray-600">Analytics dashboard, tenant screening, and communication tools</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                <svg class="w-3 h-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Maximize Your Income</h3>
                                <p class="text-gray-600">Market insights and pricing tools to optimize your rental rates</p>
                            </div>
                        </div>
                    </div>
                    
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                List Your Property
                            </a>
                            <a href="#" class="btn btn-secondary">
                                Learn More
                            </a>
                        </div>
                </div>
                
                <div class="relative">
                    <div class="card p-8">
                        <h3 class="text-xl font-semibold mb-4">Success Story</h3>
                        <blockquote class="text-gray-600 mb-4">
                            "Murugo helped me find the perfect tenant for my apartment in just 2 weeks. 
                            The platform is easy to use and the support team is excellent."
                        </blockquote>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-primary-600 font-semibold">JB</span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Jean Baptiste</div>
                                <div class="text-sm text-gray-500">Property Owner, Kigali</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

        <!-- Testimonials Section -->
        <section class="section">
            <div class="container">
                <div class="text-center mb-12">
                    <h2 class="text-heading-2 mb-4">What Our Users Say</h2>
                    <p class="text-body-lg max-w-2xl mx-auto">
                        Don't just take our word for it. Here's what our community has to say about Murugo.
                    </p>
                </div>
            
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($testimonials as $testimonial)
                        <div class="card p-6">
                        <div class="flex items-center mb-4">
                            @for($i = 0; $i < $testimonial['rating']; $i++)
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-gray-600 mb-4">"{{ $testimonial['content'] }}"</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-primary-600 font-semibold text-sm">
                                    {{ substr($testimonial['name'], 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $testimonial['name'] }}</div>
                                <div class="text-sm text-gray-500">{{ $testimonial['role'] }}, {{ $testimonial['location'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

        <!-- CTA Section -->
        <section class="section bg-slate-900">
            <div class="container text-center text-white">
                <h2 class="text-4xl font-bold mb-4">Ready to Find Your Perfect Home?</h2>
                <p class="text-xl mb-8 text-gray-100 max-w-2xl mx-auto">
                    Join thousands of satisfied users who have found their dream home through Murugo. 
                    Start your search today!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="btn btn-primary bg-white text-slate-900 hover:bg-gray-100">
                        Get Started Free
                    </a>
                    <a href="{{ route('properties.index') }}" class="btn btn-outline border-white text-white hover:bg-white hover:text-slate-900">
                        Browse Properties
                    </a>
                </div>
            </div>
        </section>

     </div>
     @endsection

     @push('scripts')
<script>
    // Simple search suggestions (can be enhanced later)
    document.addEventListener('DOMContentLoaded', function() {
        const locationInput = document.querySelector('input[name="location"]');
        if (locationInput) {
            locationInput.addEventListener('input', function(e) {
                // Add autocomplete functionality here
            });
        }
    });
</script>
@endpush
