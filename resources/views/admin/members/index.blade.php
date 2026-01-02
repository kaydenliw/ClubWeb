@extends('layouts.dashboard')

@section('page-title', 'Members Management')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Members', 'url' => null]
]])
<div class="space-y-4">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manage Members</h3>
            <p class="text-sm text-gray-500 mt-1">View all members across organizations</p>
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
                <a href="{{ route('admin.members.export.csv') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export as CSV
                </a>
                <a href="{{ route('admin.members.export.excel') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Export as Excel
                </a>
                <a href="{{ route('admin.members.export.pdf') }}" onclick="showLoading()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Export as PDF
                </a>
            </div>
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
                <input type="text" name="search" placeholder="Search by name, email, or phone..."
                       value="{{ request('search') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select name="organization" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Organizations</option>
                @foreach($organizations as $org)
                <option value="{{ $org->id }}" {{ request('organization') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                @endforeach
            </select>
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
            @if(request()->hasAny(['search', 'organization', 'status', 'start_date', 'end_date']))
            <a href="{{ route('admin.members.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="membersTable" class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Organization</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Car Details</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Sync Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Joined</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($members as $member)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-sm font-bold text-white">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $member->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-900">{{ $member->organization->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $member->email }}</div>
                            <div class="text-xs text-gray-500">{{ $member->phone ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $member->car_brand ?? '-' }} {{ $member->car_model ?? '' }}</div>
                            <div class="text-xs text-gray-500">{{ $member->car_plate ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $member->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($member->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($member->synced_to_accounting)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Synced</span>
                                @if($member->accounting_sync_at)
                                <div class="text-xs text-gray-500 mt-1">{{ $member->accounting_sync_at->format('d M Y') }}</div>
                                @endif
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Not Synced</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ $member->created_at->format('d M Y') }}</span>
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
                                    <a href="{{ route('admin.members.show', $member) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                    <a href="{{ route('admin.members.edit', $member) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form id="delete-member-{{ $member->id }}" action="{{ route('admin.members.destroy', $member) }}" method="POST" style="display: none;">
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
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                            No members found.
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
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": 7 }
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
});
</script>
@endpush