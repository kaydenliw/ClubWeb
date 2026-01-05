@extends('layouts.dashboard')

@section('page-title', 'Settlements Management')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Settlements', 'url' => null]
]])
<div class="space-y-4">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manage Settlements</h3>
            <p class="text-sm text-gray-500 mt-1">View and manage settlements to organizations</p>
        </div>
        <a href="{{ route('admin.settlements.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            + Add Settlement
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="organization" class="flex-1 min-w-[200px] px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Organizations</option>
                @foreach($organizations as $org)
                <option value="{{ $org->id }}" {{ request('organization') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <select name="approval_status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Approval Status</option>
                <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="Start Date"
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="End Date"
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Apply
            </button>
            @if(request()->hasAny(['organization', 'status', 'approval_status', 'start_date', 'end_date']))
            <a href="{{ route('admin.settlements.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-blue-900">
                <span id="selectedCount">0</span> item(s) selected
            </span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="bulkApprove()" class="px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                    Approve Selected
                </button>
                <button type="button" onclick="showBulkRejectModal()" class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                    Reject Selected
                </button>
                <button type="button" onclick="clearSelection()" class="text-sm text-blue-600 hover:text-blue-800">
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Settlements Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="settlementsTable" class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Settlement #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Organization</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Settlement Date</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Approval</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($settlements as $settlement)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="settlement-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" value="{{ $settlement->id }}">
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-medium text-gray-900">{{ $settlement->settlement_number }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-900">{{ $settlement->organization->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-semibold text-green-600">RM {{ number_format($settlement->amount, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($settlement->settlement_date)->format('d M Y') }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $settlement->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($settlement->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($settlement->approval_status == 'pending') bg-yellow-100 text-yellow-700
                                @elseif($settlement->approval_status == 'approved') bg-green-100 text-green-700
                                @elseif($settlement->approval_status == 'rejected') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst($settlement->approval_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="relative inline-block" x-data="{ open: false }">
                                <button @click="open = !open" class="inline-flex items-center justify-center px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition">
                                    Actions
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                    @if($settlement->approval_status == 'pending')
                                    <button onclick="quickApprove({{ $settlement->id }})" class="w-full text-left px-4 py-2.5 text-sm text-green-700 hover:bg-green-50 flex items-center transition">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve
                                    </button>
                                    <button onclick="showRejectModal({{ $settlement->id }})" class="w-full text-left px-4 py-2.5 text-sm text-red-700 hover:bg-red-50 flex items-center transition">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Reject
                                    </button>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    @endif
                                    <a href="{{ route('admin.settlements.show', $settlement) }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center transition">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                    <a href="{{ route('admin.settlements.edit', $settlement) }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center transition">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                            No settlements found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Settlement</h3>
            <textarea id="rejectReason" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500" placeholder="Enter rejection reason..."></textarea>
            <div class="flex justify-end gap-2 mt-4">
                <button onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
                <button onclick="confirmReject()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedIds = [];
let currentRejectId = null;
let isBulkReject = false;

$(document).ready(function() {
    // Initialize DataTables first
    $('#settlementsTable').DataTable({
        "pageLength": 15,
        "order": [[4, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 7] }
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

    // Then attach event listeners
    $('#selectAll').on('change', function() {
        $('.settlement-checkbox').prop('checked', this.checked);
        updateSelection();
    });

    $(document).on('change', '.settlement-checkbox', function() {
        updateSelection();
    });
});

function updateSelection() {
    selectedIds = [];
    $('.settlement-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });
    $('#selectedCount').text(selectedIds.length);
    $('#bulkActionsBar').toggleClass('hidden', selectedIds.length === 0);
}

function clearSelection() {
    $('.settlement-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
    updateSelection();
}

function quickApprove(id) {
    if (!confirm('Approve this settlement?')) return;
    fetch(`/admin/settlements/${id}/approve`, {
        method: 'PUT',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'}
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); });
}

function bulkApprove() {
    if (selectedIds.length === 0) return;
    if (!confirm(`Approve ${selectedIds.length} settlement(s)?`)) return;
    fetch('/admin/settlements/bulk-approve', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'},
        body: JSON.stringify({ ids: selectedIds })
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); });
}

function showRejectModal(id) {
    currentRejectId = id;
    isBulkReject = false;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function showBulkRejectModal() {
    if (selectedIds.length === 0) return;
    isBulkReject = true;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectReason').value = '';
}

function confirmReject() {
    const reason = document.getElementById('rejectReason').value.trim();
    if (!reason) { alert('Please enter a rejection reason'); return; }

    const url = isBulkReject ? '/admin/settlements/bulk-reject' : `/admin/settlements/${currentRejectId}/reject`;
    const method = isBulkReject ? 'POST' : 'PUT';
    const body = isBulkReject ? { ids: selectedIds, reject_reason: reason } : { reject_reason: reason };

    fetch(url, {
        method: method,
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'},
        body: JSON.stringify(body)
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); });
}
</script>
@endpush

@endsection
