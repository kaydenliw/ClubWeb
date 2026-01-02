@extends('layouts.dashboard')

@section('page-title', 'Organization Details')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Organizations', 'url' => route('admin.organizations.index')],
    ['label' => 'View', 'url' => null]
]])
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $organization->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">Organization Details</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.organizations.edit', $organization) }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                Edit Organization
            </a>
            <a href="{{ route('admin.organizations.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Organization Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $organization->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $organization->phone ?? '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $organization->address ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</label>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $organization->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($organization->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Created Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $organization->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Bank Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bank Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $organization->bank_name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Account Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $organization->bank_account_number ?? '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Account Holder</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $organization->bank_account_holder ?? '-' }}</p>
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
                            <span class="text-sm text-gray-600">Total Members</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $organization->members->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Total Charges</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $organization->charges->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Total Transactions</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $organization->transactions->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-sm text-gray-600">Total Settlements</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $organization->settlements->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charges/Fees Section -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Charges & Fees</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($organization->charges as $charge)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $charge->title }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($charge->type) }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">RM {{ number_format($charge->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $charge->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($charge->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">No charges found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- FAQs Section -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">FAQs</h3>
        </div>
        <div class="p-6">
            @forelse($organization->faqs as $faq)
            <div class="mb-4 pb-4 border-b border-gray-200 last:border-0">
                <h4 class="text-sm font-semibold text-gray-900 mb-2">{{ $faq->question }}</h4>
                <p class="text-sm text-gray-600">{{ $faq->answer }}</p>
            </div>
            @empty
            <p class="text-sm text-gray-500 text-center py-4">No FAQs found</p>
            @endforelse
        </div>
    </div>

    <!-- Announcements Section -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Announcements</h3>
        </div>
        <div class="p-6">
            @forelse($organization->announcements as $announcement)
            <div class="mb-4 pb-4 border-b border-gray-200 last:border-0">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="text-sm font-semibold text-gray-900">{{ $announcement->title }}</h4>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $announcement->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $announcement->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
                <p class="text-sm text-gray-600 mb-2">{{ Str::limit($announcement->content, 150) }}</p>
                <p class="text-xs text-gray-500">{{ $announcement->created_at->format('d M Y') }}</p>
            </div>
            @empty
            <p class="text-sm text-gray-500 text-center py-4">No announcements found</p>
            @endforelse
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
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Member</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Subject</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Priority</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($organization->contactTickets as $ticket)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $ticket->ticket_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $ticket->member->name }}</td>
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
                            <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">No support tickets found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
