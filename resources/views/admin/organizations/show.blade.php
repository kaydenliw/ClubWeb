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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Organization Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $organization->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $organization->email }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $organization->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">PIC Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $organization->pic_name ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
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

                    <div class="mt-6">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $organization->address ?? '-' }}</p>
                    </div>

                    <div class="mt-6">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Logo</label>
                        @if($organization->logo)
                            <img src="{{ asset('storage/' . $organization->logo) }}" alt="Organization Logo" class="mt-1 h-20 w-auto object-contain border border-gray-200 rounded p-2">
                        @else
                            <p class="mt-1 text-sm text-gray-900">-</p>
                        @endif
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
                        <div class="py-3 border-b border-gray-100">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Number of Charges/Plans</span>
                                <span class="text-lg font-semibold text-gray-900">{{ $organization->charges->count() }}</span>
                            </div>
                        </div>
                        <div class="py-3 border-b border-gray-100">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Number of Transactions Last Month</span>
                                <span class="text-lg font-semibold text-gray-900">{{ $organization->transactions()->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Amount</span>
                                <span class="text-sm font-medium text-gray-900">RM {{ number_format($organization->transactions()->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->where('status', 'completed')->sum('amount'), 2) }}</span>
                            </div>
                        </div>
                        <div class="py-3">
                            @php
                                $lastSettlement = $organization->settlements()->latest()->first();
                            @endphp
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Last Settlement</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $lastSettlement ? $lastSettlement->created_at->format('d M Y h:i A') : '-' }}
                                </span>
                            </div>
                            @if($lastSettlement)
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Amount</span>
                                <span class="text-sm font-medium text-gray-900">RM {{ number_format($lastSettlement->amount ?? 0, 2) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charges & Plans Section -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Charges & Plans</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="chargesTable" class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Platform Fee</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Approval</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($organization->charges as $charge)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $charge->title }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($charge->type) }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">RM {{ number_format($charge->amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($charge->platform_fee_percentage || $charge->platform_fee_fixed)
                                    @if($charge->platform_fee_percentage)
                                        {{ $charge->platform_fee_percentage }}%
                                    @endif
                                    @if($charge->platform_fee_percentage && $charge->platform_fee_fixed)
                                        {{ strtoupper($charge->platform_fee_operator) }}
                                    @endif
                                    @if($charge->platform_fee_fixed)
                                        RM{{ number_format($charge->platform_fee_fixed, 2) }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $charge->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($charge->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $charge->approval_status == 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $charge->approval_status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $charge->approval_status == 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($charge->approval_status) }}
                                </span>
                                @if($charge->approval_status == 'rejected' && $charge->reject_reason)
                                    <p class="text-xs text-red-600 mt-1">{{ $charge->reject_reason }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="relative inline-block text-left">
                                    <button type="button" onclick="toggleDropdown(event, {{ $charge->id }})" class="inline-flex justify-center w-full px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">
                                        Actions
                                        <svg class="-mr-1 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div id="dropdown-{{ $charge->id }}" class="hidden fixed w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                        <div class="py-1">
                                            @if($charge->approval_status == 'pending')
                                                <a href="#" onclick="event.preventDefault(); approveCharge({{ $charge->id }})" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span class="text-green-600">✓ Approve</span>
                                                </a>
                                                <a href="#" onclick="event.preventDefault(); showRejectModal({{ $charge->id }})" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span class="text-red-600">✗ Reject</span>
                                                </a>
                                            @endif
                                            <a href="#" onclick="event.preventDefault(); editPlatformFee({{ $charge->id }})" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Edit Platform Fee
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">No charges found</td>
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
            @forelse($organization->faqs as $index => $faq)
            <div class="mb-4 border border-gray-200 rounded-lg overflow-hidden">
                <button onclick="toggleFaq({{ $index }})" class="w-full px-5 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition">
                    <span class="text-sm font-semibold text-gray-900 pr-4">{{ $faq->question }}</span>
                    <svg id="faq-icon-{{ $index }}" class="w-5 h-5 text-gray-500 transform transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="faq-content-{{ $index }}" class="hidden px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $faq->answer }}</p>
                </div>
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
            <div class="overflow-x-auto">
                <table id="announcementsTable" class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Content</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Created By</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Publish Date</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($organization->announcements as $announcement)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $announcement->title }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($announcement->content, 50) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $announcement->creator->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $announcement->publish_date ? $announcement->publish_date->format('d M y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-700',
                                        'pending_approval' => 'bg-yellow-100 text-yellow-700',
                                        'approved_pending_publish' => 'bg-blue-100 text-blue-700',
                                        'approved_published' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'pending_approval' => 'Pending Approve',
                                        'approved_pending_publish' => 'Approved & Pending Publish',
                                        'approved_published' => 'Approved & Published',
                                        'rejected' => 'Rejected by Admin',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$announcement->approval_status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $statusLabels[$announcement->approval_status] ?? ucfirst($announcement->approval_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="relative inline-block text-left">
                                    <button type="button" onclick="toggleDropdown(event, 'announcement-{{ $announcement->id }}')" class="inline-flex justify-center w-full px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">
                                        Actions
                                        <svg class="-mr-1 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div id="dropdown-announcement-{{ $announcement->id }}" class="hidden fixed w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                        <div class="py-1">
                                            <a href="{{ route('admin.announcements.show', $announcement) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                View Details
                                            </a>
                                            @if($announcement->approval_status == 'pending_approval')
                                                <a href="#" onclick="event.preventDefault(); approveAnnouncement({{ $announcement->id }})" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span class="text-green-600">✓ Approve</span>
                                                </a>
                                                <a href="#" onclick="event.preventDefault(); showRejectAnnouncementModal({{ $announcement->id }})" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span class="text-red-600">✗ Reject</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">No announcements found</td>
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
                <table id="supportTicketsTable" class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Ticket #</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Member</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Subject</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
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
                                    {{ $ticket->status == 'open' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $ticket->status == 'replied' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $ticket->status == 'closed' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $ticket->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <button onclick="viewTicketDetails({{ $ticket->id }})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Details
                                </button>
                            </td>
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

<!-- Announcement Details Modal -->
<div id="announcementModal" class="hidden" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7);" onclick="closeAnnouncementModal()"></div>
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px;">
        <div class="bg-white rounded-lg shadow-xl" onclick="event.stopPropagation()">
            <div class="bg-white px-6 py-4 border-b border-gray-200 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Announcement Details</h3>
                    <button onclick="closeAnnouncementModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4" style="max-height: 500px; overflow-y: auto;">
                <div id="announcementContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7);" onclick="closeRejectModal()"></div>
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 500px;">
        <div class="bg-white rounded-lg shadow-xl" onclick="event.stopPropagation()">
            <div class="bg-white px-6 py-4 border-b border-gray-200 rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-900">Reject Charge</h3>
            </div>
            <div class="px-6 py-4">
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reject Reason *</label>
                        <textarea name="reject_reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-md">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                            Reject Charge
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Platform Fee Modal -->
<div id="editFeeModal" class="hidden" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7);" onclick="closeEditFeeModal()"></div>
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 500px;">
        <div class="bg-white rounded-lg shadow-xl" onclick="event.stopPropagation()">
            <div class="bg-white px-6 py-4 border-b border-gray-200 rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-900">Edit Platform Fee</h3>
            </div>
            <div class="px-6 py-4">
                <form id="editFeeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Percentage (%)</label>
                        <input type="number" name="platform_fee_percentage" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Operator</label>
                        <select name="platform_fee_operator" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-</option>
                            <option value="and">AND</option>
                            <option value="or">OR</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fixed Amount (RM)</label>
                        <input type="number" name="platform_fee_fixed" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditFeeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-md">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Update Fee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Support Ticket Details Modal -->
<div id="ticketModal" class="hidden" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7);" onclick="closeTicketModal()"></div>
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 900px;">
        <div class="bg-white rounded-lg shadow-xl" onclick="event.stopPropagation()">
            <div class="bg-white px-6 py-4 border-b border-gray-200 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Support Ticket Details</h3>
                    <button onclick="closeTicketModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4 max-h-96 overflow-y-auto">
                <div id="ticketContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Announcement Modal -->
<div id="rejectAnnouncementModal" class="hidden" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7);" onclick="closeRejectAnnouncementModal()"></div>
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 500px;">
        <div class="bg-white rounded-lg shadow-xl" onclick="event.stopPropagation()">
            <div class="bg-white px-6 py-4 border-b border-gray-200 rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-900">Reject Announcement</h3>
            </div>
            <div class="px-6 py-4">
                <form id="rejectAnnouncementForm" onsubmit="submitRejectAnnouncement(event)">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reject Reason *</label>
                        <textarea id="announcementRejectReason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectAnnouncementModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-md">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                            Reject Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDropdown(event, chargeId) {
    event.stopPropagation();
    const dropdown = document.getElementById(`dropdown-${chargeId}`);
    const button = event.currentTarget;
    const rect = button.getBoundingClientRect();

    // Close all other dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
        if (d.id !== `dropdown-${chargeId}`) {
            d.classList.add('hidden');
        }
    });

    // Toggle current dropdown
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        // Position the dropdown
        dropdown.style.top = `${rect.bottom + 5}px`;
        dropdown.style.left = `${rect.left}px`;
    } else {
        dropdown.classList.add('hidden');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

function toggleFaq(index) {
    const content = document.getElementById(`faq-content-${index}`);
    const icon = document.getElementById(`faq-icon-${index}`);
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

function viewAnnouncementDetails(announcementId) {
    fetch(`/admin/announcements/${announcementId}`)
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('announcementContent');
            content.innerHTML = `
                <div class="space-y-4">
                    <div><strong>Title:</strong> ${data.title}</div>
                    <div><strong>Content:</strong><br>${data.content}</div>
                    <div><strong>Created By:</strong> ${data.creator || '-'}</div>
                    <div><strong>Publish Date:</strong> ${data.publish_date || '-'}</div>
                    <div><strong>Status:</strong> ${data.status}</div>
                    ${data.reject_reason ? `<div class="text-red-600"><strong>Reject Reason:</strong> ${data.reject_reason}</div>` : ''}
                    <div class="flex space-x-3 mt-6">
                        ${data.approval_status === 'pending_approval' ? `
                            <button onclick="approveAnnouncement(${announcementId})" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Approve</button>
                            <button onclick="showRejectAnnouncementModal(${announcementId})" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                        ` : ''}
                    </div>
                </div>
            `;
            document.getElementById('announcementModal').classList.remove('hidden');
        });
}

function closeAnnouncementModal() {
    document.getElementById('announcementModal').classList.add('hidden');
}

function approveCharge(chargeId) {
    showConfirmModal(
        'Approve Charge',
        'Are you sure you want to approve this charge?',
        'Approve',
        () => {
            fetch(`/admin/charges/${chargeId}/approve`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Charge approved successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Error approving charge', 'error');
                }
            })
            .catch(() => {
                showNotification('An error occurred', 'error');
            });
        }
    );
}

function showRejectModal(chargeId) {
    // Close all dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        dropdown.classList.add('hidden');
    });
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectForm').action = `/admin/charges/${chargeId}/reject`;
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function editPlatformFee(chargeId) {
    // Close all dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        dropdown.classList.add('hidden');
    });
    document.getElementById('editFeeModal').classList.remove('hidden');
    document.getElementById('editFeeForm').action = `/admin/charges/${chargeId}/update-fee`;
}

function closeEditFeeModal() {
    document.getElementById('editFeeModal').classList.add('hidden');
}

function approveAnnouncement(announcementId) {
    // Close all dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        dropdown.classList.add('hidden');
    });

    showConfirmModal(
        'Approve Announcement',
        'Are you sure you want to approve this announcement?',
        'Approve',
        () => {
            fetch(`/admin/announcements/${announcementId}/approve`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Announcement approved successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Failed to approve announcement', 'error');
                }
            })
            .catch(() => {
                showNotification('An error occurred', 'error');
            });
        }
    );
}

let currentAnnouncementId = null;

function showRejectAnnouncementModal(announcementId) {
    // Close all dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        dropdown.classList.add('hidden');
    });

    currentAnnouncementId = announcementId;
    document.getElementById('rejectAnnouncementModal').classList.remove('hidden');
    document.getElementById('announcementRejectReason').value = '';
}

function closeRejectAnnouncementModal() {
    document.getElementById('rejectAnnouncementModal').classList.add('hidden');
    currentAnnouncementId = null;
}

function submitRejectAnnouncement(event) {
    event.preventDefault();
    const reason = document.getElementById('announcementRejectReason').value;
    const button = event.target.querySelector('button[type="submit"]');

    button.disabled = true;
    button.classList.add('opacity-50', 'cursor-not-allowed');
    button.textContent = 'Rejecting...';

    fetch(`/admin/announcements/${currentAnnouncementId}/reject`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ reject_reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Announcement rejected successfully!', 'success');
            closeRejectAnnouncementModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Failed to reject announcement', 'error');
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            button.textContent = 'Reject Announcement';
        }
    })
    .catch(error => {
        showNotification('An error occurred', 'error');
        button.disabled = false;
        button.classList.remove('opacity-50', 'cursor-not-allowed');
        button.textContent = 'Reject Announcement';
    });
}

function viewTicketDetails(ticketId) {
    fetch(`/admin/tickets/${ticketId}`)
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('ticketContent');

            content.innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4 pb-4 border-b">
                        <div><strong class="text-gray-700">Ticket #:</strong> <span class="text-gray-900">${data.ticket_number}</span></div>
                        <div><strong class="text-gray-700">Member:</strong> <span class="text-gray-900">${data.member_name}</span></div>
                        <div class="col-span-2"><strong class="text-gray-700">Subject:</strong> <span class="text-gray-900">${data.subject}</span></div>
                        <div class="col-span-2">
                            <strong class="text-gray-700">Status:</strong>
                            <select id="ticketStatus" class="ml-2 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="open" ${data.status === 'open' ? 'selected' : ''}>Open</option>
                                <option value="replied" ${data.status === 'replied' ? 'selected' : ''}>Replied</option>
                                <option value="closed" ${data.status === 'closed' ? 'selected' : ''}>Closed</option>
                            </select>
                            <button onclick="updateTicketStatus(${ticketId}, event)" class="ml-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">Update Status</button>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-xl">
                        <h4 class="font-semibold text-gray-900 mb-2">Member's Message</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">${data.message}</p>
                        <p class="text-xs text-gray-500 mt-2">Sent: ${data.created_at}</p>
                    </div>

                    ${data.reply ? `
                        <div class="bg-green-50 p-4 rounded-xl">
                            <h4 class="font-semibold text-gray-900 mb-2">Admin Reply</h4>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">${data.reply}</p>
                            <p class="text-xs text-gray-500 mt-2">Replied: ${data.replied_at}</p>
                        </div>
                    ` : ''}

                    <div class="pt-4 border-t">
                        <h4 class="font-semibold text-gray-900 mb-3">Send Reply</h4>
                        <textarea id="replyMessage" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Type your reply..."></textarea>
                        <button onclick="sendReply(${ticketId}, event)" class="mt-3 px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Send Reply</button>
                    </div>
                </div>
            `;
            document.getElementById('ticketModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to load ticket details', 'error');
        });
}

function closeTicketModal() {
    document.getElementById('ticketModal').classList.add('hidden');
}

function updateTicketStatus(ticketId, event) {
    const status = document.getElementById('ticketStatus').value;
    const button = event.target;
    button.disabled = true;
    button.classList.add('opacity-50', 'cursor-not-allowed');
    button.textContent = 'Updating...';

    fetch(`/admin/tickets/${ticketId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Status updated successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Failed to update status', 'error');
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            button.textContent = 'Update Status';
        }
    })
    .catch(error => {
        showNotification('An error occurred', 'error');
        button.disabled = false;
        button.classList.remove('opacity-50', 'cursor-not-allowed');
        button.textContent = 'Update Status';
    });
}

function sendReply(ticketId, event) {
    const message = document.getElementById('replyMessage').value;
    if (!message) {
        showNotification('Please enter a message', 'error');
        return;
    }

    const button = event.target;
    button.disabled = true;
    button.classList.add('opacity-50', 'cursor-not-allowed');
    button.textContent = 'Sending...';

    fetch(`/admin/tickets/${ticketId}/reply`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Reply sent successfully!', 'success');
            document.getElementById('replyMessage').value = '';
            setTimeout(() => viewTicketDetails(ticketId), 1000);
        } else {
            showNotification('Failed to send reply', 'error');
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            button.textContent = 'Send Reply';
        }
    })
    .catch(error => {
        showNotification('An error occurred', 'error');
        button.disabled = false;
        button.classList.remove('opacity-50', 'cursor-not-allowed');
        button.textContent = 'Send Reply';
    });
}

// Notification helper function (use the global showToast from layout)
function showNotification(message, type) {
    if (typeof showToast === 'function') {
        showToast(message, type);
    } else {
        // Fallback if showToast is not available
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-2xl text-white font-semibold z-[9999] ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
        notification.style.minWidth = '250px';
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }
}

// Custom confirmation modal
function showConfirmModal(title, message, confirmText, onConfirm) {
    const modal = document.createElement('div');
    modal.id = 'customConfirmModal';
    modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;';
    modal.innerHTML = `
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7);" onclick="closeConfirmModal()"></div>
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 400px;">
            <div class="bg-white rounded-lg shadow-xl" onclick="event.stopPropagation()">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-600">${message}</p>
                </div>
                <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                    <button onclick="closeConfirmModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-md transition">
                        Cancel
                    </button>
                    <button onclick="confirmAction()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition">
                        ${confirmText}
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    window.confirmAction = function() {
        closeConfirmModal();
        onConfirm();
    };
}

function closeConfirmModal() {
    const modal = document.getElementById('customConfirmModal');
    if (modal) {
        modal.remove();
    }
}

// Initialize DataTables
$(document).ready(function() {
    $('#chargesTable').DataTable({
        "pageLength": 10,
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [6] }
        ],
        "scrollX": false,
        "autoWidth": false
    });

    $('#announcementsTable').DataTable({
        "pageLength": 10,
        "order": [[3, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [5] }
        ],
        "scrollX": false,
        "autoWidth": false
    });

    $('#supportTicketsTable').DataTable({
        "pageLength": 10,
        "order": [[4, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [5] }
        ],
        "scrollX": false,
        "autoWidth": false
    });
});

</script>
@endsection
