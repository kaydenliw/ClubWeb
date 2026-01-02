@extends('layouts.dashboard')

@section('page-title', 'Settlement Details')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Settlements', 'url' => route('admin.settlements.index')],
    ['label' => 'View', 'url' => null]
]])
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Settlement Details</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $settlement->settlement_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.settlements.edit', $settlement) }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                Edit Settlement
            </a>
            <form id="delete-settlement-{{ $settlement->id }}" action="{{ route('admin.settlements.destroy', $settlement) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
            <button type="button"
                    onclick="return confirmDelete('delete-settlement-{{ $settlement->id }}', '{{ addslashes($settlement->settlement_number) }}')"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                Delete Settlement
            </button>
            <a href="{{ route('admin.settlements.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Settlement Information</h3>
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
@endsection
