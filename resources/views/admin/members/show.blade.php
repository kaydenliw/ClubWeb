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
            <p class="text-sm text-gray-500 mt-1">Member ID: {{ $member->id }}</p>
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
            <!-- Member Information -->
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
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</label>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $member->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member Of Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Member Of</h3>
                </div>
                <div class="p-6">
                    @if($member->organizations->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($member->organizations as $org)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="text-lg font-bold text-white">{{ strtoupper(substr($org->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">{{ $org->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ ucfirst($org->pivot->role) }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $org->pivot->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($org->pivot->status) }}
                                    </span>
                                </div>
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Joined:</span>
                                        <span class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($org->pivot->joined_at)->format('d M Y') }}</span>
                                    </div>
                                    @if($org->pivot->membership_number)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Member ID:</span>
                                        <span class="text-gray-900 font-medium">{{ $org->pivot->membership_number }}</span>
                                    </div>
                                    @endif

                                    {{-- Type-specific details based on organization type --}}
                                    @if($org->organizationType && isset($org->details))
                                        <div class="mt-2 pt-2 border-t border-gray-100">
                                            @if($org->organizationType->slug === 'car_club')
                                                <p class="text-gray-500 font-medium mb-1">🚗 Vehicle Details:</p>
                                                @if($org->details->car_brand || $org->details->car_model)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Vehicle:</span>
                                                    <span class="text-gray-900 font-medium">{{ $org->details->car_brand }} {{ $org->details->car_model }}</span>
                                                </div>
                                                @endif
                                                @if($org->details->car_plate)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Plate:</span>
                                                    <span class="text-gray-900 font-medium">{{ $org->details->car_plate }}</span>
                                                </div>
                                                @endif
                                                @if($org->details->car_color)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Color:</span>
                                                    <span class="text-gray-900 font-medium">{{ $org->details->car_color }}</span>
                                                </div>
                                                @endif
                                                @if($org->details->car_year)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Year:</span>
                                                    <span class="text-gray-900 font-medium">{{ $org->details->car_year }}</span>
                                                </div>
                                                @endif
                                            @endif

                                            @if($org->organizationType->slug === 'residential_club')
                                                <p class="text-gray-500 font-medium mb-1">🏠 Residential Details:</p>
                                                @if($org->details->unit_number)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Unit:</span>
                                                    <span class="text-gray-900 font-medium">{{ $org->details->unit_number }}</span>
                                                </div>
                                                @endif
                                                @if($org->details->block)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Block:</span>
                                                    <span class="text-gray-900 font-medium">{{ $org->details->block }}</span>
                                                </div>
                                                @endif
                                                @if($org->details->address_line_1)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Address:</span>
                                                    <span class="text-gray-900 font-medium">{{ $org->details->address_line_1 }}</span>
                                                </div>
                                                @endif
                                            @endif

                                            @if($org->organizationType->slug === 'sports_club')
                                                <p class="text-gray-500 font-medium mb-1">⚽ Sports Club Details:</p>
                                                @if($org->details->emergency_contact_name)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Emergency Contact:</span>
                                                    <span class="text-gray-900 font-medium">{{ $org->details->emergency_contact_name }}</span>
                                                </div>
                                                @endif
                                                @if($org->details->emergency_contact_phone)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Contact Phone:</span>
                                                    <span class="text-gray-900 font-medium">{{ $org->details->emergency_contact_phone }}</span>
                                                </div>
                                                @endif
                                                @if($org->details->blood_type)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Blood Type:</span>
                                                    <span class="text-gray-900 font-medium">{{ $org->details->blood_type }}</span>
                                                </div>
                                                @endif
                                                @if($org->details->preferred_sports)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Preferred Sports:</span>
                                                    <span class="text-gray-900 font-medium">{{ $org->details->preferred_sports }}</span>
                                                </div>
                                                @endif
                                                @if($org->details->medical_conditions)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Medical Notes:</span>
                                                    <span class="text-gray-900 font-medium text-right">{{ $org->details->medical_conditions }}</span>
                                                </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endif

                                    @if($org->pivot->notes)
                                    <div class="mt-2 pt-2 border-t border-gray-100">
                                        <p class="text-gray-600 italic">{{ $org->pivot->notes }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Not a member of any organization yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Statistics -->
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
        </div>
    </div>

    <!-- Recent Transactions -->
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

    <!-- Support Tickets -->
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
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($member->contactTickets->take(10) as $ticket)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $ticket->ticket_number }}</td>
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
                            <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">No support tickets found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
@endsection

@push('scripts')
<script>
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
                        <div class="col-span-2"><strong class="text-gray-700">Created:</strong> <span class="text-gray-900">${data.created_at}</span></div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <strong class="text-gray-700 block mb-2">Message:</strong>
                            <div class="bg-gray-50 p-4 rounded-lg text-gray-900">${data.message}</div>
                        </div>

                        ${data.reply ? `
                            <div>
                                <strong class="text-gray-700 block mb-2">Reply:</strong>
                                <div class="bg-blue-50 p-4 rounded-lg text-gray-900">${data.reply}</div>
                                <div class="text-xs text-gray-500 mt-2">Replied at: ${data.replied_at || '-'}</div>
                            </div>
                        ` : ''}

                        <div>
                            <strong class="text-gray-700 block mb-2">Send Reply:</strong>
                            <textarea id="replyMessage" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Type your reply here..."></textarea>
                            <button onclick="sendTicketReply(${ticketId}, event)" class="mt-2 px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Send Reply</button>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('ticketModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to load ticket details', 'error');
        });
}

function closeTicketModal() {
    document.getElementById('ticketModal').classList.add('hidden');
}

function updateTicketStatus(ticketId, event) {
    const button = event.target;
    const status = document.getElementById('ticketStatus').value;

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
            showToast('Status updated successfully!', 'success');
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            button.textContent = 'Update Status';
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Failed to update status', 'error');
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            button.textContent = 'Update Status';
        }
    })
    .catch(error => {
        showToast('An error occurred', 'error');
        button.disabled = false;
        button.classList.remove('opacity-50', 'cursor-not-allowed');
        button.textContent = 'Update Status';
    });
}

function sendTicketReply(ticketId, event) {
    const button = event.target;
    const message = document.getElementById('replyMessage').value;

    if (!message.trim()) {
        showToast('Please enter a reply message', 'warning');
        return;
    }

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
            showToast('Reply sent successfully!', 'success');
            document.getElementById('replyMessage').value = '';
            setTimeout(() => viewTicketDetails(ticketId), 1000);
        } else {
            showToast('Failed to send reply', 'error');
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            button.textContent = 'Send Reply';
        }
    })
    .catch(error => {
        showToast('An error occurred', 'error');
        button.disabled = false;
        button.classList.remove('opacity-50', 'cursor-not-allowed');
        button.textContent = 'Send Reply';
    });
}
</script>
@endpush
