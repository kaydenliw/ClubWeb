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
    <div class="flex justify-between items-center">
        <a href="{{ route('organization.settlements.index') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Settlements
        </a>

        @if($settlement->status == 'completed')
        <a href="{{ route('organization.settlements.receipt', $settlement) }}"
           target="_blank"
           class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download Official Receipt
        </a>
        @endif
    </div>

    <!-- Settlement Header Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <p class="text-sm text-gray-500">Settlement Number</p>
                <h2 class="text-2xl font-bold text-gray-900 mt-1">{{ $settlement->settlement_number }}</h2>
                <p class="text-sm text-gray-500 mt-2">
                    {{ $settlement->completed_at ? $settlement->completed_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($settlement->settlement_date)->format('d/m/Y') }}
                </p>
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                {{ $settlement->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ ucfirst($settlement->status) }}
            </span>
        </div>

        <!-- Settlement Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
            <div>
                <p class="text-xs text-gray-500 uppercase">Total Amount</p>
                <p class="text-xl font-bold text-gray-900 mt-1">RM {{ number_format($totalAmount, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Platform Fee</p>
                <p class="text-xl font-bold text-red-600 mt-1">RM {{ number_format($totalPlatformFee, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Net Settlement</p>
                <p class="text-xl font-bold text-green-600 mt-1">RM {{ number_format($netAmount, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Transactions Summary Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Transactions Included</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $settlement->transactions->count() }} transaction(s) in this settlement</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Transaction ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Charge/Plan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Platform Fee</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Net Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($settlement->transactions as $txn)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="text-sm font-medium text-gray-900">#{{ str_pad($txn->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-900">{{ $txn->charge ? $txn->charge->title : '-' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-semibold text-gray-900">RM {{ number_format($txn->amount, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-red-600">RM {{ number_format($txn->platform_fee, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-semibold text-green-600">RM {{ number_format($txn->amount - $txn->platform_fee, 2) }}</span>
                        </td>
                    </tr>
                    @endforeach

                    <!-- TOTAL Row -->
                    <tr class="bg-blue-50 font-semibold border-t-2 border-blue-200">
                        <td colspan="2" class="px-4 py-3 text-right text-sm text-gray-900">TOTAL:</td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-bold text-gray-900">RM {{ number_format($totalAmount, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-bold text-red-600">RM {{ number_format($totalPlatformFee, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-bold text-green-600">RM {{ number_format($netAmount, 2) }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Proof of Receipt Section -->
    @if($settlement->status == 'completed' && $settlement->proof_of_receipt)
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Proof of Receipt</h3>
        <div class="flex items-center justify-center bg-gray-50 rounded-lg p-4">
            <img src="{{ asset('storage/' . $settlement->proof_of_receipt) }}"
                 alt="Proof of Receipt"
                 class="max-w-full h-auto rounded-lg shadow-md">
        </div>
    </div>
    @endif
</div>
@endsection
