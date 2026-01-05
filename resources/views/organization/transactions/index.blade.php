@extends('layouts.dashboard')

@section('title', 'Transactions')
@section('page-title', 'Transactions')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Transactions', 'url' => null]
]])
<div class="space-y-4">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manage Transactions</h3>
            <p class="text-sm text-gray-500 mt-1">View and track all payment transactions</p>
        </div>
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open" x-cloak @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                <a href="{{ route('organization.transactions.export.csv') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export as CSV
                </a>
                <a href="{{ route('organization.transactions.export.excel') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Export as Excel
                </a>
                <a href="{{ route('organization.transactions.export.pdf') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Export as PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('organization.transactions.index') }}" class="space-y-3">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by member..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <select name="charge_id" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Charges/Plans</option>
                    @foreach($charges as $charge)
                        <option value="{{ $charge->id }}" {{ request('charge_id') == $charge->id ? 'selected' : '' }}>
                            {{ $charge->title }}
                        </option>
                    @endforeach
                </select>
                <select name="recurring" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Recurring</option>
                    <option value="one-time" {{ request('recurring') == 'one-time' ? 'selected' : '' }}>One-Time</option>
                    <option value="1" {{ request('recurring') == '1' ? 'selected' : '' }}>Monthly (1 month)</option>
                    <option value="2" {{ request('recurring') == '2' ? 'selected' : '' }}>Bi-Monthly (2 months)</option>
                    <option value="3" {{ request('recurring') == '3' ? 'selected' : '' }}>Quarterly (3 months)</option>
                    <option value="6" {{ request('recurring') == '6' ? 'selected' : '' }}>Semi-Annually (6 months)</option>
                    <option value="12" {{ request('recurring') == '12' ? 'selected' : '' }}>Annually (12 months)</option>
                </select>
                <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-600">Created:</label>
                    <input type="date" name="created_from" value="{{ request('created_from') }}"
                           class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <span class="text-xs text-gray-500">to</span>
                    <input type="date" name="created_to" value="{{ request('created_to') }}"
                           class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-600">Updated:</label>
                    <input type="date" name="updated_from" value="{{ request('updated_from') }}"
                           class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <span class="text-xs text-gray-500">to</span>
                    <input type="date" name="updated_to" value="{{ request('updated_to') }}"
                           class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Apply
                </button>
                @if(request()->hasAny(['search', 'charge_id', 'recurring', 'status', 'created_from', 'created_to', 'updated_from', 'updated_to']))
                <a href="{{ route('organization.transactions.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Transactions List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="transactionsTable" class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Charges/Plan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Recurring</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Platform Fee</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Settlement</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sync Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Last Updated</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $txn)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <span class="text-xs font-bold text-white">{{ strtoupper(substr($txn->member->name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $txn->member->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $txn->member->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-900">{{ $txn->charge ? $txn->charge->title : '-' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-900">
                                @if($txn->charge)
                                    @if($txn->charge->is_recurring)
                                        {{ $txn->charge->recurring_months }} {{ $txn->charge->recurring_months == 1 ? 'month' : 'months' }}
                                    @else
                                        One-Time
                                    @endif
                                @else
                                    -
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-semibold text-gray-900">RM {{ number_format($txn->amount, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-900">RM {{ number_format($txn->platform_fee, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $txn->status == 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $txn->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $txn->status == 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($txn->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($txn->settlement_id)
                                <div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Settled</span>
                                    <p class="text-xs text-gray-500 mt-1">{{ $txn->settlement->completed_at ? $txn->settlement->completed_at->format('d/m/y H:i') : '-' }}</p>
                                </div>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($txn->synced_to_accounting)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Synced</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">Not Synced</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ $txn->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ $txn->updated_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('organization.transactions.show', $txn) }}"
                               class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-sm text-gray-500">
                            No transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                <!-- TOTAL Row in tfoot -->
                @if($transactions->count() > 0)
                <tfoot class="bg-blue-50 font-semibold border-t-2 border-gray-300">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right text-sm text-gray-900">TOTAL:</td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-bold text-gray-900">RM {{ number_format($totals->total_amount ?? 0, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-bold text-gray-900">RM {{ number_format($totals->total_platform_fee ?? 0, 2) }}</span>
                        </td>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#transactionsTable').DataTable({
        "pageLength": 15,
        "order": [[8, "desc"]],
        "language": {
            "paginate": {
                "previous": "← Previous",
                "next": "Next →"
            },
            "info": "Showing _START_ to _END_ of _TOTAL_ transactions",
            "infoEmpty": "No transactions available",
            "zeroRecords": "No matching transactions found"
        }
    });
});
</script>
@endpush
