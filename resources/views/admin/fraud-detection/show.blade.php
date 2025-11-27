@extends('layouts.app')

@section('title', 'Fraud Score Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.fraud-detection.index') }}" class="text-primary-600 hover:text-primary-900 flex items-center mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Fraud Detection
            </a>
            <h1 class="text-heading-1 text-gray-900">Fraud Score Details</h1>
        </div>

        <!-- Score Overview Card -->
        <div class="card mb-6">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-5xl font-bold text-gray-900 mb-2">{{ $fraudScore->fraud_score }}</div>
                        <div class="text-sm text-gray-500">Fraud Score (out of 100)</div>
                    </div>
                    <div class="text-center">
                        <span class="inline-flex px-4 py-2 text-lg font-semibold rounded-full 
                            @if($fraudScore->risk_level == 'critical') bg-red-100 text-red-800
                            @elseif($fraudScore->risk_level == 'high') bg-orange-100 text-orange-800
                            @elseif($fraudScore->risk_level == 'medium') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($fraudScore->risk_level) }} Risk
                        </span>
                        <div class="text-sm text-gray-500 mt-2">Risk Level</div>
                    </div>
                    <div class="text-center">
                        @if($fraudScore->admin_reviewed)
                            <span class="inline-flex px-4 py-2 text-lg font-semibold rounded-full bg-green-100 text-green-800">
                                Reviewed
                            </span>
                        @else
                            <span class="inline-flex px-4 py-2 text-lg font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending Review
                            </span>
                        @endif
                        <div class="text-sm text-gray-500 mt-2">Status</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entity Information -->
        <div class="card mb-6">
            <div class="card-header">
                <h2 class="text-lg font-semibold">Entity Information</h2>
            </div>
            <div class="card-body">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ class_basename($fraudScore->scoreable_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">#{{ $fraudScore->scoreable_id }}</dd>
                    </div>
                    @if($fraudScore->scoreable)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name/Title</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $fraudScore->scoreable->name ?? $fraudScore->scoreable->title ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $fraudScore->scoreable->created_at->format('M d, Y') }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Model Version</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $fraudScore->model_version ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $fraudScore->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
                <div class="mt-4">
                    <a href="{{ $fraudScore->scoreable_type === 'App\\Models\\User' ? route('admin.users') : route('admin.properties') }}" 
                       class="btn btn-outline">
                        View {{ class_basename($fraudScore->scoreable_type) }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Risk Factors -->
        <div class="card mb-6">
            <div class="card-header">
                <h2 class="text-lg font-semibold">Risk Factors</h2>
            </div>
            <div class="card-body">
                @if($fraudScore->risk_factors && count($fraudScore->risk_factors) > 0)
                    <ul class="space-y-2">
                        @foreach($fraudScore->risk_factors as $factor)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-900">{{ $factor }}</span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">No specific risk factors identified.</p>
                @endif
            </div>
        </div>

        <!-- Score Breakdown -->
        <div class="card mb-6">
            <div class="card-header">
                <h2 class="text-lg font-semibold">Score Breakdown</h2>
            </div>
            <div class="card-body">
                @if($fraudScore->score_breakdown)
                    <div class="space-y-4">
                        @foreach($fraudScore->score_breakdown as $category => $details)
                        <div class="border-b border-gray-200 pb-4 last:border-0">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-sm font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $category)) }}</h3>
                                <span class="text-sm font-bold text-gray-900">{{ $details['score'] ?? 0 }} points</span>
                            </div>
                            @if(isset($details['factor']) && $details['factor'])
                                <p class="text-sm text-gray-600">{{ $details['factor'] }}</p>
                            @endif
                            @if(isset($details['value']))
                                <p class="text-xs text-gray-500 mt-1">Value: {{ is_array($details['value']) ? json_encode($details['value']) : $details['value'] }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No detailed breakdown available.</p>
                @endif
            </div>
        </div>

        <!-- Review Section -->
        @if(!$fraudScore->admin_reviewed)
        <div class="card mb-6">
            <div class="card-header">
                <h2 class="text-lg font-semibold">Mark as Reviewed</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.fraud-detection.review', $fraudScore->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="4" class="form-textarea w-full" placeholder="Add any notes about this review..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Mark as Reviewed</button>
                </form>
            </div>
        </div>
        @else
        <div class="card mb-6">
            <div class="card-header">
                <h2 class="text-lg font-semibold">Review Information</h2>
            </div>
            <div class="card-body">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Reviewed By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $fraudScore->reviewer->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Reviewed At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $fraudScore->reviewed_at?->format('M d, Y H:i') ?? 'N/A' }}</dd>
                    </div>
                    @if($fraudScore->admin_notes)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Admin Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $fraudScore->admin_notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex space-x-3">
            <form action="{{ route('admin.fraud-detection.recalculate', $fraudScore->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Recalculate Score
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
