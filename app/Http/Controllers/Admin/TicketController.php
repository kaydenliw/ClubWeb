<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactTicket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function show(ContactTicket $ticket)
    {
        $ticket->load('member');

        return response()->json([
            'ticket_number' => $ticket->ticket_number,
            'member_name' => $ticket->member->name,
            'subject' => $ticket->subject,
            'message' => $ticket->message,
            'reply' => $ticket->reply,
            'status' => $ticket->status,
            'created_at' => $ticket->created_at->format('d M Y H:i'),
            'replied_at' => $ticket->replied_at ? $ticket->replied_at->format('d M Y H:i') : null,
        ]);
    }

    public function updateStatus(Request $request, ContactTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,replied,closed',
        ]);

        $ticket->update($validated);

        return response()->json(['success' => true]);
    }

    public function reply(Request $request, ContactTicket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $ticket->update([
            'reply' => $validated['message'],
            'replied_at' => now(),
            'status' => 'replied',
        ]);

        return response()->json(['success' => true]);
    }
}
