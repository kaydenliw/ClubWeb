<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\ContactTicket;
use App\Mail\TicketReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactTicket::where('organization_id', auth()->user()->organization_id)
            ->with('member');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('ticket_number', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($mq) use ($request) {
                      $mq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tickets = $query->latest()->get();

        return view('organization.tickets.index', compact('tickets'));
    }

    public function show(ContactTicket $ticket)
    {
        if ($ticket->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $ticket->load('member');

        return view('organization.tickets.show-new', compact('ticket'));
    }

    public function update(Request $request, ContactTicket $ticket)
    {
        if ($ticket->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $validated = $request->validate([
            'reply' => 'required|string',
            'status' => 'required|in:open,replied,closed',
        ]);

        // Keep existing priority and category
        $validated['priority'] = $request->input('priority', $ticket->priority);
        $validated['category'] = $request->input('category', $ticket->category);
        $validated['replied_at'] = now();

        // Track first response time
        if (!$ticket->first_response_at) {
            $validated['first_response_at'] = now();
            $validated['first_response_time_minutes'] = $ticket->created_at->diffInMinutes(now());
        }

        // Track resolution time if ticket is being closed
        if ($validated['status'] === 'closed' && !$ticket->resolved_at) {
            $validated['resolved_at'] = now();
            $validated['resolution_time_minutes'] = $ticket->created_at->diffInMinutes(now());
        }

        $ticket->update($validated);

        // Send email notification to member
        try {
            Mail::to($ticket->member->email)->send(new TicketReplyNotification($ticket));
        } catch (\Exception $e) {
            \Log::error('Failed to send ticket reply email: ' . $e->getMessage());
        }

        $message = $validated['status'] === 'closed'
            ? 'Ticket has been resolved and closed. Customer has been notified via email.'
            : 'Reply sent successfully! Customer has been notified via email.';

        return redirect()->route('organization.tickets.show', $ticket)
            ->with('success', $message);
    }
}
