@extends('layouts.dashboard')

@section('page-title', 'Transaction Details')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Transactions', 'url' => route('admin.transactions.index')],
    ['label' => 'View', 'url' => null]
]])
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Transaction #{{ $transaction->transaction_number ?? $transaction->id }}</h1>
            <p class="text-sm text-gray-500 mt-1">Transaction Details</p>
        </div>
        <div class="flex space-x-3">
            <form id="delete-transaction-form" action="{{ route('admin.transactions.destroy', $transaction) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="button" onclick="return confirmDelete('delete-transaction-form', 'Transaction #{{ $transaction->transaction_number ?? $transaction->id }}')" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                    Delete Transaction
                </button>
            </form>
            <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Transaction Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Transaction Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->transaction_number ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Amount</label>
                            <p class="mt-1 text-lg font-semibold {{ $transaction->type == 'payment' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type == 'payment' ? '+' : '-' }}RM {{ number_format(abs($transaction->amount), 2) }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Type</label>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->type == 'payment' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</label>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $transaction->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $transaction->status == 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Payment Method</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->payment_method ? ucfirst($transaction->payment_method) : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Transaction Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>

                    @if($transaction->notes)
                    <div class="mt-6">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Notes</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $transaction->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Member Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Member Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->member->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->member->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->member->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Member Status</label>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->member->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($transaction->member->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.members.show', $transaction->member) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            View Member Details →
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Organization Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Organization Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->organization->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->organization->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->organization->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</label>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->organization->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($transaction->organization->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.organizations.show', $transaction->organization) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            View Organization Details →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Sync Status</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Accounting Sync</label>
                            <p class="mt-1">
                                @if($transaction->synced_to_accounting)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Synced</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Not Synced</span>
                                @endif
                            </p>
                        </div>
                        @if($transaction->synced_at)
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Synced At</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->synced_at->format('d M Y, h:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($transaction->charge)
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Related Charge</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Charge Title</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->charge->title }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Charge Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($transaction->charge->type) }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Charge Amount</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900">RM {{ number_format($transaction->charge->amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Created</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $transaction->updated_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
