@extends('layouts.app')

@section('title', $category['name'] . ' - Blog - Murugo')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Category Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <!-- Breadcrumb -->
                <nav class="flex justify-center mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="text-indigo-200 hover:text-white">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Home
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-indigo-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('blog.index') }}" class="ml-1 text-indigo-200 hover:text-white md:ml-2">Blog</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-indigo-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-white md:ml-2">{{ $category['name'] }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <h1 class="text-4xl md:text-5xl font-bold mb-6">{{ $category['name'] }}</h1>
                <p class="text-xl md:text-2xl text-indigo-100 max-w-3xl mx-auto">
                    {{ $category['description'] }}
                </p>
            </div>
        </div>
    </div>

    <!-- Category Stats -->
    <div class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ count($categoryPosts) }} {{ count($categoryPosts) === 1 ? 'Article' : 'Articles' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Articles -->
    <div class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(count($categoryPosts) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($categoryPosts as $post)
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
                                    {{ $category['name'] }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500">{{ $post['read_time'] }}</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                <a href="{{ route('blog.show', $post['slug']) }}" 
                                   class="hover:text-indigo-600 transition-colors">
                                    {{ $post['title'] }}
                                </a>
                            </h3>
                            <p class="text-gray-600 mb-4 text-sm">{{ \Illuminate\Support\Str::limit($post['excerpt'], 120) }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img class="h-6 w-6 rounded-full" 
                                         src="{{ $post['author_image'] ?? '/images/team/ceo.png' }}" 
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
                    <div class="mx-auto h-24 w-24 text-gray-400 mb-6">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No articles yet</h3>
                    <p class="text-gray-600 mb-6">
                        We're working on creating great content for this category. Check back soon!
                    </p>
                    <a href="{{ route('blog.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Browse All Articles
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Other Categories -->
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Explore Other Categories</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $otherCategories = [
                        ['name' => 'Market Trends', 'slug' => 'market-trends', 'color' => 'blue'],
                        ['name' => 'Property Tips', 'slug' => 'property-tips', 'color' => 'green'],
                        ['name' => 'Investment Guide', 'slug' => 'investment-guide', 'color' => 'purple'],
                        ['name' => 'Neighborhood Spotlight', 'slug' => 'neighborhood-spotlight', 'color' => 'orange'],
                        ['name' => 'Legal & Finance', 'slug' => 'legal-finance', 'color' => 'red']
                    ];
                    $otherCategories = array_filter($otherCategories, function($cat) use ($slug) {
                        return $cat['slug'] !== $slug;
                    });
                @endphp
                
                @foreach(array_slice($otherCategories, 0, 4) as $otherCategory)
                <a href="{{ route('blog.category', $otherCategory['slug']) }}" 
                   class="group bg-white rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-{{ $otherCategory['color'] }}-100 rounded-lg flex items-center justify-center group-hover:bg-{{ $otherCategory['color'] }}-200 transition-colors">
                            <svg class="h-6 w-6 text-{{ $otherCategory['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-{{ $otherCategory['color'] }}-600 transition-colors">
                        {{ $otherCategory['name'] }}
                    </h3>
                    <p class="text-gray-600 text-sm mt-2">
                        Explore articles about {{ strtolower($otherCategory['name']) }}.
                    </p>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Newsletter Signup -->
    <div class="bg-indigo-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Stay Updated</h2>
            <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                Get the latest {{ strtolower($category['name']) }} articles and real estate insights delivered to your inbox
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
