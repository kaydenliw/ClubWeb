@extends('layouts.dashboard')

@section('title', 'Ticket Details')
@section('page-title', 'Ticket Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('organization.tickets.index') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Tickets
        </a>
    </div>

    <!-- Ticket Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $ticket->ticket_number }}</h1>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                        {{ $ticket->status == 'open' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $ticket->status == 'replied' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $ticket->status == 'closed' ? 'bg-gray-100 text-gray-700' : '' }}">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </div>
                <h2 class="text-lg text-gray-700 font-medium">{{ $ticket->subject }}</h2>
                <p class="text-sm text-gray-500 mt-1">Submitted {{ $ticket->created_at->format('M d, Y \a\t h:i A') }}</p>
            </div>

            @if($ticket->first_response_at)
            <div class="text-right">
                <p class="text-xs text-gray-500 uppercase font-semibold">Response Time</p>
                <p class="text-lg font-bold text-blue-600">{{ $ticket->getFirstResponseTimeFormatted() }}</p>
            </div>
            @endif
        </div>

        <!-- Member Info -->
        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                <span class="text-sm font-bold text-white">{{ strtoupper(substr($ticket->member->name, 0, 2)) }}</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $ticket->member->name }}</p>
                <p class="text-xs text-gray-500">{{ $ticket->member->email }} @if($ticket->member->phone)• {{ $ticket->member->phone }}@endif</p>
            </div>
        </div>
    </div>

    <!-- Conversation Thread -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700 uppercase">Conversation</h3>
        </div>

        <div class="p-6 space-y-6">
            <!-- Customer Message -->
            <div class="flex gap-4">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-white">{{ strtoupper(substr($ticket->member->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-sm font-semibold text-gray-900">{{ $ticket->member->name }}</span>
                        <span class="text-xs text-gray-500">{{ $ticket->created_at->format('M d, Y \a\t h:i A') }}</span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $ticket->message }}</p>
                    </div>
                </div>
            </div>

            <!-- Your Reply (if exists) -->
            @if($ticket->reply)
            <div class="flex gap-4">
                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-sm font-semibold text-gray-900">You</span>
                        <span class="text-xs text-gray-500">{{ $ticket->replied_at->format('M d, Y \a\t h:i A') }}</span>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $ticket->reply }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Reply Form (Only if not closed) -->
    @if($ticket->status !== 'closed')
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">
            {{ $ticket->reply ? 'Send Another Reply' : 'Reply to Customer' }}
        </h3>

        <form method="POST" action="{{ route('organization.tickets.update', $ticket) }}" id="replyForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="status" id="statusField" value="replied">
            <input type="hidden" name="priority" value="{{ $ticket->priority }}">
            <input type="hidden" name="category" value="{{ $ticket->category }}">

            <div class="space-y-4">
                <!-- Quick Templates -->
                <div x-data="{ showTemplates: false }">
                    <button type="button"
                            @click="showTemplates = !showTemplates"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                        Use Quick Template
                    </button>

                    <div x-show="showTemplates"
                         x-cloak
                         class="mb-4 grid grid-cols-2 gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <button type="button"
                                onclick="document.getElementById('reply').value = 'Thank you for contacting us. We have received your inquiry and will get back to you shortly.'"
                                class="text-left px-3 py-2 bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition text-sm">
                            <span class="font-medium text-gray-700">Thank You</span>
                        </button>
                        <button type="button"
                                onclick="document.getElementById('reply').value = 'We are pleased to inform you that your issue has been resolved. Please let us know if you need any further assistance.'"
                                class="text-left px-3 py-2 bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition text-sm">
                            <span class="font-medium text-gray-700">Issue Resolved</span>
                        </button>
                        <button type="button"
                                onclick="document.getElementById('reply').value = 'Thank you for reaching out. To better assist you, could you please provide more details about your concern?'"
                                class="text-left px-3 py-2 bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition text-sm">
                            <span class="font-medium text-gray-700">Need More Info</span>
                        </button>
                        <button type="button"
                                onclick="document.getElementById('reply').value = 'We are currently investigating your issue and will update you as soon as we have more information.'"
                                class="text-left px-3 py-2 bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition text-sm">
                            <span class="font-medium text-gray-700">Under Investigation</span>
                        </button>
                    </div>
                </div>

                <!-- Reply Textarea -->
                <div>
                    <textarea name="reply"
                              id="reply"
                              rows="6"
                              placeholder="Type your reply here..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('reply') border-red-500 @enderror"
                              required>{{ old('reply') }}</textarea>
                    @error('reply')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Customer will receive an email notification
                    </p>
                    <div class="flex items-center gap-3">
                        <button type="button"
                                onclick="document.getElementById('statusField').value='closed'; document.getElementById('replyForm').submit();"
                                class="px-5 py-2.5 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Resolve & Close
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send Reply
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @else
    <!-- Ticket Closed Message -->
    <div class="bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 p-8 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-700 mb-1">Ticket Closed</h3>
        <p class="text-sm text-gray-500">This ticket has been resolved and closed.</p>
        @if($ticket->resolved_at)
        <p class="text-xs text-gray-400 mt-2">Resolved in {{ $ticket->getResolutionTimeFormatted() }}</p>
        @endif
    </div>
    @endif
</div>
@endsection
