@extends('layouts.app')

@section('title', 'Fraud Detection Dashboard')
@section('description', 'Monitor and manage fraud detection alerts')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Dashboard Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-heading-1 text-gray-900">Fraud Detection Dashboard</h1>
                    <p class="text-body text-gray-600 mt-1">Monitor suspicious activity and review flagged entities</p>
                </div>
                <div class="flex space-x-3">
                    <form action="{{ route('admin.fraud-detection.run-users') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-outline">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Scan Users
                        </button>
                    </form>
                    <form action="{{ route('admin.fraud-detection.run-properties') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-outline">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Scan Properties
                        </button>
                    </form>
                    <a href="{{ route('admin.fraud-detection.export', ['type' => $type]) }}" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Flagged -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Flagged</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_flagged'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unreviewed -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Unreviewed</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['unreviewed'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Critical Risk -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Critical Risk</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['critical'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- High Risk -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">High Risk</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['high'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-6">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.fraud-detection.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select name="type" class="form-select w-full">
                            <option value="all" {{ $type == 'all' ? 'selected' : '' }}>All</option>
                            <option value="users" {{ $type == 'users' ? 'selected' : '' }}>Users</option>
                            <option value="properties" {{ $type == 'properties' ? 'selected' : '' }}>Properties</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Risk Level</label>
                        <select name="risk_level" class="form-select w-full">
                            <option value="">All Levels</option>
                            <option value="critical" {{ $riskLevel == 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="high" {{ $riskLevel == 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ $riskLevel == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ $riskLevel == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reviewed</label>
                        <select name="reviewed" class="form-select w-full">
                            <option value="">All</option>
                            <option value="no" {{ $reviewed == 'no' ? 'selected' : '' }}>Unreviewed</option>
                            <option value="yes" {{ $reviewed == 'yes' ? 'selected' : '' }}>Reviewed</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn btn-primary w-full">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Fraud Scores Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fraud Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Factors</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($fraudScores as $score)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ class_basename($score->scoreable_type) }} #{{ $score->scoreable_id }}
                                            </div>
                                            @if($score->scoreable)
                                            <div class="text-sm text-gray-500">
                                                {{ $score->scoreable->name ?? $score->scoreable->title ?? 'N/A' }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $score->fraud_score }}/100</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($score->risk_level == 'critical') bg-red-100 text-red-800
                                        @elseif($score->risk_level == 'high') bg-orange-100 text-orange-800
                                        @elseif($score->risk_level == 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($score->risk_level) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if($score->risk_factors && count($score->risk_factors) > 0)
                                            <ul class="list-disc list-inside">
                                                @foreach(array_slice($score->risk_factors, 0, 2) as $factor)
                                                <li class="text-xs">{{ $factor }}</li>
                                                @endforeach
                                                @if(count($score->risk_factors) > 2)
                                                <li class="text-xs text-gray-500">+{{ count($score->risk_factors) - 2 }} more</li>
                                                @endif
                                            </ul>
                                        @else
                                            <span class="text-gray-400">None</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($score->admin_reviewed)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Reviewed
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $score->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.fraud-detection.show', $score->id) }}" class="text-primary-600 hover:text-primary-900 mr-3">View</a>
                                    @if(!$score->admin_reviewed)
                                    <form action="{{ route('admin.fraud-detection.review', $score->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">Mark Reviewed</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No fraud scores found. Run detection to generate scores.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $fraudScores->links() }}
        </div>
    </div>
</div>
@endsection
