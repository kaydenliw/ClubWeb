@extends('layouts.dashboard')

@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Announcements', 'url' => null]
]])
<div class="space-y-4">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manage Announcements</h3>
            <p class="text-sm text-gray-500 mt-1">Create and manage announcements for your members</p>
        </div>
        <a href="{{ route('organization.announcements.create') }}"
           class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            + Add New Announcement
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('organization.announcements.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search announcements..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Apply
            </button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('organization.announcements.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-blue-900">
                <span id="selectedCount">0</span> item(s) selected
            </span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="bulkUpdateStatus('published')" class="px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                    Publish
                </button>
                <button type="button" onclick="bulkUpdateStatus('draft')" class="px-3 py-1.5 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
                    Unpublish
                </button>
                <button type="button" onclick="bulkDelete()" class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                    Delete Selected
                </button>
                <button type="button" onclick="clearSelection()" class="text-sm text-blue-600 hover:text-blue-800">
                    Clear Selection
                </button>
            </div>
        </div>
    </div>

    <!-- Announcements List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="announcementsTable" class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Published Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="announcement-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" value="{{ $announcement->id }}">
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $announcement->title }}</p>
                                <p class="text-xs text-gray-500">{{ Str::limit(strip_tags($announcement->content), 60) }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $announcement->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $announcement->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">
                                {{ $announcement->published_at ? $announcement->published_at->format('M d, Y') : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ $announcement->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="relative" x-data="{ open: false }">
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
                                    <a href="{{ route('organization.announcements.show', $announcement) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                    <a href="{{ route('organization.announcements.edit', $announcement) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form id="delete-announcement-{{ $announcement->id }}" method="POST" action="{{ route('organization.announcements.destroy', $announcement) }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button"
                                            onclick="return confirmDelete('delete-announcement-{{ $announcement->id }}', '{{ addslashes($announcement->title) }}')"
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
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                            No announcements found. Create your first announcement to get started.
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
    $('#announcementsTable').DataTable({
        "pageLength": 10,
        "order": [[4, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 5] }
        ],
        "language": {
            "paginate": {
                "previous": "← Previous",
                "next": "Next →"
            },
            "info": "Showing _START_ to _END_ of _TOTAL_ announcements",
            "infoEmpty": "No announcements available",
            "zeroRecords": "No matching announcements found"
        }
    });

    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.announcement-checkbox').prop('checked', this.checked);
        updateBulkActionsBar();
    });

    // Individual checkbox change
    $('.announcement-checkbox').on('change', function() {
        updateBulkActionsBar();
    });

    function updateBulkActionsBar() {
        const checkedCount = $('.announcement-checkbox:checked').length;
        $('#selectedCount').text(checkedCount);

        if (checkedCount > 0) {
            $('#bulkActionsBar').removeClass('hidden');
        } else {
            $('#bulkActionsBar').addClass('hidden');
        }
    }
});

function clearSelection() {
    $('.announcement-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
    $('#bulkActionsBar').addClass('hidden');
}

function bulkUpdateStatus(status) {
    const selectedIds = $('.announcement-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        showToast('Please select at least one announcement', 'warning');
        return;
    }

    const statusText = status === 'published' ? 'publish' : 'unpublish';
    showConfirmDialog(
        'Confirm Status Update',
        `Are you sure you want to ${statusText} ${selectedIds.length} announcement(s)?`,
        function() {
            $.ajax({
                url: '{{ route("organization.announcements.bulk-update-status") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds,
                    status: status
                },
                success: function(response) {
                    showToast(`Successfully updated ${selectedIds.length} announcement(s)`, 'success');
                    setTimeout(() => location.reload(), 1000);
                },
                error: function(xhr) {
                    showToast('Error updating announcements', 'error');
                }
            });
        },
        'Update Status',
        'blue'
    );
}

function bulkDelete() {
    const selectedIds = $('.announcement-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        showToast('Please select at least one announcement', 'warning');
        return;
    }

    showConfirmDialog(
        'Confirm Bulk Deletion',
        `Are you sure you want to delete ${selectedIds.length} announcement(s)? This action cannot be undone.`,
        function() {
            $.ajax({
                url: '{{ route("organization.announcements.bulk-delete") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds
                },
                success: function(response) {
                    showToast(`Successfully deleted ${selectedIds.length} announcement(s)`, 'success');
                    setTimeout(() => location.reload(), 1000);
                },
                error: function(xhr) {
                    showToast('Error deleting announcements', 'error');
                }
            });
        },
        'Delete',
        'red'
    );
}
</script>
@endpush
