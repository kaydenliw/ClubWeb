@extends('layouts.dashboard')

@section('page-title', 'Member Details')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Members', 'url' => route('admin.members.index')],
    ['label' => 'View', 'url' => null]
]])
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $member->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">Member Details</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.members.edit', $member) }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                Edit Member
            </a>
            <form id="delete-member-form" action="{{ route('admin.members.destroy', $member) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="button" onclick="return confirmDelete('delete-member-form', '{{ addslashes($member->name) }}')" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                    Delete Member
                </button>
            </form>
            <a href="{{ route('admin.members.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Member Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Organization</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->organization->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</label>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $member->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Joined Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Car Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Car Brand</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->car_brand ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Car Model</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->car_model ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Car Plate</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->car_plate ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Statistics</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Total Transactions</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $member->transactions->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Total Charges</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $member->charges->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-sm text-gray-600">Support Tickets</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $member->contactTickets->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Sync Status</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Accounting Sync</label>
                            <p class="mt-1">
                                @if($member->synced_to_accounting)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Synced</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Not Synced</span>
                                @endif
                            </p>
                        </div>
                        @if($member->accounting_sync_at)
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Last Synced</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->accounting_sync_at->format('d M Y, h:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Section -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Recent Transactions</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Transaction #</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($member->transactions->take(10) as $txn)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $txn->transaction_number ?? '#' . $txn->id }}</td>
                            <td class="px-4 py-3 text-sm font-semibold {{ $txn->type == 'payment' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $txn->type == 'payment' ? '+' : '-' }}RM {{ number_format(abs($txn->amount), 2) }}
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
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $txn->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">No transactions found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Support Tickets Section -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Support Tickets</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Ticket #</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Subject</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Priority</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($member->contactTickets->take(10) as $ticket)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $ticket->ticket_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($ticket->subject, 40) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $ticket->priority == 'low' ? 'bg-gray-100 text-gray-700' : '' }}
                                    {{ $ticket->priority == 'medium' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $ticket->priority == 'high' ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $ticket->priority == 'urgent' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $ticket->status == 'open' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $ticket->status == 'replied' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $ticket->status == 'closed' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $ticket->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">No support tickets found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
