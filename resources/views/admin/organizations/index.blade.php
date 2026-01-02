@extends('layouts.dashboard')

@section('page-title', 'Organizations')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Organizations', 'url' => null]
]])
<div class="space-y-4">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manage Organizations</h3>
            <p class="text-sm text-gray-500 mt-1">Manage car club organizations</p>
        </div>
        <div class="flex items-center gap-2">
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
                    <a href="{{ route('admin.organizations.export.csv') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export as CSV
                    </a>
                    <a href="{{ route('admin.organizations.export.excel') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Export as Excel
                    </a>
                    <a href="{{ route('admin.organizations.export.pdf') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Export as PDF
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.organizations.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                + Add Organization
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by name or email..."
                       value="{{ request('search') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="Start Date"
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="End Date"
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Apply
            </button>
            @if(request()->hasAny(['search', 'status', 'start_date', 'end_date']))
            <a href="{{ route('admin.organizations.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Organizations Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="organizationsTable" class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Organization</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Bank Details</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Members</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Transactions</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($organizations as $org)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-sm font-bold text-gray-600">{{ strtoupper(substr($org->name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $org->name }}</div>
                                    <div class="text-xs text-gray-500">Created {{ $org->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $org->email }}</div>
                            <div class="text-xs text-gray-500">{{ $org->phone ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $org->bank_name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $org->bank_account_number ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-semibold text-gray-900">{{ $org->members_count }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-semibold text-gray-900">{{ $org->transactions_count }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $org->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($org->status) }}
                            </span>
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
                                    <a href="{{ route('admin.organizations.show', $org) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                    <a href="{{ route('admin.organizations.edit', $org) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form id="delete-org-{{ $org->id }}" action="{{ route('admin.organizations.destroy', $org) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button"
                                            onclick="return confirmDelete('delete-org-{{ $org->id }}', '{{ addslashes($org->name) }}')"
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
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <p>No organizations found</p>
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
    $('#organizationsTable').DataTable({
        "pageLength": 15,
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": 6 }
        ],
        "language": {
            "paginate": {
                "previous": "← Previous",
                "next": "Next →"
            },
            "info": "Showing _START_ to _END_ of _TOTAL_ organizations",
            "infoEmpty": "No organizations available",
            "zeroRecords": "No matching organizations found"
        }
    });
});
</script>
@endpush
