@extends('layouts.app')

@section('title', 'My Reports & Tickets')
@section('description', 'Track your submitted reports and their status')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Reports Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-heading-1 text-gray-900">My Reports & Tickets</h1>
                    <p class="text-body text-gray-600 mt-1">Track the status of your submitted reports</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('reports.create') }}" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Submit New Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Reports</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-warning-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pending</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-info-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Investigating</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['investigating'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Resolved</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['resolved'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Dismissed</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['dismissed'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports List -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-heading-3">Your Reports</h3>
                <span class="badge badge-primary">{{ $reports->count() }}</span>
            </div>
            <div class="card-body">
                @if($reports->count() > 0)
                    <div class="space-y-4">
                        @foreach($reports as $report)
                            <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <h4 class="text-lg font-semibold text-gray-900">
                                                <a href="{{ route('reports.show', $report) }}" class="hover:text-primary-600 transition-colors">
                                                    {{ $report->title }}
                                                </a>
                                            </h4>
                                            <span class="badge {{ $report->status === 'pending' ? 'badge-warning' : ($report->status === 'investigating' ? 'badge-info' : ($report->status === 'resolved' ? 'badge-success' : 'badge-neutral')) }}">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                            <span class="badge {{ $report->priority === 'urgent' ? 'badge-danger' : ($report->priority === 'high' ? 'badge-warning' : ($report->priority === 'medium' ? 'badge-primary' : 'badge-success')) }}">
                                                {{ ucfirst($report->priority) }} Priority
                                            </span>
                                        </div>
                                        
                                        <p class="text-gray-600 mb-4">{{ Str::limit($report->description, 150) }}</p>
                                        
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-4">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                Report #{{ $report->id }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $report->created_at->format('M d, Y \a\t g:i A') }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                {{ ucfirst($report->report_type) }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                {{ ucfirst($report->category) }}
                                            </span>
                                        </div>

                                        <!-- Show latest comment if available -->
                                        @if($report->comments && $report->comments->count() > 0)
                                            <div class="p-4 bg-info-50 border border-info-200 rounded-lg mb-4">
                                                <div class="flex items-center space-x-2 mb-2">
                                                    <svg class="h-4 w-4 text-info-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-info-900">Latest Response</span>
                                                    <span class="text-xs text-info-600">{{ $report->comments->first()->created_at->format('M d, g:i A') }}</span>
                                                </div>
                                                <p class="text-sm text-info-800">{{ Str::limit($report->comments->first()->comment, 100) }}</p>
                                            </div>
                                        @endif

                                        <!-- Show unread notifications -->
                                        @if($report->notifications && $report->notifications->where('is_read', false)->count() > 0)
                                            <div class="mb-4">
                                                <span class="badge badge-danger">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                                    </svg>
                                                    {{ $report->notifications->where('is_read', false)->count() }} new update{{ $report->notifications->where('is_read', false)->count() > 1 ? 's' : '' }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex flex-col items-end space-y-2">
                                        <a href="{{ route('reports.show', $report) }}" 
                                           class="btn btn-outline btn-sm">
                                            View Details
                                        </a>
                                        @if($report->status === 'pending' || $report->status === 'investigating')
                                            <form action="{{ route('reports.follow-up', $report) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-ghost btn-sm text-warning-600 hover:text-warning-800">
                                                    Request Follow-up
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $reports->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-heading-3 text-gray-900 mb-2">No reports yet</h3>
                        <p class="text-body text-gray-600 mb-6">Get started by submitting your first report.</p>
                        <a href="{{ route('reports.create') }}" class="btn btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Submit Report
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection