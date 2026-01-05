@extends('layouts.dashboard')

@section('title', 'Bank Details Approval')
@section('page-title', 'Bank Details Approval')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Bank Details', 'url' => null]
]])
<div class="space-y-4">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Pending Bank Details</h3>
            <p class="text-sm text-gray-500 mt-1">Review and approve organization bank details</p>
        </div>
        @if($pendingCount > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2">
            <p class="text-sm font-semibold text-yellow-900">{{ $pendingCount }} Pending</p>
        </div>
        @endif
    </div>

    <!-- Organizations List -->
    <div class="space-y-4">
        @forelse($organizations as $org)
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900">{{ $org->name }}</h4>
                    <p class="text-sm text-gray-500">{{ $org->email }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">
                    Pending Approval
                </span>
            </div>

            <!-- Bank Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Bank Name</p>
                    <p class="text-sm text-gray-900 mt-1">{{ $org->pending_bank_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Account Holder</p>
                    <p class="text-sm text-gray-900 mt-1">{{ $org->pending_bank_account_holder }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Account Number</p>
                    <p class="text-sm text-gray-900 mt-1">{{ $org->pending_bank_account_number }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <form method="POST" action="{{ route('admin.bank-details.approve', $org) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" onclick="return confirm('Approve these bank details?')"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                        Approve
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.bank-details.reject', $org) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" onclick="return confirm('Reject these bank details?')"
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                        Reject
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 text-center">
            <p class="text-sm text-gray-500">No pending bank details.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
