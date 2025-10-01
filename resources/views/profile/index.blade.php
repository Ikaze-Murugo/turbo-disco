<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    My Profile
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Manage your profile information and settings
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('profile.edit') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                    Edit Profile
                </a>
                <a href="{{ route('profile.settings') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    Settings
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Overview -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Profile Header -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center space-x-6">
                                <!-- Profile Picture -->
                                <div class="relative">
                                    <img src="{{ $user->getProfilePictureUrl() }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                                    <div class="absolute -bottom-2 -right-2 bg-green-500 w-6 h-6 rounded-full border-2 border-white"></div>
                                </div>
                                
                                <!-- Profile Info -->
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                                    <p class="text-gray-600">{{ ucfirst($user->role) }}</p>
                                    @if($user->location)
                                        <p class="text-sm text-gray-500 flex items-center mt-1">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $user->location }}
                                        </p>
                                    @endif
                                    @if($user->bio)
                                        <p class="text-gray-700 mt-2">{{ $user->bio }}</p>
                                    @endif
                                </div>
                                
                                <!-- Profile Completion -->
                                <div class="text-right">
                                    <div class="text-sm text-gray-600 mb-2">Profile Completion</div>
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: {{ $user->getProfileCompletionPercentage() }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $user->getProfileCompletionPercentage() }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role-Specific Information -->
                    @if($user->isRenter())
                        <!-- Renter Profile Content -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Your Activity</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                                        <div class="text-2xl font-bold text-blue-600">{{ $statistics['properties_viewed'] ?? 0 }}</div>
                                        <div class="text-sm text-gray-600">Properties Viewed</div>
                                    </div>
                                    <div class="text-center p-4 bg-red-50 rounded-lg">
                                        <div class="text-2xl font-bold text-red-600">{{ $statistics['properties_favorited'] ?? 0 }}</div>
                                        <div class="text-sm text-gray-600">Favorites</div>
                                    </div>
                                    <div class="text-center p-4 bg-green-50 rounded-lg">
                                        <div class="text-2xl font-bold text-green-600">{{ $statistics['messages_sent'] ?? 0 }}</div>
                                        <div class="text-sm text-gray-600">Messages Sent</div>
                                    </div>
                                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                        <div class="text-2xl font-bold text-yellow-600">{{ $statistics['reviews_given'] ?? 0 }}</div>
                                        <div class="text-sm text-gray-600">Reviews Given</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($user->isLandlord())
                        <!-- Landlord Profile Content -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Business Overview</h4>
                                @if($user->business_name)
                                    <div class="mb-4">
                                        <span class="text-sm text-gray-600">Business Name:</span>
                                        <span class="font-medium">{{ $user->business_name }}</span>
                                    </div>
                                @endif
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                                        <div class="text-2xl font-bold text-blue-600">{{ $statistics['properties_listed'] ?? 0 }}</div>
                                        <div class="text-sm text-gray-600">Properties Listed</div>
                                    </div>
                                    <div class="text-center p-4 bg-green-50 rounded-lg">
                                        <div class="text-2xl font-bold text-green-600">{{ $statistics['total_views'] ?? 0 }}</div>
                                        <div class="text-sm text-gray-600">Total Views</div>
                                    </div>
                                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                        <div class="text-2xl font-bold text-yellow-600">{{ $statistics['total_inquiries'] ?? 0 }}</div>
                                        <div class="text-sm text-gray-600">Inquiries</div>
                                    </div>
                                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                                        <div class="text-2xl font-bold text-purple-600">RWF {{ number_format($statistics['revenue_generated'] ?? 0) }}</div>
                                        <div class="text-sm text-gray-600">Revenue</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($user->isAdmin())
                        <!-- Admin Profile Content -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Admin Overview</h4>
                                <div class="mb-4">
                                    <span class="text-sm text-gray-600">Admin Level:</span>
                                    <span class="font-medium">{{ $user->admin_level }}</span>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                                        <div class="text-2xl font-bold text-blue-600">{{ $statistics['reports_processed'] ?? 0 }}</div>
                                        <div class="text-sm text-gray-600">Reports Processed</div>
                                    </div>
                                    <div class="text-center p-4 bg-green-50 rounded-lg">
                                        <div class="text-2xl font-bold text-green-600">{{ $statistics['properties_approved'] ?? 0 }}</div>
                                        <div class="text-sm text-gray-600">Properties Approved</div>
                                    </div>
                                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                        <div class="text-2xl font-bold text-yellow-600">{{ $statistics['users_managed'] ?? 0 }}</div>
                                        <div class="text-sm text-gray-600">Users Managed</div>
                                    </div>
                                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                                        <div class="text-2xl font-bold text-purple-600">{{ $statistics['tickets_resolved'] ?? 0 }}</div>
                                        <div class="text-sm text-gray-600">Tickets Resolved</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-2">
                                <a href="{{ route('profile.edit') }}" 
                                   class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                                    Edit Profile
                                </a>
                                <a href="{{ route('profile.settings') }}" 
                                   class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                                    Account Settings
                                </a>
                                <a href="{{ route('profile.statistics') }}" 
                                   class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                                    View Statistics
                                </a>
                                @if($user->isRenter())
                                    <a href="{{ route('favorites.index') }}" 
                                       class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                                        My Favorites
                                    </a>
                                @elseif($user->isLandlord())
                                    <a href="{{ route('properties.index') }}" 
                                       class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                                        My Properties
                                    </a>
                                @elseif($user->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                                        Admin Dashboard
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Profile Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Profile Information</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium">{{ $user->email }}</span>
                                </div>
                                @if($user->phone_number)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="font-medium">{{ $user->phone_number }}</span>
                                    </div>
                                @endif
                                @if($user->date_of_birth)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Date of Birth:</span>
                                        <span class="font-medium">{{ $user->date_of_birth->format('M d, Y') }}</span>
                                    </div>
                                @endif
                                @if($user->gender)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Gender:</span>
                                        <span class="font-medium">{{ ucfirst($user->gender) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Member Since:</span>
                                    <span class="font-medium">{{ $user->created_at->format('M Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Last Active:</span>
                                    <span class="font-medium">{{ $user->last_active_at ? $user->last_active_at->diffForHumans() : 'Never' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
