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

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

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
            <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="Start Date"
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="End Date"
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Apply
            </button>
            @if(request()->hasAny(['organization', 'status', 'start_date', 'end_date']))
            <a href="{{ route('admin.settlements.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Settlements Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="settlementsTable" class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Settlement #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Organization</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Settlement Date</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Notes</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($settlements as $settlement)
                    <tr class="hover:bg-gray-50 transition">
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
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ $settlement->notes ?? '-' }}</span>
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
                                    <a href="{{ route('admin.settlements.show', $settlement) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                    <a href="{{ route('admin.settlements.edit', $settlement) }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form id="delete-settlement-{{ $settlement->id }}" action="{{ route('admin.settlements.destroy', $settlement) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button"
                                            onclick="return confirmDelete('delete-settlement-{{ $settlement->id }}', '{{ addslashes($settlement->settlement_number) }}')"
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
            { "orderable": false, "targets": 6 }
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