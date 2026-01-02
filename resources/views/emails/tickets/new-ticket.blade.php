<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .ticket-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .label { font-weight: bold; color: #6b7280; font-size: 12px; text-transform: uppercase; }
        .value { color: #111827; margin-top: 5px; margin-bottom: 15px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge-urgent { background: #fee2e2; color: #991b1b; }
        .badge-high { background: #fed7aa; color: #9a3412; }
        .badge-medium { background: #dbeafe; color: #1e40af; }
        .badge-low { background: #f3f4f6; color: #374151; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">New Support Ticket</h1>
        </div>
        <div class="content">
            <p>Hello {{ $ticket->organization->name }},</p>
            <p>A new support ticket has been submitted by a member.</p>

            <div class="ticket-info">
                <div class="label">Ticket Number</div>
                <div class="value">{{ $ticket->ticket_number }}</div>

                <div class="label">Member</div>
                <div class="value">{{ $ticket->member->name }} ({{ $ticket->member->email }})</div>

                <div class="label">Subject</div>
                <div class="value">{{ $ticket->subject }}</div>

                <div class="label">Priority</div>
                <div class="value">
                    <span class="badge badge-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                </div>

                @if($ticket->category)
                <div class="label">Category</div>
                <div class="value">{{ $ticket->category }}</div>
                @endif

                <div class="label">Message</div>
                <div class="value" style="white-space: pre-wrap;">{{ $ticket->message }}</div>
            </div>

            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ route('organization.tickets.show', $ticket) }}"
                   style="background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    View Ticket
                </a>
            </p>
        </div>
        <div class="footer">
            <p>This is an automated notification from your support system.</p>
        </div>
    </div>
</body>
</html>
