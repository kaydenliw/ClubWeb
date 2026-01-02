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
        .reply-box { background: #eff6ff; border-left: 4px solid #2563eb; padding: 15px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">Reply to Your Support Ticket</h1>
        </div>
        <div class="content">
            <p>Hello {{ $ticket->member->name }},</p>
            <p>{{ $ticket->organization->name }} has replied to your support ticket.</p>

            <div class="ticket-info">
                <div class="label">Ticket Number</div>
                <div class="value">{{ $ticket->ticket_number }}</div>

                <div class="label">Subject</div>
                <div class="value">{{ $ticket->subject }}</div>

                <div class="label">Status</div>
                <div class="value">{{ ucfirst($ticket->status) }}</div>
            </div>

            <div class="reply-box">
                <div class="label">Reply from {{ $ticket->organization->name }}</div>
                <div class="value" style="white-space: pre-wrap; margin-top: 10px;">{{ $ticket->reply }}</div>
            </div>

            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ route('organization.tickets.show', $ticket) }}"
                   style="background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    View Full Ticket
                </a>
            </p>
        </div>
        <div class="footer">
            <p>This is an automated notification from {{ $ticket->organization->name }}.</p>
        </div>
    </div>
</body>
</html>
