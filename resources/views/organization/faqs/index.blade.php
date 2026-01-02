@extends('layouts.dashboard')

@section('title', 'FAQs')
@section('page-title', 'FAQs')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'FAQs', 'url' => null]
]])
<div class="space-y-4">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manage FAQs</h3>
            <p class="text-sm text-gray-500 mt-1">Create and manage frequently asked questions</p>
        </div>
        <a href="{{ route('organization.faqs.create') }}"
           class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            + Add New FAQ
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('organization.faqs.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search FAQs..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select name="category" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Categories</option>
                <option value="General" {{ request('category') == 'General' ? 'selected' : '' }}>General</option>
                <option value="Membership" {{ request('category') == 'Membership' ? 'selected' : '' }}>Membership</option>
                <option value="Payment" {{ request('category') == 'Payment' ? 'selected' : '' }}>Payment</option>
                <option value="Events" {{ request('category') == 'Events' ? 'selected' : '' }}>Events</option>
                <option value="Technical" {{ request('category') == 'Technical' ? 'selected' : '' }}>Technical</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Apply
            </button>
            @if(request('search') || request('category'))
            <a href="{{ route('organization.faqs.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
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
                <button type="button" onclick="bulkDelete()" class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                    Delete Selected
                </button>
                <button type="button" onclick="clearSelection()" class="text-sm text-blue-600 hover:text-blue-800">
                    Clear Selection
                </button>
            </div>
        </div>
    </div>

    <!-- FAQs List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="faqsTable" class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Question</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Answer</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($faqs as $faq)
                    <tr class="hover:bg-gray-50 transition" data-id="{{ $faq->id }}">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="faq-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" value="{{ $faq->id }}">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 text-gray-400 drag-handle cursor-move" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">{{ $faq->order }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ $faq->question }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($faq->category)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                {{ $faq->category }}
                            </span>
                            @else
                            <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-600">{{ Str::limit($faq->answer, 80) }}</p>
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
                                    <a href="{{ route('organization.faqs.edit', $faq) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form id="delete-faq-{{ $faq->id }}" method="POST" action="{{ route('organization.faqs.destroy', $faq) }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button"
                                            onclick="return confirmDelete('delete-faq-{{ $faq->id }}', '{{ addslashes($faq->question) }}')"
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
                            No FAQs found. Create your first FAQ to get started.
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    const table = $('#faqsTable').DataTable({
        "pageLength": 10,
        "order": [[1, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 5] }
        ],
        "language": {
            "paginate": {
                "previous": "← Previous",
                "next": "Next →"
            },
            "info": "Showing _START_ to _END_ of _TOTAL_ FAQs",
            "infoEmpty": "No FAQs available",
            "zeroRecords": "No matching FAQs found"
        }
    });

    // Initialize SortableJS for drag-and-drop reordering
    const tbody = document.querySelector('#faqsTable tbody');

    if (tbody) {
        Sortable.create(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const order = [];
                document.querySelectorAll('#faqsTable tbody tr').forEach((row, index) => {
                    const id = row.dataset.id;
                    if (id) {
                        order.push({
                            id: id,
                            order: index + 1
                        });
                    }
                });

                // Send AJAX request to update order
                fetch('{{ route("organization.faqs.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        // Update the order numbers in the UI
                        document.querySelectorAll('#faqsTable tbody tr').forEach((row, index) => {
                            const orderSpan = row.querySelector('td:nth-child(2) span');
                            if (orderSpan) {
                                orderSpan.textContent = index + 1;
                            }
                        });
                    }
                })
                .catch(error => {
                    showToast('Failed to update order', 'error');
                    console.error('Error:', error);
                });
            }
        });
    }

    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.faq-checkbox').prop('checked', this.checked);
        updateBulkActionsBar();
    });

    // Individual checkbox change
    $('.faq-checkbox').on('change', function() {
        updateBulkActionsBar();
    });

    function updateBulkActionsBar() {
        const checkedCount = $('.faq-checkbox:checked').length;
        $('#selectedCount').text(checkedCount);

        if (checkedCount > 0) {
            $('#bulkActionsBar').removeClass('hidden');
        } else {
            $('#bulkActionsBar').addClass('hidden');
        }
    }
});

function clearSelection() {
    $('.faq-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
    $('#bulkActionsBar').addClass('hidden');
}

function bulkDelete() {
    const selectedIds = $('.faq-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        showToast('Please select at least one FAQ', 'warning');
        return;
    }

    showConfirmDialog(
        'Confirm Bulk Deletion',
        `Are you sure you want to delete ${selectedIds.length} FAQ(s)? This action cannot be undone.`,
        function() {
            $.ajax({
                url: '{{ route("organization.faqs.bulk-delete") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds
                },
                success: function(response) {
                    showToast(`Successfully deleted ${selectedIds.length} FAQ(s)`, 'success');
                    setTimeout(() => location.reload(), 1000);
                },
                error: function(xhr) {
                    showToast('Error deleting FAQs', 'error');
                }
            });
        },
        'Delete',
        'red'
    );
}
</script>
@endpush
