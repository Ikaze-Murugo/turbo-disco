@extends('layouts.app')

@section('title', 'Analytics Dashboard')
@section('description', 'Reports and analytics dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Reports Analytics</h1>
                    <p class="text-gray-600 mt-1">Reports and ticket analytics dashboard</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.analytics.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        View Full Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Reports -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Reports</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['total_reports']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Reports -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pending Reports</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['pending_reports']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resolved Reports -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Resolved Reports</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['resolved_reports']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Resolution Time -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Avg Resolution Time</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['avg_resolution_time'], 1) }}h</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Reports -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Message Reports</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total</span>
                            <span class="font-semibold text-blue-600">{{ number_format($metrics['message_reports']['total']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Pending</span>
                            <span class="font-semibold text-yellow-600">{{ number_format($metrics['message_reports']['pending']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Resolved</span>
                            <span class="font-semibold text-green-600">{{ number_format($metrics['message_reports']['resolved']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Report Categories</h3>
                    <div class="space-y-2">
                        @forelse($metrics['category_distribution'] as $category)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ ucfirst($category->category) }}</span>
                                <span class="font-semibold text-gray-900">{{ $category->count }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No reports yet</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Admin Workload -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Admin Workload</h3>
                    <div class="space-y-2">
                        @forelse($metrics['admin_workload'] as $admin)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ $admin->name }}</span>
                                <span class="font-semibold text-gray-900">{{ $admin->active_tickets_count }} active</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No admins found</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Trends Chart -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Trends (Last 30 Days)</h3>
                <div class="h-64">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Trends Chart
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    const trendsData = @json($metrics['recent_trends']);
    
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: trendsData.map(item => item.date),
            datasets: [{
                label: 'Reports Created',
                data: trendsData.map(item => item.count),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
