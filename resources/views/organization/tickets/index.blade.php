@extends('layouts.dashboard')

@section('title', 'Support Tickets')
@section('page-title', 'Support Tickets')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Support Tickets', 'url' => null]
]])
<div class="space-y-4">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Support Tickets</h3>
            <p class="text-sm text-gray-500 mt-1">View and manage member support tickets</p>
        </div>
    </div>

    <!-- Response Time Stats -->
    @if($tickets->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-xs font-semibold text-gray-600 uppercase">Avg First Response</span>
            </div>
            <p class="text-2xl font-bold text-blue-600">
                @php
                    $avgFirstResponse = $tickets->whereNotNull('first_response_time_minutes')->avg('first_response_time_minutes');
                    if ($avgFirstResponse) {
                        $hours = floor($avgFirstResponse / 60);
                        $minutes = round($avgFirstResponse % 60);
                        echo $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                    } else {
                        echo 'N/A';
                    }
                @endphp
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-xs font-semibold text-gray-600 uppercase">Avg Resolution Time</span>
            </div>
            <p class="text-2xl font-bold text-green-600">
                @php
                    $avgResolution = $tickets->whereNotNull('resolution_time_minutes')->avg('resolution_time_minutes');
                    if ($avgResolution) {
                        $hours = floor($avgResolution / 60);
                        $minutes = round($avgResolution % 60);
                        if ($hours > 24) {
                            $days = floor($hours / 24);
                            $remainingHours = $hours % 24;
                            echo "{$days}d {$remainingHours}h";
                        } else {
                            echo $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                        }
                    } else {
                        echo 'N/A';
                    }
                @endphp
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span class="text-xs font-semibold text-gray-600 uppercase">Total Tickets</span>
            </div>
            <p class="text-2xl font-bold text-purple-600">{{ $tickets->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">
                {{ $tickets->where('status', 'open')->count() }} open,
                {{ $tickets->where('status', 'closed')->count() }} closed
            </p>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('organization.tickets.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by ticket number, subject, or member..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <select name="category" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Categories</option>
                <option value="General" {{ request('category') == 'General' ? 'selected' : '' }}>General</option>
                <option value="Payment" {{ request('category') == 'Payment' ? 'selected' : '' }}>Payment</option>
                <option value="Technical" {{ request('category') == 'Technical' ? 'selected' : '' }}>Technical</option>
                <option value="Account" {{ request('category') == 'Account' ? 'selected' : '' }}>Account</option>
                <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Apply
            </button>
            @if(request()->hasAny(['search', 'status', 'category']))
            <a href="{{ route('organization.tickets.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Tickets List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="ticketsTable" class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ticket #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <span class="text-sm font-medium text-gray-900">{{ $ticket->ticket_number }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $ticket->member->name }}</p>
                                <p class="text-xs text-gray-500">{{ $ticket->member->email }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-900">{{ Str::limit($ticket->subject, 50) }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ $ticket->category ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $ticket->status == 'open' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $ticket->status == 'replied' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $ticket->status == 'closed' ? 'bg-gray-100 text-gray-700' : '' }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ $ticket->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('organization.tickets.show', $ticket) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                Reply
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                            No support tickets found.
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
    @if($tickets->count() > 0)
    $('#ticketsTable').DataTable({
        "pageLength": 15,
        "order": [[5, "desc"]],
        "columns": [
            null, // Ticket #
            null, // Member
            null, // Subject
            null, // Category
            null, // Status
            null, // Created
            null  // Actions
        ],
        "columnDefs": [
            { "orderable": false, "targets": 6 }
        ],
        "language": {
            "paginate": {
                "previous": "← Previous",
                "next": "Next →"
            },
            "info": "Showing _START_ to _END_ of _TOTAL_ tickets",
            "infoEmpty": "No tickets available",
            "zeroRecords": "No matching tickets found"
        }
    });
    @endif
});
</script>
@endpush
