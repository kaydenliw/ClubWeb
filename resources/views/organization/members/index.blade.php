@extends('layouts.dashboard')

@section('page-title', 'Members')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Members', 'url' => null]
]])
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manage Members</h3>
            <p class="text-sm text-gray-500 mt-1">Manage your organization members</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Export Dropdown -->
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
                    <a href="{{ route('organization.members.export') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export as CSV
                    </a>
                    <a href="{{ route('organization.members.export.excel') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Export as Excel
                    </a>
                    <a href="{{ route('organization.members.export.pdf') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Export as PDF
                    </a>
                </div>
            </div>
            <a href="{{ route('organization.members.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                + Add Member
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-4 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[250px]">
                <input type="text" name="search" placeholder="Search by name, email, phone, or car plate..."
                       value="{{ request('search') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Apply
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('organization.members.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="hidden bg-blue-50 border-b border-blue-200 px-6 py-3">
            <div class="flex items-center justify-between">
                <span class="text-sm text-blue-700">
                    <span id="selectedCount">0</span> member(s) selected
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="bulkUpdateStatus('active')" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                        Set Active
                    </button>
                    <button type="button" onclick="bulkUpdateStatus('inactive')" class="px-3 py-1.5 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition">
                        Set Inactive
                    </button>
                    <button type="button" onclick="bulkDelete()" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                        Delete Selected
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="membersTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Car Details</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Charges</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($members as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="member-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" value="{{ $member->id }}">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-600 flex items-center justify-center mr-3">
                                    <span class="text-sm font-bold text-white">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                    <div class="text-xs text-gray-500">Joined {{ $member->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $member->email }}</div>
                            <div class="text-xs text-gray-500">{{ $member->phone ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $member->car_brand ?? '-' }} {{ $member->car_model ?? '' }}</div>
                            <div class="text-xs text-gray-500">{{ $member->car_plate ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-semibold text-gray-900">{{ $member->charges_count }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 text-xs font-medium {{ $member->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($member->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="relative inline-block" x-data="{ open: false }">
                                <button @click="open = !open" class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                    </svg>
                                </button>
                                <div x-show="open"
                                     x-cloak
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                    <a href="{{ route('organization.members.show', $member) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                    <a href="{{ route('organization.members.edit', $member) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form id="delete-member-{{ $member->id }}" action="{{ route('organization.members.destroy', $member) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button"
                                            onclick="return confirmDelete('delete-member-{{ $member->id }}', '{{ addslashes($member->name) }}')"
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
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p>No members found</p>
                            </div>
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
    $('#membersTable').DataTable({
        "pageLength": 15,
        "order": [[1, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 6] }
        ],
        "language": {
            "paginate": {
                "previous": "← Previous",
                "next": "Next →"
            },
            "info": "Showing _START_ to _END_ of _TOTAL_ members",
            "infoEmpty": "No members available",
            "zeroRecords": "No matching members found"
        }
    });

    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.member-checkbox').prop('checked', this.checked);
        updateBulkActionsBar();
    });

    // Individual checkbox change
    $('.member-checkbox').on('change', function() {
        updateBulkActionsBar();
    });

    function updateBulkActionsBar() {
        const checkedCount = $('.member-checkbox:checked').length;
        $('#selectedCount').text(checkedCount);

        if (checkedCount > 0) {
            $('#bulkActionsBar').removeClass('hidden');
        } else {
            $('#bulkActionsBar').addClass('hidden');
        }
    }
});

// Bulk action functions
function bulkUpdateStatus(status) {
    const selectedIds = $('.member-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        showToast('Please select at least one member', 'warning');
        return;
    }

    const statusText = status === 'active' ? 'activate' : 'deactivate';
    showConfirmDialog(
        'Confirm Status Update',
        `Are you sure you want to ${statusText} ${selectedIds.length} member(s)?`,
        function() {
            $.ajax({
                url: '{{ route("organization.members.bulk-update-status") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds,
                    status: status
                },
                success: function(response) {
                    showToast(`Successfully updated ${selectedIds.length} member(s)`, 'success');
                    setTimeout(() => location.reload(), 1000);
                },
                error: function(xhr) {
                    showToast('Error updating members', 'error');
                }
            });
        },
        'Update Status',
        'blue'
    );
}

function bulkDelete() {
    const selectedIds = $('.member-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        showToast('Please select at least one member', 'warning');
        return;
    }

    showConfirmDialog(
        'Confirm Bulk Deletion',
        `Are you sure you want to delete ${selectedIds.length} member(s)? This action cannot be undone.`,
        function() {
            $.ajax({
                url: '{{ route("organization.members.bulk-delete") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds
                },
                success: function(response) {
                    showToast(`Successfully deleted ${selectedIds.length} member(s)`, 'success');
                    setTimeout(() => location.reload(), 1000);
                },
                error: function(xhr) {
                    showToast('Error deleting members', 'error');
                }
            });
        },
        'Delete',
        'red'
    );
}
</script>
@endpush
