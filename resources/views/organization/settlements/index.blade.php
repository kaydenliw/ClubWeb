@extends('layouts.dashboard')

@section('title', 'Settlements')
@section('page-title', 'Settlements')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Settlements', 'url' => null]
]])
<div class="space-y-4">
    <!-- Header with Bank Details Button -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Settlement History</h3>
            <p class="text-sm text-gray-500 mt-1">View your settlement history and manage bank details</p>
        </div>
        <a href="{{ route('organization.settlements.edit-bank') }}"
           class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            Edit Bank Details
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Total Settled</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">RM {{ number_format($stats['total_settled'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Completed settlements</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Pending Settlement</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">RM {{ number_format($stats['pending_settlement'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Awaiting processing</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Total Settlements</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['total_settlements'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">All time</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('organization.settlements.index') }}" class="flex flex-wrap items-center gap-3">
            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From Date" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To Date" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Apply
            </button>
            @if(request()->hasAny(['status', 'date_from', 'date_to']))
            <a href="{{ route('organization.settlements.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Settlements List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="settlementsTable" class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Settlement ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Settlement Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($settlements as $settlement)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <span class="text-sm font-medium text-gray-900">#{{ $settlement->id }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-semibold text-gray-900">RM {{ number_format($settlement->amount, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $settlement->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($settlement->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($settlement->settlement_date)->format('M d, Y') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('organization.settlements.show', $settlement) }}"
                               class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                            No settlements found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#settlementsTable').DataTable({
        "pageLength": 15,
        "order": [[3, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": 4 }
        ],
        "language": {
            "paginate": {
                "previous": "← Previous",
                "next": "Next →"
            },
            "info": "Showing _START_ to _END_ of _TOTAL_ settlements",
            "infoEmpty": "No settlements available",
            "zeroRecords": "No matching settlements found"
        }
    });
});
</script>
@endpush
