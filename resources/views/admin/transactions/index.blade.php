@extends('layouts.dashboard')

@section('page-title', 'Transactions Management')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Transactions', 'url' => null]
]])
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-start justify-between gap-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manage Transactions</h3>
            <p class="text-sm text-gray-500 mt-1">View and sync transactions across organizations</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
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
                    <a href="{{ route('admin.transactions.export.csv') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export as CSV
                    </a>
                    <a href="{{ route('admin.transactions.export.excel') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Export as Excel
                    </a>
                    <a href="{{ route('admin.transactions.export.pdf') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Export as PDF
                    </a>
                </div>
            </div>
            <button type="button" onclick="syncSelected()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Sync to Accounting
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by member name..."
                       value="{{ request('search') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select name="organization" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Organizations</option>
                @foreach($organizations as $org)
                <option value="{{ $org->id }}" {{ request('organization') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                @endforeach
            </select>
            <select name="type" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Types</option>
                <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Payment</option>
                <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>
            </select>
            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="Start Date"
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="End Date"
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Apply
            </button>
            @if(request()->hasAny(['search', 'organization', 'type', 'status', 'start_date', 'end_date']))
            <a href="{{ route('admin.transactions.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-blue-900">
                <span id="selectedCount">0</span> transaction(s) selected
            </span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="markAsSettled()" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition">
                    Mark as Settled
                </button>
                <button type="button" onclick="clearSelection()" class="text-sm text-blue-600 hover:text-blue-800">
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="transactionsTable" class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Organization</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Sync Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $txn)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="transaction-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                   value="{{ $txn->id }}" {{ $txn->synced_to_accounting ? 'disabled' : '' }}>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $txn->member->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $txn->member->email ?? 'Member not found' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-900">{{ $txn->organization->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-semibold {{ $txn->type == 'payment' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $txn->type == 'payment' ? '+' : '-' }}RM {{ number_format(abs($txn->amount), 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $txn->type == 'payment' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($txn->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $txn->status == 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $txn->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $txn->status == 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($txn->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($txn->synced_to_accounting)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Synced</span>
                                @if($txn->synced_at)
                                <div class="text-xs text-gray-500 mt-1">{{ $txn->synced_at->format('d M Y') }}</div>
                                @endif
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Not Synced</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ $txn->created_at->format('d M Y') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="relative inline-block" x-data="{ open: false }">
                                <button @click="open = !open" class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                    </svg>
                                </button>
                                <div x-show="open"
                                     x-cloak
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                    <a href="{{ route('admin.transactions.show', $txn) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                    @if($txn->status == 'completed' && !$txn->settlement_id)
                                    <button type="button"
                                            onclick="markSingleAsSettled({{ $txn->id }})"
                                            class="flex items-center w-full px-4 py-2 text-sm text-purple-600 hover:bg-purple-50 transition">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Mark as Settled
                                    </button>
                                    @endif
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form id="delete-transaction-{{ $txn->id }}" action="{{ route('admin.transactions.destroy', $txn) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button"
                                            onclick="return confirmDelete('delete-transaction-{{ $txn->id }}', 'Transaction #{{ $txn->id }}')"
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">
                            No transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Settlement Preview Modal -->
    <div id="settlementModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Settlement Preview</h3>
                <div id="settlementContent" class="space-y-4">
                    <!-- Content will be loaded here -->
                </div>
                <div class="flex gap-3 mt-6">
                    <button onclick="closeSettlementModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button onclick="confirmSettlement()" class="flex-1 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition">
                        Confirm Settlement
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedTransactionIds = [];

$(document).ready(function() {
    $('#transactionsTable').DataTable({
        "pageLength": 15,
        "order": [[7, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 8] }
        ],
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

    $('#selectAll').on('change', function() {
        $('.transaction-checkbox:not(:disabled)').prop('checked', this.checked);
        updateBulkActions();
    });

    $(document).on('change', '.transaction-checkbox', function() {
        updateBulkActions();
    });
});

function updateBulkActions() {
    const selectedCount = $('.transaction-checkbox:checked').length;
    $('#selectedCount').text(selectedCount);
    $('#bulkActionsBar').toggleClass('hidden', selectedCount === 0);
}

function clearSelection() {
    $('.transaction-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
    updateBulkActions();
}

function syncSelected() {
    const selectedIds = $('.transaction-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        showToast('Please select at least one transaction to sync', 'warning');
        return;
    }

    showConfirmDialog(
        'Confirm Sync to Accounting',
        `Are you sure you want to sync ${selectedIds.length} transaction(s) to the accounting system? This action will update your accounting records.`,
        function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.transactions.sync") }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'transaction_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        },
        'Sync Now',
        'blue'
    );
}

function markAsSettled() {
    selectedTransactionIds = $('.transaction-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedTransactionIds.length === 0) {
        showToast('Please select at least one transaction', 'warning');
        return;
    }

    showLoading();

    $.ajax({
        url: '{{ route("admin.transactions.settlement-preview") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            transaction_ids: selectedTransactionIds
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                showSettlementModal(response.data);
            }
        },
        error: function(xhr) {
            hideLoading();
            const error = xhr.responseJSON?.error || 'Failed to load settlement preview';
            showToast(error, 'error');
        }
    });
}

function markSingleAsSettled(transactionId) {
    selectedTransactionIds = [transactionId];
    showLoading();

    $.ajax({
        url: '{{ route("admin.transactions.settlement-preview") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            transaction_ids: [transactionId]
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                showSettlementModal(response.data);
            }
        },
        error: function(xhr) {
            hideLoading();
            const error = xhr.responseJSON?.error || 'Failed to load settlement preview';
            showToast(error, 'error');
        }
    });
}

function showSettlementModal(data) {
    const content = `
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <h4 class="font-semibold text-blue-900 mb-2">Organization Details</h4>
            <p class="text-sm text-blue-800"><strong>Name:</strong> ${data.organization.name}</p>
            <p class="text-sm text-blue-800"><strong>Bank:</strong> ${data.organization.bank_name}</p>
            <p class="text-sm text-blue-800"><strong>Account Number:</strong> ${data.organization.bank_account_number}</p>
            <p class="text-sm text-blue-800"><strong>Account Holder:</strong> ${data.organization.bank_account_holder}</p>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="font-semibold text-gray-900 mb-3">Settlement Summary</h4>
            <div class="space-y-2">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-sm text-gray-600">Transactions Count:</span>
                    <span class="text-sm font-semibold text-gray-900">${data.transactions_count}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-sm text-gray-600">Total Amount:</span>
                    <span class="text-sm font-semibold text-gray-900">RM ${data.total_amount}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-sm text-gray-600">Platform Fee:</span>
                    <span class="text-sm font-semibold text-red-600">- RM ${data.total_platform_fee}</span>
                </div>
                <div class="flex justify-between py-3 bg-green-50 px-3 rounded-lg mt-2">
                    <span class="text-base font-semibold text-green-900">Net Settlement Amount:</span>
                    <span class="text-lg font-bold text-green-600">RM ${data.net_amount}</span>
                </div>
            </div>
        </div>
    `;

    document.getElementById('settlementContent').innerHTML = content;
    document.getElementById('settlementModal').classList.remove('hidden');
}

function closeSettlementModal() {
    document.getElementById('settlementModal').classList.add('hidden');
    selectedTransactionIds = [];
}

function confirmSettlement() {
    closeSettlementModal();
    showLoading();

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.transactions.mark-as-settled") }}';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    selectedTransactionIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'transaction_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush