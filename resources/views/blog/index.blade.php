@extends('layouts.app')

@section('title', 'Blog - Real Estate News & Tips - Murugo')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="relative bg-slate-900 text-white overflow-hidden" style="min-height: 350px;">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="/images/heroes/blog-hero.png" 
                 alt="Real Estate Blog - Murugo" 
                 class="w-full h-full object-cover object-center">
            <!-- Dark Overlay for Text Readability -->
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        
        <div class="container py-20 md:py-24 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 drop-shadow-2xl">Real Estate Blog</h1>
                <p class="text-lg md:text-xl text-white max-w-3xl mx-auto drop-shadow-lg">
                    Stay informed with the latest market trends, property tips, and investment insights
                </p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="container py-8">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <form method="GET" action="{{ route('blog.index') }}" class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ $searchQuery }}"
                               placeholder="Search articles..."
                               class="form-input input-search">
                    </form>
                </div>

                <!-- Category Filter -->
                <div class="md:w-64">
                    <form method="GET" action="{{ route('blog.index') }}">
                        <select name="category" 
                                onchange="this.form.submit()"
                                class="form-input">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category['slug'] }}" 
                                        {{ $selectedCategory === $category['slug'] ? 'selected' : '' }}>
                                    {{ $category['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Posts -->
    @if(!$selectedCategory && !$searchQuery)
    <div class="section">
        <div class="container">
            <h2 class="text-heading-2 mb-8">Featured Articles</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($featuredPosts as $post)
                <article class="card hover:shadow-lg transition-shadow duration-300">
                    <div class="aspect-w-16 aspect-h-9">
                        <img class="w-full h-48 object-cover" 
                             src="{{ $post['image'] }}" 
                             alt="{{ $post['title'] }}"
                             onerror="this.src='https://via.placeholder.com/600x400?text={{ urlencode($post['title']) }}'">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <span class="badge badge-primary">
                                {{ $post['category'] }}
                            </span>
                            <span class="ml-2 text-sm text-gray-500">{{ $post['read_time'] }}</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">
                            <a href="{{ route('blog.show', $post['slug']) }}" 
                               class="hover:text-indigo-600 transition-colors">
                                {{ $post['title'] }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-4">{{ $post['excerpt'] }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img class="h-8 w-8 rounded-full" 
                                     src="{{ $post['author_image'] }}" 
                                     alt="{{ $post['author'] }}"
                                     onerror="this.src='https://via.placeholder.com/32x32?text={{ urlencode(substr($post['author'], 0, 1)) }}'">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $post['author'] }}</p>
                                    <p class="text-sm text-gray-500">{{ date('M d, Y', strtotime($post['published_at'])) }}</p>
                                </div>
                            </div>
                            <a href="{{ route('blog.show', $post['slug']) }}" 
                               class="btn btn-outline btn-sm">
                                Read More
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- All Posts -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($selectedCategory || $searchQuery)
                <h2 class="text-3xl font-bold text-gray-900 mb-8">
                    @if($searchQuery)
                        Search Results for "{{ $searchQuery }}"
                    @else
                        @php
                            $categoryName = 'Category';
                            foreach($categories as $cat) {
                                if($cat['slug'] === $selectedCategory) {
                                    $categoryName = $cat['name'];
                                    break;
                                }
                            }
                        @endphp
                        {{ $categoryName }} Articles
                    @endif
                </h2>
            @else
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Latest Articles</h2>
            @endif

            @if(count($allPosts) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($allPosts as $post)
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="aspect-w-16 aspect-h-9">
                            <img class="w-full h-48 object-cover" 
                                 src="{{ $post['image'] }}" 
                                 alt="{{ $post['title'] }}"
                                 onerror="this.src='https://via.placeholder.com/400x300?text={{ urlencode($post['title']) }}'">
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $post['category'] }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500">{{ $post['read_time'] }}</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                <a href="{{ route('blog.show', $post['slug']) }}" 
                                   class="hover:text-indigo-600 transition-colors">
                                    {{ $post['title'] }}
                                </a>
                            </h3>
                            <p class="text-gray-600 mb-4 text-sm">{{ \Illuminate\Support\Str::limit($post['excerpt'], 100) }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img class="h-6 w-6 rounded-full" 
                                         src="{{ $post['author_image'] }}" 
                                         alt="{{ $post['author'] }}"
                                         onerror="this.src='https://via.placeholder.com/24x24?text={{ urlencode(substr($post['author'], 0, 1)) }}'">
                                    <div class="ml-2">
                                        <p class="text-xs font-medium text-gray-900">{{ $post['author'] }}</p>
                                        <p class="text-xs text-gray-500">{{ date('M d, Y', strtotime($post['published_at'])) }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('blog.show', $post['slug']) }}" 
                                   class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                    Read →
                                </a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No articles found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($searchQuery)
                            Try adjusting your search terms.
                        @else
                            No articles available in this category yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Categories Section -->
    @if(!$selectedCategory && !$searchQuery)
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Browse by Category</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                <a href="{{ route('blog.category', $category['slug']) }}" 
                   class="group bg-gray-50 rounded-lg p-6 hover:bg-indigo-50 transition-colors">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                        {{ $category['name'] }}
                    </h3>
                    <p class="text-gray-600 text-sm mt-2">
                        Explore articles about {{ strtolower($category['name']) }} and stay informed.
                    </p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Newsletter Signup -->
    <div class="relative py-16 overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="/images/heroes/blog-cta.png" 
                 alt="Stay Updated with Murugo Newsletter" 
                 class="w-full h-full object-cover object-center">
            <!-- Dark Overlay for Text Readability -->
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl font-bold text-white mb-4 drop-shadow-2xl">Stay Updated</h2>
            <p class="text-xl text-white mb-8 max-w-2xl mx-auto drop-shadow-lg">
                Get the latest real estate news and tips delivered to your inbox
            </p>
            <form class="max-w-md mx-auto flex">
                <input type="email" 
                       placeholder="Enter your email"
                       class="flex-1 px-4 py-3 rounded-l-lg border-0 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600">
                <button type="submit" 
                        class="px-6 py-3 bg-white text-indigo-600 font-medium rounded-r-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600 transition-colors">
                    Subscribe
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
