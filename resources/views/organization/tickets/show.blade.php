@extends('layouts.dashboard')

@section('title', 'Ticket Details')
@section('page-title', 'Ticket Details')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Support Tickets', 'url' => route('organization.tickets.index')],
    ['label' => 'View', 'url' => null]
]])
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $ticket->ticket_number }}</h2>
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        {{ $ticket->status == 'open' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $ticket->status == 'replied' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $ticket->status == 'closed' ? 'bg-gray-100 text-gray-700' : '' }}">
                        {{ ucfirst($ticket->status) }}
                    </span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        {{ $ticket->priority == 'low' ? 'bg-gray-100 text-gray-700' : '' }}
                        {{ $ticket->priority == 'medium' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $ticket->priority == 'high' ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $ticket->priority == 'urgent' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($ticket->priority) }} Priority
                    </span>
                    @if($ticket->category)
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                        {{ $ticket->category }}
                    </span>
                    @endif
                </div>
                <p class="text-lg text-gray-700 mb-2">{{ $ticket->subject }}</p>
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <span>Created: {{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                    @if($ticket->replied_at)
                    <span>Replied: {{ $ticket->replied_at->format('M d, Y h:i A') }}</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('organization.tickets.index') }}"
               class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                Back
            </a>
        </div>
    </div>

    <!-- Response Time Metrics -->
    @if($ticket->first_response_at || $ticket->resolved_at)
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">Response Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($ticket->first_response_at)
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-xs font-semibold text-gray-600 uppercase">First Response Time</span>
                </div>
                <p class="text-2xl font-bold text-blue-600">{{ $ticket->getFirstResponseTimeFormatted() }}</p>
                <p class="text-xs text-gray-500 mt-1">Responded at {{ $ticket->first_response_at->format('M d, Y h:i A') }}</p>
            </div>
            @endif

            @if($ticket->resolved_at)
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-xs font-semibold text-gray-600 uppercase">Resolution Time</span>
                </div>
                <p class="text-2xl font-bold text-green-600">{{ $ticket->getResolutionTimeFormatted() }}</p>
                <p class="text-xs text-gray-500 mt-1">Resolved at {{ $ticket->resolved_at->format('M d, Y h:i A') }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Member Info Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">Member Information</h3>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                <span class="text-sm font-bold text-white">{{ strtoupper(substr($ticket->member->name, 0, 2)) }}</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $ticket->member->name }}</p>
                <p class="text-sm text-gray-500">{{ $ticket->member->email }}</p>
                @if($ticket->member->phone)
                <p class="text-sm text-gray-500">{{ $ticket->member->phone }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Message Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">Message</h3>
        <div class="prose max-w-none">
            <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->message }}</p>
        </div>
    </div>
    <!-- Reply Section -->
    @if($ticket->reply)
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">Your Reply</h3>
        <div class="prose max-w-none">
            <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->reply }}</p>
        </div>
    </div>
    @endif

    <!-- Reply Form -->
    @if($ticket->status !== 'closed')
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6" x-data="{
        showTemplates: false,
        templates: [
            { name: 'Thank You', text: 'Thank you for contacting us. We have received your inquiry and will get back to you shortly.' },
            { name: 'Issue Resolved', text: 'We are pleased to inform you that your issue has been resolved. Please let us know if you need any further assistance.' },
            { name: 'Need More Info', text: 'Thank you for reaching out. To better assist you, could you please provide more details about your concern?' },
            { name: 'Under Investigation', text: 'We are currently investigating your issue and will update you as soon as we have more information.' }
        ],
        insertTemplate(text) {
            document.getElementById('reply').value = text;
            this.showTemplates = false;
        }
    }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700 uppercase">
                {{ $ticket->reply ? 'Update Reply' : 'Send Reply' }}
            </h3>
            <button type="button"
                    @click="showTemplates = !showTemplates"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                Quick Templates
            </button>
        </div>

        <!-- Quick Templates Dropdown -->
        <div x-show="showTemplates"
             x-cloak
             class="mb-4 bg-gray-50 rounded-lg p-3 border border-gray-200">
            <p class="text-xs font-semibold text-gray-600 uppercase mb-2">Select a template:</p>
            <div class="grid grid-cols-2 gap-2">
                <template x-for="template in templates" :key="template.name">
                    <button type="button"
                            @click="insertTemplate(template.text)"
                            class="text-left px-3 py-2 bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition text-sm">
                        <span x-text="template.name" class="font-medium text-gray-700"></span>
                    </button>
                </template>
            </div>
        </div>

        <form method="POST" action="{{ route('organization.tickets.update', $ticket) }}" id="replyForm">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="reply" class="block text-sm font-medium text-gray-700 mb-1">Reply Message</label>
                    <textarea name="reply"
                              id="reply"
                              rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('reply') border-red-500 @enderror"
                              required>{{ old('reply', $ticket->reply) }}</textarea>
                    @error('reply')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Ticket Status</label>
                        <select name="status"
                                id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="open" {{ old('status', $ticket->status) == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="replied" {{ old('status', $ticket->status) == 'replied' ? 'selected' : '' }}>Replied</option>
                            <option value="closed" {{ old('status', $ticket->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority"
                                id="priority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="low" {{ old('priority', $ticket->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $ticket->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $ticket->priority) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority', $ticket->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category"
                                id="category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Category</option>
                            <option value="General" {{ old('category', $ticket->category) == 'General' ? 'selected' : '' }}>General</option>
                            <option value="Payment" {{ old('category', $ticket->category) == 'Payment' ? 'selected' : '' }}>Payment</option>
                            <option value="Technical" {{ old('category', $ticket->category) == 'Technical' ? 'selected' : '' }}>Technical</option>
                            <option value="Account" {{ old('category', $ticket->category) == 'Account' ? 'selected' : '' }}>Account</option>
                            <option value="Other" {{ old('category', $ticket->category) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center gap-3">
                    <button type="button"
                            onclick="quickReply('replied')"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Send & Mark Replied
                    </button>
                    <button type="button"
                            onclick="quickReply('closed')"
                            class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Send & Close Ticket
                    </button>
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    {{ $ticket->reply ? 'Update Reply' : 'Send Reply' }}
                </button>
            </div>
        </form>
    </div>

    <script>
        function quickReply(status) {
            document.getElementById('status').value = status;
            document.getElementById('replyForm').submit();
        }
    </script>
    @endif
</div>
@endsection
