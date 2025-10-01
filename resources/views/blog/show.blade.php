@extends('layouts.app')

@section('title', $post['title'] . ' - Blog - Murugo')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Article Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Breadcrumb -->
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('blog.index') }}" class="ml-1 text-gray-700 hover:text-indigo-600 md:ml-2">Blog</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-gray-500 md:ml-2">{{ $post['title'] }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Article Meta -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                        {{ $post['category'] }}
                    </span>
                    <span class="ml-3 text-sm text-gray-500">{{ $post['read_time'] }}</span>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $post['title'] }}</h1>
                <p class="text-xl text-gray-600 mb-6">{{ $post['excerpt'] }}</p>
                
                <!-- Author Info -->
                <div class="flex items-center">
                    <img class="h-12 w-12 rounded-full" 
                         src="{{ $post['author_image'] }}" 
                         alt="{{ $post['author'] }}"
                         onerror="this.src='https://via.placeholder.com/48x48?text={{ urlencode(substr($post['author'], 0, 1)) }}'">
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">{{ $post['author'] }}</p>
                        <p class="text-sm text-gray-500">{{ date('F d, Y', strtotime($post['published_at'])) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Article Image -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <img class="w-full h-64 md:h-96 object-cover rounded-lg shadow-lg" 
             src="{{ $post['image'] }}" 
             alt="{{ $post['title'] }}"
             onerror="this.src='https://via.placeholder.com/800x400?text={{ urlencode($post['title']) }}'">
    </div>

    <!-- Article Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <article class="bg-white rounded-lg shadow-lg p-8">
                    <div class="prose prose-lg max-w-none">
                        {!! $post['content'] !!}
                    </div>

                    <!-- Tags -->
                    @if(isset($post['tags']) && count($post['tags']) > 0)
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post['tags'] as $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                {{ $tag }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Share Buttons -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Share this article</h3>
                        <div class="flex space-x-4">
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($post['title']) }}&url={{ urlencode(request()->url()) }}" 
                               target="_blank" rel="noopener noreferrer"
                               class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"></path>
                                </svg>
                                Twitter
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" 
                               target="_blank" rel="noopener noreferrer"
                               class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"></path>
                                </svg>
                                LinkedIn
                            </a>
                            <button onclick="navigator.clipboard.writeText('{{ request()->url() }}')" 
                                    class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Copy Link
                            </button>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Author Card -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">About the Author</h3>
                    <div class="flex items-center mb-4">
                        <img class="h-12 w-12 rounded-full" 
                             src="{{ $post['author_image'] }}" 
                             alt="{{ $post['author'] }}"
                             onerror="this.src='https://via.placeholder.com/48x48?text={{ urlencode(substr($post['author'], 0, 1)) }}'">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $post['author'] }}</p>
                            <p class="text-sm text-gray-500">Real Estate Expert</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">
                        Experienced professional with deep knowledge of the Rwandan real estate market.
                    </p>
                </div>

                <!-- Related Posts -->
                @if(count($relatedPosts) > 0)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Articles</h3>
                    <div class="space-y-4">
                        @foreach($relatedPosts as $relatedPost)
                        <article class="flex">
                            <img class="h-16 w-16 rounded-lg object-cover flex-shrink-0" 
                                 src="{{ $relatedPost['image'] }}" 
                                 alt="{{ $relatedPost['title'] }}"
                                 onerror="this.src='https://via.placeholder.com/64x64?text={{ urlencode($relatedPost['title']) }}'">
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-gray-900 mb-1">
                                    <a href="{{ route('blog.show', $relatedPost['slug']) }}" 
                                       class="hover:text-indigo-600 transition-colors">
                                        {{ $relatedPost['title'] }}
                                    </a>
                                </h4>
                                <p class="text-xs text-gray-500">{{ $relatedPost['read_time'] }}</p>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Back to Blog -->
    <div class="bg-white border-t border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <a href="{{ route('blog.index') }}" 
               class="inline-flex items-center text-indigo-600 hover:text-indigo-500 font-medium">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Blog
            </a>
        </div>
    </div>
</div>
@endsection
