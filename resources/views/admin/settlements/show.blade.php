@extends('layouts.dashboard')

@section('page-title', 'Settlement Details')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Settlements', 'url' => route('admin.settlements.index')],
    ['label' => 'View', 'url' => null]
]])
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content (Left - 2 columns) -->
    <div class="lg:col-span-2 space-y-4">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.settlements.index') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Settlements List
        </a>
    </div>

    <!-- Settlement Details Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Settlement Information</h3>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $settlement->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ ucfirst($settlement->status) }}
                </span>
                <span class="px-2 py-1 text-xs font-medium rounded-full
                    @if($settlement->approval_status == 'pending') bg-yellow-100 text-yellow-700
                    @elseif($settlement->approval_status == 'approved') bg-green-100 text-green-700
                    @elseif($settlement->approval_status == 'rejected') bg-red-100 text-red-700
                    @else bg-gray-100 text-gray-700
                    @endif">
                    {{ ucfirst($settlement->approval_status) }}
                </span>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Settlement Number</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $settlement->settlement_number }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</label>
                    <p class="mt-1">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $settlement->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($settlement->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Organization</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $settlement->organization->name }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Amount</label>
                    <p class="mt-1 text-sm font-semibold text-green-600">RM {{ number_format($settlement->amount, 2) }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Settlement Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($settlement->settlement_date)->format('d M Y') }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Created Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $settlement->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <div class="col-span-2">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Notes</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $settlement->notes ?? '-' }}</p>
                </div>
            </div>

            <!-- Rejection Reason (if rejected) -->
            @if($settlement->approval_status == 'rejected' && $settlement->reject_reason)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
                <p class="text-sm font-medium text-red-800 mb-1">Rejection Reason:</p>
                <p class="text-sm text-red-700">{{ $settlement->reject_reason }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Organization Bank Details</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bank Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $settlement->organization->bank_name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Account Number</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $settlement->organization->bank_account_number ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Account Holder</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $settlement->organization->bank_account_holder ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Right Sidebar (1 column) -->
    <div class="lg:col-span-1 space-y-4">
        <!-- Approval Actions (only for pending settlements) -->
        @if($settlement->approval_status == 'pending')
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 lg:sticky lg:top-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Approval Actions</h3>

            <div class="space-y-4">
                <!-- Approve -->
                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                    <h4 class="text-sm font-semibold text-green-900 mb-2">Approve Settlement</h4>
                    <p class="text-xs text-green-700 mb-4">This settlement will be approved for processing.</p>
                    <form method="POST" action="{{ route('admin.settlements.approve', $settlement) }}" onsubmit="return confirm('Are you sure you want to approve this settlement?')">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                                class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                            Approve Settlement
                        </button>
                    </form>
                </div>

                <!-- Reject -->
                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                    <h4 class="text-sm font-semibold text-red-900 mb-2">Reject Settlement</h4>
                    <p class="text-xs text-red-700 mb-4">Provide a reason for rejection.</p>
                    <form method="POST" action="{{ route('admin.settlements.reject', $settlement) }}" id="rejectForm">
                        @csrf
                        @method('PUT')
                        <textarea name="reject_reason"
                                  id="reject_reason"
                                  rows="3"
                                  required
                                  placeholder="Enter rejection reason..."
                                  class="w-full px-3 py-2 text-sm border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 mb-3"></textarea>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                            Reject Settlement
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
