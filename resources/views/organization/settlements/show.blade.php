@extends('layouts.dashboard')

@section('title', 'Settlement Details')
@section('page-title', 'Settlement Details')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Settlements', 'url' => route('organization.settlements.index')],
    ['label' => 'View', 'url' => null]
]])
<div class="space-y-4">
    <!-- Back Button -->
    <div>
        <a href="{{ route('organization.settlements.index') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Settlements
        </a>
    </div>

    <!-- Settlement Details Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Settlement #{{ $settlement->id }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($settlement->settlement_date)->format('F d, Y') }}</p>
            </div>
            <span class="px-3 py-1 text-sm font-medium rounded-full
                {{ $settlement->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ ucfirst($settlement->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Settlement Information -->
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Settlement Information</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Amount:</span>
                        <span class="text-sm font-semibold text-gray-900">RM {{ number_format($settlement->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Status:</span>
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                            {{ $settlement->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($settlement->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Settlement Date:</span>
                        <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($settlement->settlement_date)->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Bank Information -->
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Bank Information</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Bank Name:</span>
                        <span class="text-sm text-gray-900">{{ $settlement->organization->bank_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Account Name:</span>
                        <span class="text-sm text-gray-900">{{ $settlement->organization->bank_account_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Account Number:</span>
                        <span class="text-sm text-gray-900">{{ $settlement->organization->bank_account_number ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
