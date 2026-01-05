@extends('layouts.dashboard')

@section('title', 'Transaction Details')
@section('page-title', 'Transaction Details')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Transactions', 'url' => route('organization.transactions.index')],
    ['label' => 'View', 'url' => null]
]])
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('organization.transactions.index') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Transactions
        </a>
    </div>

    <!-- Transaction Header Card -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm opacity-90">Transaction ID</p>
                <h2 class="text-3xl font-bold mt-1">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</h2>
                <p class="text-sm opacity-90 mt-2">{{ $transaction->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm opacity-90">Amount</p>
                <p class="text-3xl font-bold mt-1">
                    {{ $transaction->type == 'payment' ? '+' : '-' }}RM {{ number_format(abs($transaction->amount), 2) }}
                </p>
                <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full
                    {{ $transaction->status == 'completed' ? 'bg-green-400 text-green-900' : '' }}
                    {{ $transaction->status == 'pending' ? 'bg-yellow-400 text-yellow-900' : '' }}
                    {{ $transaction->status == 'failed' ? 'bg-red-400 text-red-900' : '' }}">
                    {{ ucfirst($transaction->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Member Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Member Information
            </h3>
            <div class="flex items-start space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-xl font-bold text-white">{{ strtoupper(substr($transaction->member->name, 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-base font-semibold text-gray-900">{{ $transaction->member->name }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $transaction->member->email }}</p>
                    @if($transaction->member->phone)
                    <p class="text-sm text-gray-500 mt-1">{{ $transaction->member->phone }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Transaction Details Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Transaction Details
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Charges/Plan</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $transaction->charge ? $transaction->charge->title : '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Recurring</span>
                    <span class="text-sm font-semibold text-gray-900">
                        @if($transaction->charge)
                            @if($transaction->charge->is_recurring)
                                {{ $transaction->charge->recurring_months }} {{ $transaction->charge->recurring_months == 1 ? 'month' : 'months' }}
                            @else
                                One-Time
                            @endif
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Status</span>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                        {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $transaction->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $transaction->status == 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Sync Status</span>
                    @if($transaction->synced_to_accounting)
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Synced</span>
                    @else
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Not Synced</span>
                    @endif
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Created Date & Time</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm font-medium text-gray-500">Last Updated Date & Time</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $transaction->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Settlement Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Settlement Details
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Settlement Amount</span>
                    <span class="text-sm font-semibold text-gray-900">RM {{ number_format($transaction->amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Platform Fee</span>
                    <span class="text-sm font-semibold text-red-600">RM {{ number_format($transaction->platform_fee, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm font-medium text-gray-500">Net Amount</span>
                    <span class="text-lg font-bold text-green-600">RM {{ number_format($transaction->amount - $transaction->platform_fee, 2) }}</span>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Settlement Status</span>
                    @if($transaction->settlement_id)
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Settled</span>
                            @if($transaction->settlement && $transaction->settlement->completed_at)
                                <p class="text-xs text-gray-500 mt-1">{{ $transaction->settlement->completed_at->format('d/m/y H:i') }}</p>
                            @endif
                        </div>
                    @else
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                    @endif
                </div>
                @if($transaction->settlement_id && $transaction->settlement)
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm font-medium text-gray-500">Settlement Number</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $transaction->settlement->settlement_number }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($transaction->description)
    <!-- Description Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
            Description
        </h3>
        <p class="text-sm text-gray-700 leading-relaxed">{{ $transaction->description }}</p>
    </div>
    @endif
</div>
@endsection
