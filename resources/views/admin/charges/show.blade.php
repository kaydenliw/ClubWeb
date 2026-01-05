@extends('layouts.dashboard')

@section('title', 'Charge Details')
@section('page-title', 'Charge Details & Approval')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Charges', 'url' => route('admin.charges.index')],
    ['label' => 'Details', 'url' => null]
]])
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content (Left - 2 columns) -->
    <div class="lg:col-span-2 space-y-4">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.charges.index') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Charges List
        </a>
    </div>

    <!-- Charge Details Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-start space-x-4 flex-1">
                @if($charge->image)
                <img src="{{ asset('storage/' . $charge->image) }}"
                     alt="{{ $charge->title }}"
                     class="w-24 h-24 rounded-lg object-cover border border-gray-200">
                @else
                <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl font-bold text-gray-600">{{ strtoupper(substr($charge->title, 0, 2)) }}</span>
                </div>
                @endif
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-900">{{ $charge->title }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $charge->organization->name }}</p>
                    <div class="flex items-center space-x-3 mt-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            @if($charge->approval_status == 'draft') bg-gray-100 text-gray-700
                            @elseif($charge->approval_status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($charge->approval_status == 'approved') bg-green-100 text-green-700
                            @elseif($charge->approval_status == 'rejected') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($charge->approval_status) }}
                        </span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $charge->is_recurring ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            @if($charge->is_recurring)
                                Recurring ({{ $charge->recurring_months }} {{ $charge->recurring_months == 1 ? 'month' : 'months' }})
                            @else
                                One-Time
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Amount</p>
                <p class="text-3xl font-bold text-gray-900">RM {{ number_format($charge->amount, 2) }}</p>
            </div>
        </div>

        <!-- Description -->
        <div class="border-t border-gray-200 pt-4 mb-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-2">Description</h4>
            <div class="text-sm text-gray-700 prose max-w-none">{!! $charge->description !!}</div>
        </div>

        <!-- Charge Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-200 pt-4">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Organization</p>
                <p class="text-sm text-gray-900 mt-1">{{ $charge->organization->name }}</p>
                <p class="text-xs text-gray-500">{{ $charge->organization->email }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Created Date</p>
                <p class="text-sm text-gray-900 mt-1">{{ $charge->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Scheduled Date</p>
                <p class="text-sm text-gray-900 mt-1">
                    @if($charge->scheduled_at)
                        {{ $charge->scheduled_at->format('d M Y, h:i A') }}
                    @else
                        <span class="text-gray-400">Not scheduled</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Last Updated</p>
                <p class="text-sm text-gray-900 mt-1">{{ $charge->updated_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>

        <!-- Rejection Reason (if rejected) -->
        @if($charge->approval_status == 'rejected' && $charge->reject_reason)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
            <p class="text-sm font-medium text-red-800 mb-1">Rejection Reason:</p>
            <p class="text-sm text-red-700">{{ $charge->reject_reason }}</p>
        </div>
        @endif
    </div>

    <!-- Platform Fee Management -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Platform Fee Configuration</h3>

        <form method="POST" action="{{ route('admin.charges.update-fee', $charge) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="platform_fee_percentage" class="block text-sm font-medium text-gray-700 mb-1">
                        Percentage Fee (%)
                    </label>
                    <input type="number"
                           name="platform_fee_percentage"
                           id="platform_fee_percentage"
                           value="{{ old('platform_fee_percentage', $charge->platform_fee_percentage) }}"
                           step="0.01"
                           min="0"
                           max="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="platform_fee_operator" class="block text-sm font-medium text-gray-700 mb-1">
                        Operator
                    </label>
                    <select name="platform_fee_operator" id="platform_fee_operator"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="and" {{ old('platform_fee_operator', $charge->platform_fee_operator) == 'and' ? 'selected' : '' }}>AND (+)</option>
                        <option value="or" {{ old('platform_fee_operator', $charge->platform_fee_operator) == 'or' ? 'selected' : '' }}>OR</option>
                    </select>
                </div>

                <div>
                    <label for="platform_fee_fixed" class="block text-sm font-medium text-gray-700 mb-1">
                        Fixed Fee (RM)
                    </label>
                    <input type="number"
                           name="platform_fee_fixed"
                           id="platform_fee_fixed"
                           value="{{ old('platform_fee_fixed', $charge->platform_fee_fixed) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Update Platform Fee
                </button>
            </div>
        </form>
    </div>
    </div>

    <!-- Right Sidebar (1 column) -->
    <div class="lg:col-span-1 space-y-4">
        <!-- Approval Actions (only for pending charges) -->
        @if($charge->approval_status == 'pending')
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 lg:sticky lg:top-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Approval Actions</h3>

            <div class="space-y-4">
                <!-- Approve -->
                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                    <h4 class="text-sm font-semibold text-green-900 mb-2">Approve Charge</h4>
                    <p class="text-xs text-green-700 mb-4">This charge will be approved and can be activated by the organization.</p>
                    <form method="POST" action="{{ route('admin.charges.approve', $charge) }}" onsubmit="return confirm('Are you sure you want to approve this charge?')">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                                class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                            Approve Charge
                        </button>
                    </form>
                </div>

                <!-- Reject -->
                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                    <h4 class="text-sm font-semibold text-red-900 mb-2">Reject Charge</h4>
                    <p class="text-xs text-red-700 mb-4">Provide a reason for rejection. The organization will be notified.</p>
                    <form method="POST" action="{{ route('admin.charges.reject', $charge) }}" id="rejectForm">
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
                            Reject Charge
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
    const reason = document.getElementById('reject_reason').value.trim();
    if (!reason) {
        e.preventDefault();
        alert('Please provide a rejection reason.');
        return false;
    }
    return confirm('Are you sure you want to reject this charge? The organization will be notified.');
});
</script>
@endpush
