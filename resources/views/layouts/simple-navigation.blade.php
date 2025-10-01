<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600">
                    Murugo
                </a>
            </div>
            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700">Home</a>
                <a href="{{ route('properties.public.index') }}" class="text-gray-500 hover:text-gray-700">Properties</a>
                <a href="{{ route('blog.index') }}" class="text-gray-500 hover:text-gray-700">Blog</a>
                <a href="{{ route('team.index') }}" class="text-gray-500 hover:text-gray-700">Team</a>
                <a href="{{ route('legal.terms') }}" class="text-gray-500 hover:text-gray-700">Terms</a>
                <a href="{{ route('legal.privacy') }}" class="text-gray-500 hover:text-gray-700">Privacy</a>
            </div>
        </div>
    </div>
</nav>
